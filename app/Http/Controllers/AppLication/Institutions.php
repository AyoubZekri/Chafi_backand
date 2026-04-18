<?php

namespace App\Http\Controllers\AppLication;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Institutions extends Controller
{
    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'scope'            => 'required|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $userId = auth()->id();

            $data = Institution::query()
                ->where('scope', $request->scope)
                ->orderBy('index', 'asc')
                ->with('laws.law')
                ->with([
                    'reads' => function($q) use ($userId) {
                        $q->where('user_id', $userId);
                    }
                ])
                ->get()
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

         return Respons::success($data);            
        } catch (\Exception $e) {
            return Respons::error('غير موجودة', 404,$e);
        }
    }



}
