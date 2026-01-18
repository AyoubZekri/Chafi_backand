<?php

namespace App\Http\Controllers\Dashboard\Nataire_activitys;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\NataireActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show()
    {
        try {
            $data = NataireActivity::orderBy('index', 'asc')->get();
            return Respons::success(
                 $data
            );
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }
}
