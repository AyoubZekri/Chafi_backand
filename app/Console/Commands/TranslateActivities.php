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

        Activity::chunkById(50, function ($activities) use ($translator, &$count) {

            foreach ($activities as $activity) {

                try {

                    $data = [];

                    if (filled($activity->name) && !filled($activity->name_fr)) {
                        $data['name_fr'] = $translator->translate($activity->name);
                    }

                    if (filled($activity->body) && !filled($activity->body_fr)) {
                        $data['body_fr'] = $translator->translate($activity->body);
                    }

                    if (!empty($data)) {
                        $activity->update($data);
                        $count++;
                        $this->info("Translated ID: {$activity->id}");
                    }

                } catch (\Exception $e) {
                    $this->error("Error ID {$activity->id}: " . $e->getMessage());
                }
            }
        });

        $this->info("DONE. Total translated: {$count}");
    }
}