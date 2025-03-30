<?php

namespace App\Observers;

use App\Models\Issue;

class IssueObserver
{
    public function deleting(Issue $issue)
    {
        $issue->loadMissing('articles');

        foreach ($issue->articles as $article) {
            $article->delete();
        }
    }
}
