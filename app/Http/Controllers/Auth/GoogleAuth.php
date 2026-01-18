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
use stdClass;


class GoogleAuth extends Controller
{
    public function GoogleLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'uid' => 'required|string',
            'token' => 'sometimes|string',
            "username"=>"sometimes|string",
            "numperPhone"=>"sometimes|integer",
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
            $user = User::create([
                'name' => $firebase->displayName ?? "no name",
                'email' => $firebase->email,
                "username"=>$request->username,
                "role"=>"user",
                "numperPhone"=>$request->numperPhone,
                "wilaya"=>$request->wilaya,
                'password' => Hash::make("password@1234"),
                // 'google_id' => $firebase->id,
                "profile_image"=>$firebase->photoUrl
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



    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username"=>"sometimes|string",
            "numperPhone"=>"sometimes|integer",
            "wilaya"=>"sometimes|string",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()->first()
            ], 400);
        }

        $user = $request->user();

        $data = new stdClass();
        $data->user = $user;

        return response()->json([
            'status' => 1,
            'message' => 'Success',
            'data' => $data
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

