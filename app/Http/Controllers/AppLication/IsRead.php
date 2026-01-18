<?php

namespace App\Http\Controllers\AppLication;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class IsRead extends Controller
{
    public static function IsRead(string $table, Request $request)
    {
            $allowedTables = [
            'read_differents',
            'read_taxs_and_apps',
            'read_institutions',
            ];

            if (!in_array($table, $allowedTables)) {
            return Respons::error('Table not allowed', 403);
            }

            $validator = Validator::make($request->all(), [
                "table_id" => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return Respons::error(
                    'بيانات غير صحيحة',
                    422,
                    $validator->errors()
                );
            }

            $data = [
            'user_id'   => auth()->id()
            ];

            if ($table === 'read_institutions') {
                $data['institution_id'] = $request->table_id;
            } elseif ($table === 'read_taxs_and_apps') {
                $data['tax_and_app_id'] = $request->table_id;
            } elseif ($table === 'read_differents') {
                $data['different_id'] = $request->table_id;
            }

            

            DB::table($table)->insert($data);

            return Respons::success('تم الإنشاء بنجاح');

    }
}
