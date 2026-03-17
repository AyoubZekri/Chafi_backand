<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Image;

class GoogleAuth extends Controller
{
    public function GoogleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string',
            'token' => 'sometimes|string',
            "username"=>"sometimes|string",
            "numperPhone"=>"sometimes|string",
            "wilaya"=>"sometimes|string",
        ]);


        if ($validator->fails()) {
            return response()->json([
                'uid' => $request->uid,
                'status' => 0,
                'message' => $validator->errors()->first(),
            ], 400);

        }

        DB::beginTransaction();

        $auth = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->createAuth();



        try {
            $firebase = $auth->getUser($request->uid);
        } catch (UserNotFound $e) {
            return response()->json([
                'message' => 'User not found in Firebase'
            ], 404);
        }


        $user = User::where('email', $firebase->email)->first();

        if ($user) {
            $user->token = $request->token;
            $user->save();
            if ($user->isUser()) {
                $token = $user->createToken('auth_token')->plainTextToken;
                DB::commit();
                return response()->json([
                    'status' => 1,
                    'message' => 'Success',
                    'access_token' => $token,
                    'user' => $user,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => ' ليس لديك صلاحيات '
                ], 403);
            }
        } else {
            $imageContents = Http::get($firebase->photoUrl)->body();
            $fileName = 'user/' . uniqid() . '.jpg';
            $image = Image::make($imageContents)->encode('jpg', 80);
            Storage::disk('public')->put($fileName,$image);

            $profilePath = $fileName;
            $user = User::create([
                'name' => $firebase->displayName ?? "no name",
                'email' => $firebase->email,
                "username"=>$request->username,
                "token"=>$request->token,
                "role"=>"user",
                "numperPhone"=>$request->numperPhone,
                "wilaya"=>$request->wilaya,
                'password' => Hash::make("password@1234"),
                "image"=>$profilePath
            ]);



            // Mail::to($user['email'])->send(new WelcomeMail($user));

            $token = $user->createToken('auth_token')->plainTextToken;
            DB::commit();
            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'access_token' => $token,
                'user' => $user,
            ]);
        }


    }

    public function Login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string',
            'token' => 'sometimes|string',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'uid' => $request->uid,
                'status' => 0,
                'message' => $validator->errors()->first(),
            ], 400);

        }

        DB::beginTransaction();

        $auth = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->createAuth();



        try {
            $firebase = $auth->getUser($request->uid);
        } catch (UserNotFound $e) {
            return response()->json([
                'message' => 'User not found in Firebase'
            ], 404);
        }




        $user = User::where('email', $firebase->email)->first();



        if ($user) {
            $user->token = $request->token;
            $user->save();
            if ($user->isUser()) {
                $token = $user->createToken('auth_token')->plainTextToken;
                DB::commit();
                return response()->json([
                    'status' => 1,
                    'message' => 'Success',
                    'access_token' => $token,
                    'user' => $user,
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'status' => 0,
                    'message' => ' ليس لديك صلاحيات '
                ], 403);
            }
        } else {
                return response()->json([
                    'status' => 0,
                ],);
        }


    }



public function update(Request $request)
{
    $validator = Validator::make($request->all(), [
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'username' => 'sometimes|string',
        'numperPhone' => 'sometimes|integer',
        'wilaya' => 'sometimes|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors()->first()
        ], 400);
    }

    // جلب المستخدم الحالي
    $user = $request->user();

    // تحديث الحقول إذا موجودة في الطلب
    if ($request->has('username')) {
        $user->username = $request->username;
    }
    if ($request->has('numperPhone')) {
        $user->numperPhone = $request->numperPhone;
    }
    if ($request->has('wilaya')) {
        $user->wilaya = $request->wilaya;
    }

    // تحديث الصورة إذا موجودة
    if ($request->hasFile('image')) {
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }
        $path = $request->file('image')->store('user', 'public');
        $user->image = $path;
    }

    // حفظ التحديثات
    $user->save();

    return response()->json([
        'status' => 1,
        'message' => 'Success',
        'user' => $user,
    ], 200);
}



    public function logout(Request $request)
    {
        $user = $request->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $user->delete();
        // $user->tokens()->delete();

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ]);
    }

}

