<?php

namespace App\Helpers;

use App\Models\{Publisher, Publication, User, Volume, Issue, Article, Author, Keyword};
use Illuminate\Http\Request;

trait Searchable
{
    use EntitiesFind, Sortable;

    protected function searchUser($query)
    {
        return User::where('first_name', 'like', "%$query%")
            ->orWhere('last_name', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->orWhere('username', 'like', "%$query%")
            ->orWhere('id', $query)
            ->orWhereRaw("SOUNDEX(first_name) = SOUNDEX(?)", [$query])
            ->orWhereRaw("SOUNDEX(last_name) = SOUNDEX(?)", [$query])
            ->orWhereRaw("SOUNDEX(email) = SOUNDEX(?)", [$query])
            ->orWhereRaw("SOUNDEX(username) = SOUNDEX(?)", [$query]);
    }

    protected function searchPublisher($query)
    {
        return Publisher::where('name', 'like', "%$query%")
            ->orWhere('id', $query)
            ->orWhereRaw("SOUNDEX(name) = SOUNDEX(?)", [$query]);
    }

    protected function searchPublication($query)
    {
        return Publication::where('title', 'like', "%$query%")
            ->orWhere('id', $query)
            ->orWhereRaw("SOUNDEX(title) = SOUNDEX(?)", [$query]);
    }

    protected function searchVolume($query, $validatedData = [])
    {
        if (isset($validatedData['publication_id'])) {
            return $this->publication($validatedData['publication_id'])
                ->volumes()
                ->where('number', (int) $query);
        }

        return Volume::where('number', (int) $query);
    }

    protected function searchIssue($query, $validatedData = [])
    {
        if (isset($validatedData['publication_id'], $validatedData['volume_number'])) {
            return $this->volume($validatedData['publication_id'], $validatedData['volume_number'])
                ->issues()
                ->where('name', 'like', "%$query%");
        }

        return Issue::where('name', 'like', "%$query%");
    }

    protected function searchArticle($query, $validatedData = [])
    {
        if (isset($validatedData['publication_id'], $validatedData['volume_number'], $validatedData['issue_name'])) {
            return $this->issue($validatedData['publication_id'], $validatedData['volume_number'], $validatedData['issue_name'])
                ->articles()
                ->where('title', 'like', "%$query%")
                ->orWhere('id', $query)
                ->orWhereRaw("SOUNDEX(title) = SOUNDEX(?)", [$query]);
        }

        return Article::where('title', 'like', "%$query%")
            ->orWhere('id', $query)
            ->orWhereRaw("SOUNDEX(title) = SOUNDEX(?)", [$query]);
    }

    protected function searchAuthor($query)
    {
        return Author::where('name', 'like', "%$query%")
            ->orWhere('id', $query)
            ->orWhereRaw("SOUNDEX(name) = SOUNDEX(?)", [$query])
            ->orWhereRaw("SOUNDEX(university) = SOUNDEX(?)", [$query]);
    }

    protected function searchKeyword($query)
    {
        return Keyword::where('name', 'like', "%$query%")
            ->orWhere('id', $query)
            ->orWhereRaw("SOUNDEX(name) = SOUNDEX(?)", [$query]);
    }
}
