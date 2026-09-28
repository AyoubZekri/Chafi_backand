<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * ترجمة تسمية المواد ونصها من العربية إلى الإنجليزية (label_en / text_en)
 * عبر Google Translate المجاني.
 *
 * الرابط المجاني يحظر عنوان السيرفر مؤقتاً بعد بضع مئات من الطلبات (HTTP 429)،
 * لذلك الأمر:
 *  - يجمع عدة مواد قصيرة في طلب واحد (حوالي 10 مرات طلبات أقل)
 *  - يبدّل بين رابطين إذا حُظر أحدهما
 *  - عند الحظر ينتظر (1 ← 2 ← 5 دقائق) ثم يتوقف برسالة واضحة إذا استمر الحظر
 *  - يحفظ كل مادة فور ترجمتها، وإعادة تشغيله تكمل من حيث توقف
 * علامات الجداول مثل [[جدول:1]] تبقى كما هي في النص المترجم.
 *
 * أمثلة:
 *   php artisan articles:translate                  كل المواد غير المترجمة
 *   php artisan articles:translate --document=2     مواد ملف واحد
 *   php artisan articles:translate --limit=20       تجربة على 20 مادة
 *   php artisan articles:translate --force          إعادة ترجمة المترجم أيضاً
 *   php artisan articles:translate --dry-run        عرض النتيجة بدون حفظ
 */
class TranslateArticles extends Command
{
    protected $signature = 'articles:translate
        {--document= : ترجمة مواد ملف واحد فقط (documents.id)}
        {--limit= : أقصى عدد من المواد}
        {--force : إعادة ترجمة المواد التي لها ترجمة مسبقاً}
        {--dry-run : عرض الترجمة بدون حفظها}
        {--sleep=1000 : انتظار بين الطلبات بالميلي ثانية}';

    protected $description = 'Translate articles label and text from Arabic to English (label_en, text_en)';

    private const ENDPOINTS = [
        'https://clients5.google.com/translate_a/t?client=dict-chrome-ex&sl=ar&tl=en',
        'https://translate.googleapis.com/translate_a/single?client=gtx&sl=ar&tl=en&dt=t',
    ];

    // أقصى طول لطلب واحد (Google يرفض النصوص الأطول)
    private const MAX_CHUNK = 4500;

    // مدة الانتظار عند الحظر بالثواني، ثم التوقف
    private const BLOCK_WAITS = [60, 120, 300];

    // رمز الاستثناء عند حظر Google
    private const BLOCKED = 429;

    private int $endpoint = 0;
    private int $requests = 0;

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $dryRun = (bool) $this->option('dry-run');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        $query = Article::query()->orderBy('id');
        if ($this->option('document')) {
            $query->where('document_id', $this->option('document'));
        }
        if (!$force) {
            $query->where(function ($q) {
                $q->whereNull('label_en')->orWhere('label_en', '')
                  ->orWhereNull('text_en')->orWhere('text_en', '');
            });
        }

        $ids = $query->pluck('id');
        if ($limit) {
            $ids = $ids->take($limit);
        }
        if ($ids->isEmpty()) {
            $this->info('لا توجد مواد تحتاج ترجمة.');
            return self::SUCCESS;
        }
        $this->info("المواد المعنية: {$ids->count()}" . ($dryRun ? ' (تجربة بدون حفظ)' : ''));

        $bar = $this->output->createProgressBar($ids->count());
        $bar->start();
        $done = 0;

