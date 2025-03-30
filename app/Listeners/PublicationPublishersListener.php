<?php

namespace App\Listeners;

use App\Events\PublicationPublishersEvent;
use App\Models\Publisher;

class PublicationPublishersListener
{
    public function handle(PublicationPublishersEvent $event)
    {
        if ($event->action === 'attach') {
            $this->attachPublishers($event);
        } elseif ($event->action === 'detach') {
            $this->detachPublishers($event);
        }
    }

    private function attachPublishers(PublicationPublishersEvent $event)
    {
        foreach ($event->publishers as $publisherData) {
            $publisher = Publisher::firstOrCreate(
                ['name' => $publisherData['name']],
                ['scraper' => $publisherData['scraper'] ?? null]
            );

            if (!$event->publication->publishers()->where('publisher_id', $publisher->id)->exists()) {
                $event->publication->publishers()->attach($publisher->id);
            }
        }
    }

    private function detachPublishers(PublicationPublishersEvent $event)
    {
        foreach ($event->publishers as $publisherData) {
            $publisher = Publisher::where('name', $publisherData['name'])->first();

            if ($publisher) {
                $event->publication->publishers()->detach($publisher->id);
            }
        }
    }
}
