<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * ترجمة تسمية المواد ونصها من العربية إلى الإنجليزية (label_en / text_en)
 * عبر Google Translate المجاني (رابط dict-chrome-ex، لأن رابط gtx الذي كانت
 * تستعمله مكتبة stichoza يرد بـ 429 بعد الاستعمال الكثيف).
 * علامات الجداول مثل [[جدول:1]] تبقى كما هي في النص المترجم.
 *
 * أمثلة:
 *   php artisan articles:translate                  كل المواد غير المترجمة
 *   php artisan articles:translate --document=1     مواد ملف واحد
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
        {--sleep=300 : انتظار بين الطلبات بالميلي ثانية لتفادي الحظر}';

    protected $description = 'Translate articles label and text from Arabic to English (label_en, text_en)';

    private const ENDPOINT = 'https://clients5.google.com/translate_a/t?client=dict-chrome-ex&sl=ar&tl=en';

    // Google يرفض النصوص الطويلة جداً، نقسم النص إلى أجزاء أقل من هذا الحد
    private const MAX_CHUNK = 4500;

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

        $total = $limit ? min($limit, $query->count()) : $query->count();
        if ($total === 0) {
            $this->info('لا توجد مواد تحتاج ترجمة.');
            return self::SUCCESS;
        }
        $this->info("المواد المعنية: {$total}" . ($dryRun ? ' (تجربة بدون حفظ)' : ''));

        $bar = $this->output->createProgressBar($total);
        $bar->start();
        $done = 0;
        $failed = 0;

        $query->chunkById(50, function ($articles) use (&$done, &$failed, $total, $force, $dryRun, $bar) {
            foreach ($articles as $article) {
                if ($done + $failed >= $total) {
                    return false;
                }
                try {
                    if ($force || empty($article->label_en)) {
                        $article->label_en = $this->translateLabel($article->label);
                    }
                    if (($force || empty($article->text_en)) && trim((string) $article->text) !== '') {
                        $article->text_en = $this->translate($article->text);
                    }

                    if ($dryRun) {
                        $bar->clear();
                        $this->line("<info>#{$article->id}</info> {$article->label} → {$article->label_en}");
                        $this->line('   ' . mb_substr((string) $article->text_en, 0, 150) . '...');
                        $bar->display();
                    } else {
                        $article->save();
                    }
                    $done++;
                } catch (\Throwable $e) {
                    $failed++;
                    $bar->clear();
                    $this->error("المادة #{$article->id}: " . $e->getMessage());
                    $bar->display();
                }
                $bar->advance();
            }
            return true;
        });

        $bar->finish();
        $this->newLine(2);
        $this->info("تمت ترجمة: {$done}" . ($failed ? " — فشلت: {$failed} (أعد تشغيل الأمر لإكمالها)" : ''));

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    /**
     * التسميات من نوع "المادة 12 مكرر 3" تُترجم بقاعدة ثابتة حتى تكون موحدة
     * ("Article 12 bis 3")، والباقي عبر Google.
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

    /** ترجمة النص مع إبقاء علامات الجداول [[...]] بدون ترجمة */
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
        // الإبقاء على الأسطر حول علامات الجداول
        preg_match('/^\s*/u', $text, $lead);
        preg_match('/\s*$/u', $text, $trail);
        $parts = [];
        foreach ($this->chunks(trim($text)) as $chunk) {
            $parts[] = trim($chunk) === '' ? $chunk : $this->translateChunk($chunk);
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

    private function translateChunk(string $text): string
    {
        $attempts = 0;
        while (true) {
            $attempts++;
            usleep(((int) $this->option('sleep')) * 1000);

            $response = Http::asForm()
                ->timeout(30)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                ->post(self::ENDPOINT, ['q' => $text]);

            if ($response->successful()) {
                // الرد: ["الترجمة"] أو [["الترجمة", "ar"]]
                $data = $response->json();
                $first = is_array($data) ? ($data[0] ?? null) : null;
                $translated = is_array($first) ? ($first[0] ?? '') : (string) $first;
                if ($translated !== '') {
                    return $translated;
                }
            }

            // 429 = طلبات كثيرة: ننتظر أكثر ونعيد المحاولة
            if ($attempts >= 4) {
                throw new \RuntimeException('Google Translate: HTTP ' . $response->status());
            }
            sleep($attempts * 5);
        }
    }
}
