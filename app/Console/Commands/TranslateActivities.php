<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\Bonusesandcompensations;
use App\Models\Categories_cat_insts;
use App\Models\Categories_differents;
use App\Models\Categories_institutions;
use App\Models\Category;
use App\Models\Different;
use App\Models\DifferentLaw;
use App\Models\Institution;
use App\Models\InstitutionLaw;
use App\Models\Law;
use App\Models\LawTaxAndApp;
use App\Models\NataireActivity;
use App\Models\Notification;
use App\Models\Post;
use App\Models\TaxAndApp;
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

        Appointment::chunk(50, function ($activities) use ($translator, &$count) {

            foreach ($activities as $activity) {

                try {

                    // $activity->name_fr = $translator->translate($activity->name);
                    // $this->info("Translated name ID: {$activity->name_fr}");

                    // $activity->title2_fr = $translator->translate($activity->title2);
                    // $this->info("Translated title2 ID: {$activity->title2_fr}");

                    $activity->dependencies_fr = $translator->translate($activity->dependencies);
                    $this->info("Translated dependencies ID: {$activity->dependencies_fr}");


                    if (!empty($activity->declaration)) {
                        $activity->declaration_fr = $translator->translate($activity->declaration);
                        $this->info("Translated declaration ID: {$activity->declaration_fr}");
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