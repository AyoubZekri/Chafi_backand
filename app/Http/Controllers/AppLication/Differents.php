<?php

namespace App\Http\Controllers\AppLication;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Different;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Differents extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type'            => 'required|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $userId = auth()->id(); 

                $data = Different::where('type', $request->type)
                    ->orderBy('index', 'asc')
                    ->with(['reads' => function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    }])
                    ->get()
                    ->map(function($item) {
                        $item->is_read = $item->reads->count() > 0;
                        unset($item->reads); 
                        return $item;
                    });

            return Respons::success(
                 $data
            );
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }
}