        try {
            foreach ($ids->chunk(50) as $chunkIds) {
                $articles = Article::whereIn('id', $chunkIds)->orderBy('id')->get();

                // 1) التسميات: بالقاعدة الثابتة، وتُحفظ حتى لو فشلت ترجمة النص لاحقاً
                foreach ($articles as $article) {
                    if ($force || empty($article->label_en)) {
                        $article->label_en = $this->translateLabel((string) $article->label);
                        $this->persist($article, $dryRun);
                    }
                }

                // 2) النصوص: مجمّعة في طلبات قليلة
                $pending = $articles->filter(fn ($a) =>
                    ($force || empty($a->text_en)) && trim((string) $a->text) !== '');
                foreach ($this->batches($pending) as $batch) {
                    foreach ($this->translateBatch($batch) as $id => $english) {
                        $article = $batch[$id];
                        $article->text_en = $english;
                        $this->persist($article, $dryRun);
                        if ($dryRun) {
                            $bar->clear();
                            $this->line("<info>#{$article->id}</info> {$article->label} → {$article->label_en}");
                            $this->line('   ' . mb_substr($english, 0, 150) . '...');
                            $bar->display();
                        }
                    }
                }

                $done += $articles->count();
                $bar->advance($articles->count());
            }
        } catch (\RuntimeException $e) {
            if ($e->getCode() !== self::BLOCKED) {
                throw $e;
            }
            $bar->clear();
            $this->newLine();
            $this->error($e->getMessage());
            $this->warn("تمت ترجمة {$done} مادة قبل الحظر. أعد تشغيل نفس الأمر لاحقاً (بعد ساعة مثلاً) وسيكمل الباقي.");
            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("تمت ترجمة {$done} مادة بـ {$this->requests} طلب.");

        return self::SUCCESS;
    }

    private function persist(Article $article, bool $dryRun): void
    {
        if (!$dryRun) {
            $article->save();
        }
    }

    /**
     * التسميات من نوع "المادة 12 مكرر 3" تُترجم بقاعدة ثابتة حتى تكون موحدة
     * ("Article 12 bis 3") وبدون طلب، والباقي عبر Google.
     */
    private function translateLabel(string $label): string
    {
        $label = trim($label);
        if (preg_match('/^المادة\s+([0-9\-\s\/]+?)\s*(مكرر(?:ة|ا)?\s*([0-9]*))?$/u', $label, $m)) {
            $result = 'Article ' . trim($m[1]);
            if (!empty($m[2])) {
                $result .= ' bis' . (!empty($m[3]) ? ' ' . $m[3] : '');
            }
            return $result;
        }
        return $this->translate($label);
    }

    /**
     * تجميع المواد في دفعات: المواد القصيرة معاً حتى MAX_CHUNK،
     * والطويلة أو التي فيها جداول وحدها.
     *
     * @return array<int, array<int, Article>>  كل دفعة: [id => Article]
     */
    private function batches($articles): array
    {
        $batches = [];
        $current = [];
        $size = 0;
        foreach ($articles as $article) {
            $len = mb_strlen($article->text) + 20;
            $solo = $len > self::MAX_CHUNK || str_contains($article->text, '[[');
            if ($solo) {
                $batches[] = [$article->id => $article];
                continue;
            }
            if ($size + $len > self::MAX_CHUNK && $current) {
                $batches[] = $current;
                $current = [];
                $size = 0;
            }
            $current[$article->id] = $article;
            $size += $len;
        }
        if ($current) {
            $batches[] = $current;
        }
        return $batches;
    }

