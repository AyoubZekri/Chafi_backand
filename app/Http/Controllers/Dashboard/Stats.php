<?php

namespace App\Http\Controllers\Dashboard;

use App\Function\Respons;
use App\Http\Controllers\Controller;
  use App\Models\User;
use App\Models\Mypath;
use App\Models\Stats as ModelsStats;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class Stats extends Controller
{


    public function stats()
    {
        $today = Carbon::today();

        $user = User::where('role',"user")->count();
        $item = ModelsStats::select(
            DB::raw('SUM(CASE WHEN type_user = 1 THEN 1 ELSE 0 END) as totalUser'),
            DB::raw('SUM(CASE WHEN type_user = 2 THEN 1 ELSE 0 END) as totalG'),
            DB::raw("SUM(CASE WHEN type_user = 1 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyUser"),
            DB::raw("SUM(CASE WHEN type_user = 2 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyG")
        )->first();
        $tax1 = Mypath::where('tax_id', 0)->count();
        $tax2 = Mypath::where('tax_id', 1)->count();
        $tax3 = Mypath::where('tax_id', 2)->count();
        $total = Mypath::count();

        $tax1Percent = $total > 0 ? (Mypath::where('tax_id', 0)->count() * 100) / $total : 0;
        $tax2Percent = $total > 0 ? (Mypath::where('tax_id', 1)->count() * 100) / $total: 0;
        $tax3Percent = $total > 0 ? (Mypath::where('tax_id', 2)->count() * 100) / $total: 0;

        // تقريب النسب
        $tax1Percent = round($tax1Percent, 2);
        $tax2Percent = round($tax2Percent, 2);
        $tax3Percent = round($tax3Percent, 2);

        $today = now()->toDateString();

        $dashboard = ModelsStats::select(
                'state',
                DB::raw('SUM(CASE WHEN type_user = 1 THEN 1 ELSE 0 END) as totalUser'),
                DB::raw('SUM(CASE WHEN type_user = 2 THEN 1 ELSE 0 END) as totalG'),
                DB::raw("SUM(CASE WHEN type_user = 1 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyUser"),
                DB::raw("SUM(CASE WHEN type_user = 2 AND open_date = '{$today}' THEN 1 ELSE 0 END) as dailyG")
            )
            ->groupBy('state')
            ->get()
            ->map(function ($item) {
                $totalCount = $item->totalUser + $item->totalG;
                $dailyCount = $item->dailyUser + $item->dailyG;
                $dailyPercent = $totalCount > 0 ? round(($dailyCount * 100) / $totalCount, 2) : 0;

                return [
                    'state' => $item->state,
                    'dailyUser' => $item->dailyUser,
                    'dailyG' => $item->dailyG,
                    'totalUser' => $item->totalUser,
                    'totalG' => $item->totalG,
                    'daily_percent' => $dailyPercent,
                ];
            });
            return Respons::success([
            "totalUsersEnter" => $item->totalUser,
            "totalGuestsEnter" =>  $item->totalG,
            "totalUsersEntertoday" =>  $item->dailyUser,
            "totalGuestsEntertoday" =>  $item->dailyG,
            "user" => $user,
            "tax_1_jazafi" => $tax1,
            "tax_2_mobassat" => $tax2,
            "tax_3_hakiki" => $tax3,
            "tax1Percent" => $tax1Percent,
            "tax2Percent" => $tax2Percent,
            "tax3Percent" => $tax3Percent,
            'data' => $dashboard
        ]);
}


    public function addUserEnter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'nullable|string',
            'state' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
        }

        $data = $validator->validated();
        $today = Carbon::today()->toDateString();

        $stats = ModelsStats::firstOrCreate(
            ['device_id' => $data['device_id']??"", 'open_date' => $today],
            [
                'state' => $data['state']??"",
                'type_user' => 1,
            ]
        );

        return Respons::success("ok");
    }

    public function addGuestEnter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required|string',
            'state' => 'required|string',
        ]);

        if ($validator->fails()) {
            return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
        }

        $data = $validator->validated();
        $today = Carbon::today()->toDateString();

        $stats = ModelsStats::firstOrCreate(
            ['device_id' => $data['device_id']??"", 'open_date' => $today],
            [
                'state' => $data['state']??"",
                'type_user' => 2,
            ]
        );

        return Respons::success("ok");
    }
}
