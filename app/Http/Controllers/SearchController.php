<?php

namespace App\Http\Controllers;

use App\Helpers\EntitiesFind;
use App\Helpers\Searchable;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\AuthorResource;
use App\Http\Resources\IssueResource;
use App\Http\Resources\KeywordResource;
use App\Http\Resources\PublicationResource;
use App\Http\Resources\PublisherResource;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use EntitiesFind, Searchable;

    protected $allowedSortFields = [
        'publisher' => ['name', 'id', 'created_at', 'updated_at'],
        'publication' => ['year_published', 'id', 'title', 'created_at', 'updated_at'],
        'volume' => ['number', 'id', 'created_at', 'updated_at'],
        'issue' => ['name', 'id', 'created_at', 'updated_at'],
        'article' => ['title', 'id', 'published_date', 'created_at', 'updated_at'],
        'author' => ['name', 'id', 'created_at', 'updated_at'],
        'keyword' => ['name', 'id', 'created_at', 'updated_at'],
    ];

    protected function getResourceForModel($model, $modelResults)
    {
        $resourceMappings = [
            'publisher' => PublisherResource::collection($modelResults),
            'publication' => PublicationResource::collection($modelResults),
            'volume' => $modelResults,
            'issue' => IssueResource::collection($modelResults),
            'article' => ArticleResource::collection($modelResults),
            'author' => AuthorResource::collection($modelResults),
            'keyword' => KeywordResource::collection($modelResults),
        ];

        return $resourceMappings[$model];
    }

    public function search(Request $request)
    {
        $validatedData = $request->validate([
            'query' => 'required|string|max:255|min:2',
            'model' => 'required|string|in:publisher,publication,volume,issue,article,author,keyword',
        ]);

        $query = trim($validatedData['query']);
        $model = $validatedData['model'];

        $allowedSortFields = $this->allowedSortFields[$model] ?? [];

        $searchMappings = [
            'publisher' => fn() => $this->searchPublisher($query),
            'publication' => fn() => $this->searchPublication($query),
            'volume' => fn() => $this->searchVolume($query),
            'issue' => fn() => $this->searchIssue($query),
            'article' => fn() => $this->searchArticle($query),
            'author' => fn() => $this->searchAuthor($query),
            'keyword' => fn() => $this->searchKeyword($query),
        ];

        $queryBuilder = $searchMappings[$model]();

        $modelResults = $queryBuilder
            ->sort($request, $allowedSortFields, $allowedSortFields[0], 'desc')
            ->paginateData($request, 20);

        return $this->getResourceForModel($model, $modelResults);
    }

    public function searchEntities(Request $request)
    {
        $validatedData = $request->validate([
            'query' => 'nullable|string|max:255',
            'model' => 'required|string|in:publisher,publication,volume,issue,article,author,keyword',
            'publication_id' => 'nullable|integer',
            'volume_number' => 'nullable|integer',
            'issue_name' => 'nullable|string|max:255',
        ]);

        $query = trim($validatedData['query']);
        $model = $validatedData['model'];

        $searchMappings = [
            'publisher' => fn() => $this->searchPublisher($query)->get(),
            'publication' => fn() => $this->searchPublication($query)->get(),
            'volume' => fn() => $this->searchVolume($query, $validatedData)->get(),
            'issue' => fn() => $this->searchIssue($query, $validatedData)->get(),
            'article' => fn() => $this->searchArticle($query, $validatedData)->get(),
            'author' => fn() => $this->searchAuthor($query)->get(),
            'keyword' => fn() => $this->searchKeyword($query)->get(),
        ];

        return response()->json([
            $model => $searchMappings[$model]() ?? []
        ]);
    }
}
