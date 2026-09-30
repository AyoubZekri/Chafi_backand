<?php

namespace App\Http\Controllers\Dashboard;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

/**
 * إحصائيات آراء المستخدمين حسب المشاركة: الرأي الأول، الثاني، الثالث،
 * أو "latest" = آخر رأي لكل مستخدم في كل سؤال.
 *
 * كل مشاركة = مجموعة أسطر في feedbacks لنفس المستخدم بنفس created_at،
 * و type = رمز الاختيار (العشرات = رقم السؤال، مثال 23 = السؤال 2 الاختيار 3).
 *
 * الرد:
 *  - users: عدد المستخدمين الذين شاركوا
 *  - participants: عدد المشاركين في كل جولة (1، 2، 3، latest)
 *  - questions: لكل سؤال، في كل جولة: total وعدد كل اختيار
 */
class FeedbackCompare extends Controller
{
    private const ROUNDS = ['1', '2', '3', 'latest'];

    public function compare(Request $request)
    {
        try {
            $query = Feedback::query()
                ->select('feedbacks.user_id', 'feedbacks.type', 'feedbacks.created_at')
                ->orderBy('feedbacks.user_id')
                ->orderBy('feedbacks.created_at');

            // فلتر اختياري حسب صفة المكلف بالضريبة
            if ($request->filled('is_taxpayer')) {
                $query->join('users', 'users.id', '=', 'feedbacks.user_id')
                      ->where('users.is_taxpayer', $request->is_taxpayer);
            }

            return Respons::success($this->aggregate($query->cursor()));
        } catch (\Throwable $e) {
            return Respons::error($e->getMessage());
        }
    }

    /**
     * @param iterable $rows أسطر feedbacks مرتبة حسب user_id ثم created_at
     */
    public function aggregate(iterable $rows): array
    {
        // answers[user][round][question] = option ، round = 1..3 أو latest
        $answers = [];
        $submissionIndex = [];
        foreach ($rows as $row) {
            $userId = $row->user_id;
            $time = (string) $row->created_at;
            if (!isset($submissionIndex[$userId][$time])) {
                $submissionIndex[$userId][$time] = count($submissionIndex[$userId] ?? []) + 1;
            }
            $round = $submissionIndex[$userId][$time];

            $type = (int) $row->type;
            $question = intdiv($type, 10);
            if ($question < 1) {
                continue;
            }
            if ($round <= 3) {
                $answers[$userId][(string) $round][$question] = $type;
            }
            // الأسطر مرتبة زمنياً، فآخر قيمة هي آخر رأي في هذا السؤال
            $answers[$userId]['latest'][$question] = $type;
        }

        $participants = array_fill_keys(self::ROUNDS, 0);
        $stats = [];
        foreach ($answers as $rounds) {
            foreach ($rounds as $round => $byQuestion) {
                $participants[$round]++;
                foreach ($byQuestion as $question => $option) {
                    $stats[$question][$round]['total'] = ($stats[$question][$round]['total'] ?? 0) + 1;
                    $stats[$question][$round]['options'][$option] =
                        ($stats[$question][$round]['options'][$option] ?? 0) + 1;
                }
            }
        }
        ksort($stats);

        $questions = [];
        foreach ($stats as $question => $rounds) {
            $questions[] = ['question' => $question, 'rounds' => (object) $rounds];
        }

        return [
            'users' => count($answers),
            'participants' => (object) $participants,
            'questions' => $questions,
        ];
    }
}
