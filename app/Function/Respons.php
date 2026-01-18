<?php

namespace App\Function;

class Respons
{
    public static function success($data, $message = 'Success', $code = 200)
    {
        $response = [
            'status' => 1,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    public static function error($message = 'حدث خطأ', $code = 500, $errors = [])
    {
        $response = [
            'status' => 0,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
