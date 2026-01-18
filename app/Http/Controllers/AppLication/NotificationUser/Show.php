<?php

namespace App\Http\Controllers\AppLication\NotificationUser;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Show extends Controller
{
        public function show(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'tax_id' => 'nullable|integer',
                ]);

                if ($validator->fails()) {
                    return Respons::error(
                        'بيانات غير صحيحة',
                        422,
                        $validator->errors()
                    );
                }

            $query = NotificationUsers::where('user_id', auth()->id())
                        ->with('notification');

                    if ($request->filled('tax_id')) {

                        $query->whereHas('notification', function ($q) use ($request) {

                            if ((int)$request->tax_id === 4) {
                                $q->whereNull('tax_id');
                            } else {
                                $q->where('tax_id', $request->tax_id);
                            }

                        });
                    }

                $data = $query->get();

                return Respons::success($data);

            } catch (\Exception $e) {
                return Respons::error('غير موجودة', 404,$e);
            }
        }
}
