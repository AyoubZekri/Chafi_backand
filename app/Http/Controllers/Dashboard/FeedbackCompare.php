<?php

namespace App\Http\Controllers\Dashboard;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;

/**
 * مقارنة آراء نفس المستخدم بين مشاركاته: الرأي الأول مع الثاني والثالث.
 *
 * كل مشاركة = مجموعة أسطر في feedbacks لنفس المستخدم بنفس created_at،
 * و type = رمز الاختيار (العشرات = رقم السؤال، مثال 23 = السؤال 2 الاختيار 3).
 *
 * الرد لكل سؤال:
 *  - rounds: توزيع الإجابات في كل جولة (1، 2، 3)
 *  - transitions: مصفوفة "من → إلى" بين الجولتين للمستخدمين الذين أجابوا في الاثنتين
 *  - summary: عدد من ثبت على رأيه ومن غيّره لكل مقارنة
 */
class FeedbackCompare extends Controller
{
    private const ROUNDS = [1, 2, 3];
    private const PAIRS = ['1-2', '2-3', '1-3'];

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

            // answers[user][round][question] = option
            $answers = [];
            $submissionIndex = [];
            foreach ($query->cursor() as $row) {
                $userId = $row->user_id;
                $time = (string) $row->created_at;
                if (!isset($submissionIndex[$userId][$time])) {
                    $submissionIndex[$userId][$time] = count($submissionIndex[$userId] ?? []) + 1;
                }
                $round = $submissionIndex[$userId][$time];
                if ($round > 3) {
                    continue;
                }
                $type = (int) $row->type;
                $question = intdiv($type, 10);
                if ($question < 1) {
                    continue;
                }
                $answers[$userId][$round][$question] = $type;
            }

            // عدد المشاركين في كل جولة
            $participants = array_fill_keys(self::ROUNDS, 0);
            foreach ($answers as $rounds) {
                foreach (array_keys($rounds) as $round) {
                    $participants[$round]++;
                }
            }

            $questionIds = [];
            foreach ($answers as $rounds) {
                foreach ($rounds as $byQuestion) {
                    foreach (array_keys($byQuestion) as $q) {
                        $questionIds[$q] = true;
                    }
                }
            }
            ksort($questionIds);

            $questions = [];
            foreach (array_keys($questionIds) as $q) {
                $questions[] = $this->questionStats($answers, $q);
            }

            return Respons::success([
                'users' => count($answers),
                'participants' => $participants,
                'questions' => $questions,
            ]);
        } catch (\Throwable $e) {
            return Respons::error($e->getMessage());
        }
    }

    private function questionStats(array $answers, int $question): array
    {
        $rounds = [];
        foreach (self::ROUNDS as $round) {
            $rounds[$round] = ['total' => 0, 'options' => []];
        }
        $transitions = [];
        $summary = [];
        foreach (self::PAIRS as $pair) {
            $transitions[$pair] = [];
            $summary[$pair] = ['users' => 0, 'same' => 0, 'changed' => 0];
        }

        foreach ($answers as $userRounds) {
            foreach (self::ROUNDS as $round) {
                $option = $userRounds[$round][$question] ?? null;
                if ($option === null) {
                    continue;
                }
                $rounds[$round]['total']++;
                $rounds[$round]['options'][$option] = ($rounds[$round]['options'][$option] ?? 0) + 1;
            }

            foreach (self::PAIRS as $pair) {
                [$from, $to] = array_map('intval', explode('-', $pair));
                $a = $userRounds[$from][$question] ?? null;
                $b = $userRounds[$to][$question] ?? null;
                if ($a === null || $b === null) {
                    continue;
                }
                $transitions[$pair][$a][$b] = ($transitions[$pair][$a][$b] ?? 0) + 1;
                $summary[$pair]['users']++;
                $summary[$pair][$a === $b ? 'same' : 'changed']++;
            }
        }

        return [
            'question' => $question,
            'rounds' => $rounds,
            'transitions' => $transitions,
            'summary' => $summary,
        ];
    }
}
