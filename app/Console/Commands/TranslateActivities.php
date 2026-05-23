<?php

namespace App\Console\Commands;

use App\Models\Bonusesandcompensations;
use App\Models\Categories_cat_insts;
use App\Models\Categories_differents;
use App\Models\Categories_institutions;
use App\Models\Category;
use App\Models\Different;
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

        Different::chunk(50, function ($activities) use ($translator, &$count) {

            foreach ($activities as $activity) {

                try {

                    // $activity->name_fr = $translator->translate($activity->name);
                    // $this->info("Translated name ID: {$activity->name_fr}");

                    $activity->title_fr = $translator->translate($activity->title);
                    $this->info("Translated title ID: {$activity->title_fr}");

                    $activity->body_fr = $translator->translate($activity->body);
                    $this->info("Translated body ID: {$activity->body_fr}");


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