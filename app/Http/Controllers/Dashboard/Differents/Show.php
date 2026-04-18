<?php

namespace App\Http\Controllers\Dashboard\Differents;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Different;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
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

            $data = Different::with('laws.law')
            ->where('type', $request->type)->orderBy('index', 'asc')->get()                
            ->map(function($item) {
                $item->is_read = $item->reads->count() > 0;
                unset($item->reads);
                    $item->setRelation('laws', $item->laws->map(function ($law) {
                        return [
                            'law_id'     => $law->law_id,
                            'name_ar'    => $law->name_ar,
                            'name_fr'    => $law->name_fr,
                            'index_link' => $law->index_link,
                            'pdf'        => optional($law->law)->pdf,
                        ];
                    }));
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
