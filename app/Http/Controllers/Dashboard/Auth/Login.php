<?php

namespace App\Http\Controllers\Dashboard\Auth;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Exception;


class Login extends Controller
{

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password) || $user->role != "admin") {
                throw ValidationException::withMessages([
                    'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة'],
                ]);
            }

            $token = $user->createToken('admin-token')->plainTextToken;

            return Respons::success([
                'user' => $user,
                "token"=> $token,
            ], 'تم تسجيل الدخول بنجاح');

        } catch (ValidationException $e) {

            return Respons::error('بيانات الدخول غير صحيحة', 422, $e->errors());

        } catch (Exception $e) {

            return Respons::error('حدث خطأ أثناء محاولة تسجيل الدخول', 500, $e->getMessage());

        }
    }

}
