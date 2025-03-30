<?php

namespace App\Observers;

use App\Models\Volume;

class VolumeObserver
{
    public function deleting(Volume $volume)
    {
        $volume->loadMissing('issues');

        foreach ($volume->issues as $issue) {
            $issue->delete();
        }
    }
}
