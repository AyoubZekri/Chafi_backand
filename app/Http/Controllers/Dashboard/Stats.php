<?php

namespace App\Http\Controllers\Dashboard;

use App\Function\Respons;
use App\Http\Controllers\Controller;
  use App\Models\User;
use App\Models\Mypath;
use App\Models\Stats as ModelsStats;
use Carbon\Carbon;

class Stats extends Controller
{


    public function stats()
    {
        $today = Carbon::today();

        $user = User::where('role',"admin")->count();
        $totalUsersEnter = ModelsStats::sum('numper_enter');
        $totalGuestsEnter = ModelsStats::sum('numper_enter_Guest');
        $totalUsersEntertoday= ModelsStats::whereDate('created_at', $today)->sum('numper_enter');
        $totalGuestsEntertoday = ModelsStats::whereDate('created_at', $today)->sum('numper_enter_Guest');
        $tax1 = Mypath::where('tax_id', 1)->count();
        $tax2 = Mypath::where('tax_id', 2)->count();
        $tax3 = Mypath::where('tax_id', 3)->count();
        $total = Mypath::count();

        $tax1Percent = $total > 0 ? (Mypath::where('tax_id', 1)->count() * 100) / $total : 0;
        $tax2Percent = $total > 0 ? (Mypath::where('tax_id', 2)->count() * 100) / $total: 0;
        $tax3Percent = $total > 0 ? (Mypath::where('tax_id', 3)->count() * 100) / $total: 0;

        // تقريب النسب
        $tax1Percent = round($tax1Percent, 2);
        $tax2Percent = round($tax2Percent, 2);
        $tax3Percent = round($tax3Percent, 2);

        return Respons::success([
            "totalUsersEnter" => $totalUsersEnter,
            "totalGuestsEnter" => $totalGuestsEnter,
            "totalUsersEntertoday" => $totalUsersEntertoday,
            "totalGuestsEntertoday" => $totalGuestsEntertoday,
            "user" => $user,
            "tax_1_jazafi" => $tax1,
            "tax_2_mobassat" => $tax2,
            "tax_3_hakiki" => $tax3,
            "tax1Percent" => $tax1Percent,
            "tax2Percent" => $tax2Percent,
            "tax3Percent" => $tax3Percent
        ]);
}


    public function addUserEnter()
    {
        $today = Carbon::today();

        $stat = ModelsStats::whereDate('created_at', $today)->first();

        if ($stat) {
            $stat->increment('numper_enter');
            return Respons::success();
        } else {
            ModelsStats::create([
                'numper_enter' => 1,
                'numper_enter_Guest' => 0,
            ]);
        return Respons::success();

        }
    }



    public function addGuestEnter()
    {
        $today = Carbon::today();

        $stat = ModelsStats::whereDate('created_at', $today)->first();

        if ($stat) {
            $stat->increment('numper_enter_Guest');
            return Respons::success();

        } else {
            ModelsStats::create([
                'numper_enter' => 0,
                'numper_enter_Guest' => 1,
            ]);
            return Respons::success();

        }
    }
   }
