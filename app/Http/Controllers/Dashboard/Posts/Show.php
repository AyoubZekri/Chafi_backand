<?php

namespace App\Http\Controllers\Dashboard\Posts;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'=> 'nullable|integer',
                "type"=>"required|integer"
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }
            $query = Post::where('type', $request->type);

            if ($request->filled('id')) {
                $query->where('id', $request->id);
            }

            $data = $query->get();

            return Respons::success(
                 $data
            );
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }

}
