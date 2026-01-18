<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Rejester extends Controller
{
    public function Register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if ($validator->fails()) {
                return Respons::error('بيانات غير صحيحة', 422, $validator->errors());
            }

            DB::beginTransaction();
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'admin',
            ]);


            $token = $user->createToken('admin-token')->plainTextToken;
            DB::commit();

            return Respons::success([
                'user' => $user,
                "token"=> $token,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return Respons::error('حدث خطأ أثناء إنشاء حساب الأدمن', 500, $e->getMessage());
        }
    }
}

