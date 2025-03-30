<?php

namespace App\Observers;

use App\Models\Publisher;

class PublisherObserver
{
    public function deleting(Publisher $publisher)
    {
        $publications = $publisher->publications;

        $publisher->publications()->detach();

        foreach ($publications as $publication) {
            $publication->load('publishers');
            if ($publication->publishers()->count() === 0) {
                $publication->delete();
            }
        }
    }
}
