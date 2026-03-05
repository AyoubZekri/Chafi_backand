<?php

namespace App\Http\Controllers\Dashboard\Bonusesandcompensation;
use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Bonusesandcompensations;

class Show extends Controller
{
    public function show()
        {
            try {
                $data = Bonusesandcompensations::all();

                return Respons::success(
                    $data
                );
            } catch (\Exception $e) {
                return Respons::error('غير موجودة', 404,$e);
            }
        }

}
