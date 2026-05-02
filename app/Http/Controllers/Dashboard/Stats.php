<?php

namespace App\Http\Controllers\Dashboard;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Mypath;
use App\Models\Stats as ModelsStats;
use App\Models\Feedback;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class Stats extends Controller
{


    public function stats()
    {
        $today = Carbon::today();

        $user = User::where('role', "user")->count();
        $item = ModelsStats::select(
            DB::raw('SUM(CASE WHEN type_user = 1 AND type_stats = 0 THEN 1 ELSE 0 END) as totalUser'),
            DB::raw('SUM(CASE WHEN type_user = 2 AND type_stats = 0 THEN 1 ELSE 0 END) as totalG'),
            DB::raw("SUM(CASE WHEN type_user = 1 AND type_stats = 0 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyUser"),
            DB::raw("SUM(CASE WHEN type_user = 2 AND type_stats = 0 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyG"),
            DB::raw('SUM(CASE WHEN type_user = 1 AND type_stats = 1 THEN 1 ELSE 0 END) as totalIns'),
            DB::raw("SUM(CASE WHEN type_user = 1 AND type_stats = 1 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyIns"),
            DB::raw('SUM(CASE WHEN type_user = 1 AND type_stats = 2 THEN 1 ELSE 0 END) as totalTax'),
            DB::raw("SUM(CASE WHEN type_user = 1 AND type_stats = 2 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyTax"),
            DB::raw('SUM(CASE WHEN type_user = 1 AND type_stats = 3 THEN 1 ELSE 0 END) as totalCard'),
            DB::raw("SUM(CASE WHEN type_user = 1 AND type_stats = 3 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyCard"),
            DB::raw('SUM(CASE WHEN type_user = 1 AND type_stats = 4 THEN 1 ELSE 0 END) as totalCal'),
            DB::raw("SUM(CASE WHEN type_user = 1 AND type_stats = 4 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyCal"),

        )->first();
        $tax1 = Mypath::where('tax_id', 0)->count();
        $tax2 = Mypath::where('tax_id', 1)->count();
        $tax3 = Mypath::where('tax_id', 2)->count();
        $total = Mypath::count();

        $tax1Percent = $total > 0 ? (Mypath::where('tax_id', 0)->count() * 100) / $total : 0;
        $tax2Percent = $total > 0 ? (Mypath::where('tax_id', 1)->count() * 100) / $total : 0;
        $tax3Percent = $total > 0 ? (Mypath::where('tax_id', 2)->count() * 100) / $total : 0;

        // تقريب النسب
        $tax1Percent = round($tax1Percent, 2);
        $tax2Percent = round($tax2Percent, 2);
        $tax3Percent = round($tax3Percent, 2);

        $today = now()->toDateString();

        $dashboardData = ModelsStats::select(
            'state',
            DB::raw('SUM(CASE WHEN type_user = 1 THEN 1 ELSE 0 END) as totalUser'),
            DB::raw('SUM(CASE WHEN type_user = 2 THEN 1 ELSE 0 END) as totalG'),
            DB::raw("SUM(CASE WHEN type_user = 1 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyUser"),
            DB::raw("SUM(CASE WHEN type_user = 2 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyG"),

        )
            ->groupBy('state')
            ->get();

        $allChartData = ModelsStats::select(
            'open_date as date',
            DB::raw('SUM(CASE WHEN type_stats = 1 THEN 1 ELSE 0 END) as type_1'),
            DB::raw('SUM(CASE WHEN type_stats = 2 THEN 1 ELSE 0 END) as type_2'),
            DB::raw('SUM(CASE WHEN type_stats = 3 THEN 1 ELSE 0 END) as type_3'),
            DB::raw('SUM(CASE WHEN type_stats = 4 THEN 1 ELSE 0 END) as type_4')
        )
        ->where('type_user', 1)
        ->groupBy('open_date')
        ->orderBy('open_date', 'ASC')
        ->get();

        $formatData = function ($groupedData) {
            return $groupedData->map(function ($chunk, $key) {
                return [
                    'date' => (string) $key,
                    'type_1' => $chunk->sum('type_1'),
                    'type_2' => $chunk->sum('type_2'),
                    'type_3' => $chunk->sum('type_3'),
                    'type_4' => $chunk->sum('type_4'),
                ];
            })->values();
        };

        $chartDaily = $allChartData;

        $chartWeekly = $formatData($allChartData->groupBy(function ($item) {
            return Carbon::parse($item->date)->startOfWeek()->format('Y-m-d');
        }));

        $chartMonthly = $formatData($allChartData->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('Y-m');
        }));

        $chartYearly = $formatData($allChartData->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('Y');
        }));

        $chartStatsData = [
            'daily' => $chartDaily,
            'weekly' => $chartWeekly,
            'monthly' => $chartMonthly,
            'yearly' => $chartYearly,
        ];

        $allUsersGuestsData = ModelsStats::select(
            'open_date as date',
            DB::raw('SUM(CASE WHEN type_user = 1 THEN 1 ELSE 0 END) as users_count'),
            DB::raw('SUM(CASE WHEN type_user = 2 THEN 1 ELSE 0 END) as guests_count')
        )
        ->groupBy('open_date')
        ->orderBy('open_date', 'ASC')
        ->get();

        $formatUsersGuestsData = function ($groupedData) {
            return $groupedData->map(function ($chunk, $key) {
                return [
                    'date' => (string) $key,
                    'users_count' => $chunk->sum('users_count'),
                    'guests_count' => $chunk->sum('guests_count'),
                ];
            })->values();
        };

        $chartUsersGuestsData = [
            'daily' => $allUsersGuestsData,
            'weekly' => $formatUsersGuestsData($allUsersGuestsData->groupBy(function ($item) {
                return Carbon::parse($item->date)->startOfWeek()->format('Y-m-d');
            })),
            'monthly' => $formatUsersGuestsData($allUsersGuestsData->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m');
            })),
            'yearly' => $formatUsersGuestsData($allUsersGuestsData->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('Y');
            })),
        ];

        $totalAllUsers = $dashboardData->sum(fn($item) => $item->totalUser + $item->totalG);
        $dailyAllUsers = $dashboardData->sum(fn($item) => $item->dailyUser + $item->dailyG);

        $dashboard = $dashboardData->map(function ($item) use ($totalAllUsers, $dailyAllUsers) {
            $totalCount = $totalAllUsers; // ← مجموع كل الولايات
            $dailyCount = $item->dailyUser + $item->dailyG;

            $dailyPercent = $totalCount > 0 ? round(($dailyCount * 100) / $totalCount, 2) : 0;

            return [
                'state' => $item->state,
                'dailyUser' => $item->dailyUser,
                'dailyG' => $item->dailyG,
                'totalUser' => $item->totalUser,
                'totalG' => $item->totalG,
                'daily_percent' => $dailyPercent, // نسبة دخول اليوم مقابل الاجمالي لكل الولايات
            ];
        });
        return Respons::success([
            "totalUsersEnter" => $item->totalUser,
            "totalGuestsEnter" => $item->totalG,
            "totalUsersEntertoday" => $item->dailyUser,
            "totalGuestsEntertoday" => $item->dailyG,
            "totalIns" => $item->totalIns,
            "totalTax" => $item->totalTax,
            "totalCard" => $item->totalCard,
            "totalCal" => $item->totalCal,
            "dailyIns" => $item->dailyIns,
            "dailyTax" => $item->dailyTax,
            "dailyCard" => $item->dailyCard,
            "dailyCal" => $item->dailyCal,
            "user" => $user,
            "tax_1_jazafi" => $tax1,
            "tax_2_mobassat" => $tax2,
            "tax_3_hakiki" => $tax3,
            "tax1Percent" => $tax1Percent,
            "tax2Percent" => $tax2Percent,
            "tax3Percent" => $tax3Percent,
            'chart_stats_data' => $chartStatsData,
            'chart_users_guests_data' => $chartUsersGuestsData,
            'data' => $dashboard
        ]);
    }


    public function addUserEnter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'nullable|string',
            'state' => 'nullable|string',
            'type_stats' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
        }

        $data = $validator->validated();
        $today = Carbon::today()->toDateString();

        $insertData = [
            'state' => $data['state'] ?? "",
            'type_user' => 1,
            'user_id' => auth()->id(),
        ];
        
        if (isset($data['type_stats']) && $data['type_stats'] !== '') {
            $insertData['type_stats'] = $data['type_stats'];
        }

        $stats = ModelsStats::firstOrCreate(
            ['device_id' => $data['device_id'] ?? "", 'open_date' => $today],
            $insertData
        );

        return Respons::success("ok");
    }

    public function addGuestEnter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'state' => 'required|string',
            'type_stats' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
        }

        $data = $validator->validated();
        $today = Carbon::today()->toDateString();

        $insertData = [
            'state' => $data['state'] ?? "",
            'type_user' => 2,
        ];
        
        if (isset($data['type_stats']) && $data['type_stats'] !== '') {
            $insertData['type_stats'] = $data['type_stats'];
        }

        $stats = ModelsStats::firstOrCreate(
            ['device_id' => $data['device_id'] ?? "", 'open_date' => $today],
            $insertData
        );

        return Respons::success("ok");
    }

    public function addFeedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'rating' => 'nullable|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
        }

        $data = $validator->validated();
        $data['user_id'] = auth()->id();

        Feedback::create($data);

        return Respons::success("تمت إضافة الرأي بنجاح");
    }
}
