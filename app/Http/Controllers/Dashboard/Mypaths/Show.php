<?php

namespace App\Http\Controllers\Dashboard\Mypaths;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Mypath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'=> 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }
            $query = Mypath::query();

            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            $data = $query->where('user_id', auth()->id())->get();

            return Respons::success(
                 $data
            );
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }
}
