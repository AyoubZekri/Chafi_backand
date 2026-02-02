<?php

namespace App\Http\Controllers\Dashboard\activitys;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nataire_activitys_id'=> 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

        $data = Activity::with('NataireActivity')
            ->where('nataire_activitys_id', $request->nataire_activitys_id)
            ->orderBy('index', 'asc')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'body' => $item->body,
                    'name_fr' => $item->name_fr,
                    'body_fr' => $item->body_fr,
                    'tax_id' => $item->tax_id,
                    'nataire_activitys_id' => $item->nataire_activitys_id,

                    'status_tax' => $item->status_tax,
                    'code_activity' => $item->code_activity,
                    'index' => $item->index,
                    'updated_at' => $item->updated_at,
                    'created_at' => $item->created_at,

                    // أسماء النظام مباشرة مع النشاط
                    'nataire_name' => $item->NataireActivity?->name,
                    'nataire_name_fr' => $item->NataireActivity?->name_fr,
                ];
            });

            return Respons::success(
                 $data
            );
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404);
        }
    }
}
