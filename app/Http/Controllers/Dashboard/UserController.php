<?php

namespace App\Http\Controllers\Dashboard;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\User;



class UserController extends Controller
{
    public function show()
    {
        try {
            $data = User::where('role', 'user')
                ->withCount(['stats'])
                ->with('feedback')
                ->get();

            return Respons::success(
                 $data
            );
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }


}