    /**
     * ترجمة دفعة: كل مادة تسبقها علامة [[#id]] ثم تُفصل النتيجة عليها.
     * إذا ضاعت علامة في الترجمة، تُترجم مواد الدفعة واحدة واحدة.
     *
     * @return array<int, string>  [id => النص الإنجليزي]
     */
    private function translateBatch(array $batch): array
    {
        if (count($batch) === 1) {
            $article = reset($batch);
            return [$article->id => $this->translate($article->text)];
        }

        $joined = '';
        foreach ($batch as $id => $article) {
            $joined .= "[[#{$id}]]\n" . trim($article->text) . "\n\n";
        }
        $translated = $this->request(trim($joined));

        $parts = preg_split('/\[\[\s*#\s*(\d+)\s*\]\]/u', $translated, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = [];
        for ($i = 1; $i < count($parts) - 1; $i += 2) {
            $id = (int) $parts[$i];
            if (isset($batch[$id])) {
                $result[$id] = trim($parts[$i + 1]);
            }
        }

        if (count($result) !== count($batch) || in_array('', $result, true)) {
            $result = [];
            foreach ($batch as $id => $article) {
                $result[$id] = $this->translate($article->text);
            }
        }
        return $result;
    }

    /** ترجمة نص واحد مع إبقاء علامات الجداول [[...]] بدون ترجمة */
    private function translate(string $text): string
    {
        $segments = preg_split('/(\[\[[^\]]+\]\])/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $out = '';
        foreach ($segments as $segment) {
            $out .= preg_match('/^\[\[[^\]]+\]\]$/u', $segment)
                ? $segment
                : $this->translatePlain($segment);
        }
        return $out;
    }

    private function translatePlain(string $text): string
    {
        if (trim($text) === '') {
            return $text;
        }
        preg_match('/^\s*/u', $text, $lead);
        preg_match('/\s*$/u', $text, $trail);
        $parts = [];
        foreach ($this->chunks(trim($text)) as $chunk) {
            $parts[] = trim($chunk) === '' ? $chunk : $this->request($chunk);
        }
        return $lead[0] . implode("\n", $parts) . $trail[0];
    }

    /** تقسيم النص على الأسطر، ثم على الجمل إذا كان السطر أطول من الحد */
    private function chunks(string $text): array
    {
        $chunks = [];
        $current = '';
        foreach (preg_split("/\r\n|\n|\r/", $text) as $line) {
            $pieces = mb_strlen($line) > self::MAX_CHUNK
                ? preg_split('/(?<=[\.\!\?؟؛])\s+/u', $line)
                : [$line];
            foreach ($pieces as $piece) {
                $candidate = $current === '' ? $piece : $current . "\n" . $piece;
                if (mb_strlen($candidate) > self::MAX_CHUNK && $current !== '') {
                    $chunks[] = $current;
                    $current = $piece;
                } else {
                    $current = $candidate;
                }
            }
        }
        if ($current !== '') {
            $chunks[] = $current;
        }
        return $chunks;
    }

    /**
     * طلب ترجمة واحد. عند الفشل يجرب الرابط الآخر، وعند الحظر ينتظر
     * (1 ← 2 ← 5 دقائق)، ثم يرمي RuntimeException برمز BLOCKED.
     */
    private function request(string $text): string
    {
        $waits = self::BLOCK_WAITS;
        $switches = 0;

        while (true) {
            usleep(((int) $this->option('sleep')) * 1000);
            $this->requests++;

            $status = 0;
            try {
                $response = Http::asForm()
                    ->timeout(60)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->post(self::ENDPOINTS[$this->endpoint], ['q' => $text]);
                $status = $response->status();
                if ($response->successful()) {
                    $translated = $this->parse($response->json());
                    if ($translated !== '') {
                        return $translated;
                    }
                }
            } catch (\Throwable $e) {
                // خطأ شبكة: نعامله كفشل مؤقت
            }

            // جرّب الرابط الآخر أولاً
            if ($switches < count(self::ENDPOINTS) - 1) {
                $switches++;
                $this->endpoint = ($this->endpoint + 1) % count(self::ENDPOINTS);
                continue;
            }

            // كل الروابط فشلت: انتظار ثم إعادة المحاولة، أو التوقف
            if (!$waits) {
                throw new \RuntimeException(
                    "Google Translate حظر هذا السيرفر مؤقتاً (HTTP {$status}) بعد {$this->requests} طلب.",
                    self::BLOCKED
                );
            }
            $wait = array_shift($waits);
            $this->newLine();
            $this->warn("Google رد بـ HTTP {$status}، انتظار {$wait} ثانية ثم إعادة المحاولة...");
            sleep($wait);
            $switches = 0;
        }
    }

    /** قراءة الرد حسب الرابط: ["..."] أو [["...","ar"]] أو [[["...", "..."], ...]] */
    private function parse($data): string
    {
        if (!is_array($data) || !isset($data[0])) {
            return '';
        }
        $first = $data[0];
        if (is_string($first)) {
            return $first;
        }
        if (is_array($first) && isset($first[0]) && is_string($first[0])) {
            return $first[0];
        }
        // gtx: [[["translated","original",...], ...], ...]
        $out = '';
        foreach ($first as $segment) {
            if (is_array($segment) && isset($segment[0]) && is_string($segment[0])) {
                $out .= $segment[0];
            }
        }
        return $out;
    }
}
