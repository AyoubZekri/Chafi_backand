<?php

namespace App\Http\Controllers\Dashboard\Posts;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Add extends Controller
{
    public function addPost(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "type" => 'nullable|integer',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'title' => 'nullable|string|max:255',
                'title2' => 'nullable|string|max:255',
                'body' => 'nullable|string|max:255',
                'title_fr' => 'nullable|string|max:255', 
                'title2_fr' => 'nullable|string|max:255',
                'body_fr' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $data = $validator->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('Post', 'public');
                $data['image'] = $path;
            }

            Post::create($data);

            return Respons::success('تم الإنشاء بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء الإنشاء',
                500,
                $e->getMessage()
            );
        }
    }

}
