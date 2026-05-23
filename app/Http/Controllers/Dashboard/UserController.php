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
        try {

            $translator = new GoogleTranslate();

            $translator->setSource('ar');
            $translator->setTarget('en');

            Activity::chunk(10, function ($activities) use ($translator) {

                foreach ($activities as $activity) {

                    try {

                        if (!empty($activity->name)) {

                            $activity->name_fr =
                                $translator->translate($activity->name);
                        }

                        if (!empty($activity->body)) {

                            $activity->body_fr =
                                $translator->translate($activity->body);
                        }

                        $activity->save();

                    } catch (\Exception $e) {

                        echo $e->getMessage();
                    }
                }
            });

            return response()->json([
                'status' => true
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
