<?php

namespace App\Http\Controllers\Dashboard;

use App\Function\Respons;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Stichoza\GoogleTranslate\GoogleTranslate;
use App\Models\User;



class UserController extends Controller
{
    public function show()
    {
        try {
            $data = User::where('role', 'user')
                ->withCount(['stats'])
                ->with('feedback')
                ->get();

            return Respons::success(
                $data
            );
        } catch (\Exception $e) {
            return Respons::error($e, 404);
        }
    }
    public function translateActivities()
    {    
        $translator = new GoogleTranslate();

        $translator->setSource('ar');
        $translator->setTarget('en');

        Activity::chunk(50, function ($activities) use ($translator) {

            foreach ($activities as $activity) {

                try {

                    // ترجمة الاسم
                    if (!empty($activity->name) && empty($activity->name_fr)) {

                        $activity->name_fr =
                            $translator->translate($activity->name);
                    }

                    // ترجمة الوصف
                    if (!empty($activity->body) && empty($activity->body_fr)) {

                        $activity->body_fr =
                            $translator->translate($activity->body);
                    }

                    $activity->save();

                } catch (\Exception $e) {

                    continue;
                }
            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Translation completed'
        ]);
    }

}
