<?php

namespace App\Http\Controllers;

use App\Events\ArticleAuthorsEvent;
use App\Events\ArticleKeywordsEvent;
use App\Helpers\EntitiesFind;
use App\Helpers\Searchable;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArticleController extends Controller implements HasMiddleware
{
    use EntitiesFind, Searchable;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['storeIssueArticles', 'update', 'destroy', 'syncAuthor', 'syncKeyword']),
            new Middleware('role:admin', only: ['storeIssueArticles', 'update', 'destroy', 'syncAuthor', 'syncKeyword']),
        ];
    }

    public function indexIssueArticles(Request $request, $publicationId, $volumeNumber, $issueName)
    {
        $issue = $this->issue($publicationId, $volumeNumber, $issueName);

        $allowedSortFields = ['id', 'title', 'published_date', 'created_at', 'updated_at'];

        $articles = $issue->articles()->sort($request, $allowedSortFields, 'published_date', 'desc')->paginateData($request, 20);

        if ($articles->count() === 0) {
            return response()->json(['message' => 'No Articles Found'], 404);
        }

        return ArticleResource::collection($articles);
    }

    public function storeIssueArticles(StoreArticleRequest $request, $publicationId, $volumeNumber, $issueName)
    {
        $issue = $this->issue($publicationId, $volumeNumber, $issueName);

        $articleData = $request->validated();
        $articleData['published_date'] = Carbon::createFromFormat('d-m-Y', $articleData['published_date'])->format('Y-m-d');
        $articleData['issue_id'] = $issue->id;

        $authors = $articleData['authors'] ?? [];
        $keywords = $articleData['keywords'] ?? [];

        $article = Article::create($articleData);

        event(new ArticleAuthorsEvent($article, $authors, 'attach'));
        event(new ArticleKeywordsEvent($article, $keywords, 'attach'));

        return response()->json([
            'message' => 'Article Created Successfully',
            'article' => new ArticleResource($article)
        ]);
    }

    public function showIssueArticles($publicationId, $volumeNumber, $issueName, $id)
    {
        $article = $this->article($publicationId, $volumeNumber, $issueName, $id);

        return new ArticleResource($article);
    }

    public function index(Request $request)
    {
        $allowedSortFields = ['id', 'title', 'published_date', 'created_at', 'updated_at'];

        $query = $request->input('query');

        $articlesQuery = Article::query();

        if ($query) {
            $articlesQuery = $this->searchArticle($query);
        }

        $articles = $articlesQuery
            ->sort($request, $allowedSortFields, 'id', 'asc')
            ->paginateData($request, 20);

        return ArticleResource::collection($articles);
    }

    public function show($id)
    {
        $article = Article::where('id', $id)->first();
        return new ArticleResource($article);
    }

    public function update(UpdateArticleRequest $request, $id)
    {
        $article = Article::where('id', $id)->first();

        $articleData = $request->validated();

        if (isset($articleData['published_date'])) {
            $articleData['published_date'] = Carbon::createFromFormat('d-m-Y', $articleData['published_date'])->format('Y-m-d');
        }

        $authors = $articleData['authors'] ?? [];
        $keywords = $articleData['keywords'] ?? [];

        $article->update($articleData);

        if (!empty($authors)) {
            event(new ArticleAuthorsEvent($article, $authors, 'attach'));
        }

        if (!empty($keywords)) {
            event(new ArticleKeywordsEvent($article, $keywords, 'attach'));
        }

        return response()->json([
            'message' => 'Article updated successfully',
            'article' => new ArticleResource($article)
        ]);
    }

    public function destroy($id)
    {
        $article = Article::where('id', $id)->first();
        $article->delete();
        return response()->json(['message' => 'Article ' . $article->title . ' deleted successfully.']);
    }

    public function syncAuthor(UpdateArticleRequest $request, $id, string $action)
    {
        $authorData = $request->validated();
        $authors = $authorData['authors'] ?? [];

        $article = Article::where('id', $id)->first();

        if (!empty($authors) && in_array($action, ['attach', 'detach'])) {
            event(new ArticleAuthorsEvent($article, $authors, $action));

            $authorNames = collect($authors)->pluck('name')->join(', ');
            $message = ($action === 'attach')
                ? "Author: {$authorNames} attached to Article {$article->title}"
                : "Author: {$authorNames} detached from Article {$article->title}";

            return response()->json(['message' => $message]);
        }

        return response()->json(['message' => 'Invalid action or no authors provided'], 400);
    }

    public function syncKeyword(UpdateArticleRequest $request, $id, string $action)
    {
        $keywordData = $request->validated();
        $keywords = $keywordData['keywords'] ?? [];

        $article = Article::where('id', $id)->first();

        if (!empty($keywords) && in_array($action, ['attach', 'detach'])) {
            event(new ArticleKeywordsEvent($article, $keywords, $action));

            $keywordNames = collect($keywords)->pluck('name')->join(', ');
            $message = ($action === 'attach')
                ? "Keyword: {$keywordNames} attached to Article {$article->title}"
                : "Keyword: {$keywordNames} detached from Article {$article->title}";

            return response()->json(['message' => $message]);
        }

        return response()->json(['message' => 'Invalid action or no keywords provided'], 400);
    }
}
