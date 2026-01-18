<?php

namespace App\Http\Controllers\Dashboard\Posts;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Edit extends Controller
{
    public function EditPost(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:posts,id',
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

            $post = Post::findOrFail($data['id']);

            if ($request->hasFile('image')) {
                if ($post->image && Storage::disk('public')->exists($post->image)) {
                    Storage::disk('public')->delete($post->image);
                }

                $path = $request->file('image')->store('Post', 'public');
                $data['image'] = $path;
            }

            $post->update($data);

            return Respons::success($post,'تم التعديل بنجاح');
        } catch (\Exception $e) {
            return Respons::error(
                'حدث خطأ أثناء التعديل',
                500,
                $e->getMessage()
            );
        }
    }

}
