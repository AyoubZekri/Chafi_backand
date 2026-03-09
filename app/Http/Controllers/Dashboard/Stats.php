<?php

namespace App\Http\Controllers\Dashboard;

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

        return response()->json([
                "totalUsersEnter" => $totalUsersEnter,
                "totalGuestsEnter" => $totalGuestsEnter,
                "totalUsersEntertoday" => $totalUsersEntertoday,
                "totalGuestsEntertoday" => $totalGuestsEntertoday,
                "user" =>$user,
                "tax_1_jazafi" => $tax1,
                "tax_2_mobassat" => $tax2,
                "tax_3_hakiki" => $tax3

        ]);
    }


    public function addUserEnter()
    {
        $today = Carbon::today();

        $stat = ModelsStats::whereDate('created_at', $today)->first();

        if ($stat) {
            $stat->increment('numper_enter');
        } else {
            ModelsStats::create([
                'numper_enter' => 1,
                'numper_enter_Guest' => 0,
            ]);
        }
    }



    public function addGuestEnter()
    {
        $today = Carbon::today();

        $stat = ModelsStats::whereDate('created_at', $today)->first();

        if ($stat) {
            $stat->increment('numper_enter_Guest');
        } else {
            ModelsStats::create([
                'numper_enter' => 0,
                'numper_enter_Guest' => 1,
            ]);
        }
    }
}
