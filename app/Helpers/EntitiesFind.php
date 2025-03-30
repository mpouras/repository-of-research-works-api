<?php

namespace App\Helpers;

use App\Models\Article;
use App\Models\Author;
use App\Models\Issue;
use App\Models\Keyword;
use App\Models\Publication;
use App\Models\Publisher;
use App\Models\Volume;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait EntitiesFind
{
    public function publisher($id): Publisher
    {
        $publisher = Publisher::find($id);
        if (!$publisher) {
            throw new ModelNotFoundException('Publisher with id:' . $id . ' not found.');
        }
        return $publisher;
    }

    public function publication($id): Publication
    {
        $publication = Publication::find($id);
        if (!$publication) {
            throw new ModelNotFoundException('Publication with id:' . $id . ' not found.');
        }
        return $publication;
    }

    public function volume($publicationId, $volumeNumber): Volume
    {
        $publication = $this->publication($publicationId);

        $volume = $publication->volumes()->where('number', $volumeNumber)->first();
        if (!$volume) {
            throw new ModelNotFoundException('Volume with number:' . $volumeNumber . ' not found.');
        }
        return $volume;
    }

    public function issue($publicationId, $volumeNumber, $issueName): Issue
    {
        $publicationId = $this->publication($publicationId)->id;
        $volume = $this->volume($publicationId, $volumeNumber);

        $issue = $volume->issues()->where('name', $issueName)->first();
        if (!$issue) {
            throw new ModelNotFoundException('Issue with number:' . $issueName . ' not found.');
        }
        return $issue;
    }

    public function article($publicationId, $volumeNumber, $issueName, $articleId): Article
    {
        $publicationId = $this->publication($publicationId)->id;
        $volumeNumber = $this->volume($publicationId, $volumeNumber)->number;
        $issue = $this->issue($publicationId, $volumeNumber, $issueName);

        $article = $issue->articles()->where('id', $articleId)->first();
        if (!$article) {
            throw new ModelNotFoundException('Article with id:' . $articleId . ' not found.');
        }
        return $article;
    }

    public function author($id): Author
    {
        $author = Author::find($id);
        if (!$author) {
            throw new ModelNotFoundException('Author with id:' . $id . ' not found.');
        }

        return $author;
    }

    public function keyword($name): Keyword
    {
        $keyword = Keyword::where('name', $name)->first();
        if (!$keyword) {
            throw new ModelNotFoundException('Keyword "' . $name . '" not found.');
        }

        return $keyword;
    }
}
