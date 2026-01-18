<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class Logout extends Controller
{
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

         return Respons::success();

        } catch (Exception $e) {
            return Respons::error('حدث خطأ أثناء تسجيل الخروج', 500, $e->getMessage());

        }
    }

}
