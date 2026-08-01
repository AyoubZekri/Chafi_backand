<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Institution;
use App\Models\Different;
use App\Models\TaxAndApp;
use App\Models\Law;
use App\Models\Notification;
use Carbon\Carbon;
use App\Function\Notification as NotificationService;

class SendMonthlyUpdatesNotification extends Command
{
    protected $signature = 'notifications:monthly-updates';
    protected $description = 'Send a general notification if there are new items added to Institutions, TaxAndApp, and Different';

    public function handle()
    {
        $startDate = Carbon::now()->subMonth()->startOfMonth();
        $endDate = Carbon::now()->subMonth()->endOfMonth();

        $institutionsCount = Institution::whereBetween('created_at', [$startDate, $endDate])->count();
        $taxAndAppsCount = TaxAndApp::whereBetween('created_at', [$startDate, $endDate])->count();
        
        $differentType1Count = Different::where('type', 1)->whereBetween('created_at', [$startDate, $endDate])->count();
        $differentType2Count = Different::where('type', 2)->whereBetween('created_at', [$startDate, $endDate])->count();
        $differentType3Count = Different::where('type', 3)->whereBetween('created_at', [$startDate, $endDate])->count();
        $differentType4Count = Different::where('type', 4)->whereBetween('created_at', [$startDate, $endDate])->count();
        
        $lawsNewCount = Law::whereBetween('created_at', [$startDate, $endDate])->count();
        $lawsUpdatedCount = Law::whereBetween('updated_at', [$startDate, $endDate])->whereColumn('updated_at', '>', 'created_at')->count();

        $totalNewItems = $institutionsCount + $taxAndAppsCount + 
                         $differentType1Count + $differentType2Count + 
                         $differentType3Count + $differentType4Count +
                         $lawsNewCount + $lawsUpdatedCount;

        if ($totalNewItems > 0) {
            $title = "جديد";
            $titleFr = "New";
            
            $contentParts = [];
            $contentFrParts = [];
            
            if ($institutionsCount > 0) {
                $contentParts[] = "المؤسسات";
                $contentFrParts[] = "Institutions";
            }
            if ($taxAndAppsCount > 0) {
                $contentParts[] = "الأنظمة الجبائية والجزاءات";
                $contentFrParts[] = "Tax systems and penalties";
            }
            if ($differentType1Count > 0) {
                $contentParts[] = "الأسئلة الشائعة";
                $contentFrParts[] = "FAQ";
            }
            if ($differentType2Count > 0) {
                $contentParts[] = "الروابط";
                $contentFrParts[] = "Links";
            }
            if ($differentType3Count > 0) {
                $contentParts[] = "المتفرقات";
                $contentFrParts[] = "Misc";
            }
            if ($differentType4Count > 0) {
                $contentParts[] = "القاموس الجبائي";
                $contentFrParts[] = "Tax Dictionary";
            }
            if ($lawsNewCount > 0 || $lawsUpdatedCount > 0) {
                $contentParts[] = "القوانين";
                $contentFrParts[] = "Laws";
            }

            $content = "تمت إضافة أو تحديث عناصر جديدة في:\n" . implode("، ", $contentParts) . ".";
            $contentFr = "New items have been added or updated in:\n" . implode(", ", $contentFrParts) . ".";

            // Save to Notification model
            $notification = new Notification();
            $notification->title = $title;
            $notification->title_fr = $titleFr;
            $notification->content = $content;
            $notification->content_fr = $contentFr;
            $notification->type_notification = "تحديثات";
            $notification->timer = Carbon::today()->toDateString();
            $notification->Status = 1;
            $notification->save();

            // Send notification to 'user' topic
            $service = new NotificationService();
            $service->sendNotificationToTopic(
                "user",
                $title,
                $content
            );

            $this->info("Notification sent. {$totalNewItems} new items found.");
        } else {
            $this->info('No new items found. No notification sent.');
        }
    }
}
