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
            $users = User::where('role', 'user')
                ->withCount(['stats'])
                ->with('feedback')
                ->get();

            // تجميع التقييمات حسب وقت الإرسال لتظهر كل عملية إضافة على حدى
            $data = $users->map(function ($user) {
                $userArray = $user->toArray();
                
                if (isset($userArray['feedback'])) {
                    $groupedFeedbacks = collect($userArray['feedback'])->groupBy(function ($item) {
                        return \Carbon\Carbon::parse($item['created_at'])->format('Y-m-d H:i:s');
                    })->map(function ($group) {
                        return [
                            'date' => \Carbon\Carbon::parse($group->first()['created_at'])->format('Y-m-d H:i:s'),
                            'types' => $group->pluck('type')->toArray()
                        ];
                    })->values()->toArray();
                    
                    $userArray['feedback'] = $groupedFeedbacks;
                }
                
                return $userArray;
            });

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
