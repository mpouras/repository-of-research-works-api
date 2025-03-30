<?php

namespace App\Observers;

use App\Models\Publication;

class PublicationObserver
{
    public function deleting(Publication $publication)
    {
        $publication->loadMissing('volumes');

        foreach ($publication->volumes as $volume) {
            $volume->delete();
        }

        $publication->publishers()->detach();
    }
}
