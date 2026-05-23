<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activity;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslateActivities extends Command
{
    protected $signature = 'activities:translate';

    protected $description = 'Translate activities Arabic fields to English';

    public function handle()
    {
        $translator = new GoogleTranslate();
        $translator->setSource('ar');
        $translator->setTarget('en');

        $count = 0;

        Activity::chunk(50, function ($activities) use ($translator, &$count) {

            foreach ($activities as $activity) {

                try {

                    if (!empty($activity->name) && empty($activity->name_fr)) {
                        $activity->name_fr = $translator->translate($activity->name);
                    }

                    if (!empty($activity->body) && empty($activity->body_fr)) {
                        $activity->body_fr = $translator->translate($activity->body);
                    }

                    $activity->save();
                    $count++;

                    $this->info("Translated ID: {$activity->id}");

                } catch (\Exception $e) {
                    $this->error("Error ID {$activity->id}: " . $e->getMessage());
                }
            }
        });

        $this->info("DONE. Total translated: {$count}");

        return 0;
    }
}