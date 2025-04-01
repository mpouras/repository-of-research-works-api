<?php

namespace App\Http\Controllers;

use App\Events\ArticleAuthorsEvent;
use App\Events\ArticleKeywordsEvent;
use App\Events\PublicationPublishersEvent;
use App\Helpers\EntitiesFind;
use App\Http\Requests\Scraper\StoreManyArticlesRequest;
use App\Http\Requests\Scraper\StoreManyIssuesRequest;
use App\Http\Requests\Scraper\StoreManyPublicationsRequest;
use App\Http\Requests\Scraper\StoreManyVolumesIssuesRequest;
use App\Http\Requests\Scraper\StoreManyVolumesRequest;
use App\Http\Requests\Scraper\UpdateManyPublicationsRequest;
use App\Http\Resources\ScraperPublicationsResource;
use App\Models\Article;
use App\Models\Issue;
use App\Models\Publication;
use App\Models\Publisher;
use App\Models\Volume;
use App\Rules\MinimumVolumePublishedYear;
use App\Rules\SequentialVolumeNumber;
use App\Rules\UniqueArticlePerIssue;
use App\Rules\UniqueIssueNamePerVolume;
use App\Rules\UniquePublicationPerPublisher;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ScraperController extends Controller implements HasMiddleware
{
    use EntitiesFind;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum'),
            new Middleware('role:admin'),
        ];
    }

    private function skipDuplicate(array $publicationData, array &$processedTitles): bool
    {
        $title = $publicationData['title'];
        $publishers = $publicationData['publishers'];

        if (in_array($title, $processedTitles, true)) {
            return true;
        }

        foreach ($publishers as $publisher) {
            $validator = Validator::make(
                ['title' => $title],
                [
                    'title' => [
                        'required',
                        'string',
                        new UniquePublicationPerPublisher($publisher),
                    ],
                ]
            );

            if ($validator->fails()) {
                return true;
            }
        }

        if ($validator->fails()) {
            return true;
        }

        $processedTitles[] = $title;
        return false;
    }

    public function monthPublished(int $month_published, int $year_published): string
    {
        $month = str_pad($month_published, 2, '0', STR_PAD_LEFT);

        return "{$month}-{$year_published}";
    }

    private function processVolume($publication, array $volumeData)
    {
        $volume = $publication->volumes()->where('number', $volumeData['number'])->first();

        if (!$volume) {
            $validator = Validator::make($volumeData, [
                'number' => new SequentialVolumeNumber($publication),
                'year_published' => new MinimumVolumePublishedYear($publication),
            ]);

            if ($validator->fails()) {
                return ['skipped' => $volumeData['number'], 'volume' => null];
            }

            $volume = $publication->volumes()->create($volumeData);
        }

        return ['skipped' => null, 'volume' => $volume];
    }

    private function processIssue($volume, array $issueData)
    {
        $issue = $volume->issues()->where('name', $issueData['name'])->first();

        if (!$issue) {
            $validator = Validator::make($issueData, [
                'name' => new UniqueIssueNamePerVolume($volume->id)
            ]);

            if ($validator->fails()) {
                return ['skipped' => $issueData['name'], 'issue' => null];
            }

            $issue = $volume->issues()->create($issueData);
        }

        return ['skipped' => null, 'issue' => $issue];
    }

    private function processArticle($issue, array $articleData)
    {
        $validator = Validator::make($articleData, [
            'title' => new UniqueArticlePerIssue($issue->id)
        ]);

        if ($validator->fails()) {
            return ['skipped' => $articleData['title'], 'article' => null];
        }

        if (!empty($articleData['published_date'])) {
            $articleData['published_date'] = Carbon::createFromFormat('d-m-Y', $articleData['published_date'])->format('Y-m-d');
        } else {
            $articleData['published_date'] = null;
        }

        $authors = $articleData['authors'] ?? [];
        $keywords = $articleData['keywords'] ?? [];

        $article = $issue->articles()->create($articleData);

        event(new ArticleAuthorsEvent($article, $authors, 'attach'));
        event(new ArticleKeywordsEvent($article, $keywords, 'attach'));

        return ['skipped' => null, 'article' => $article];
    }

    public function indexPublications($scraper)
    {
        $publications = Publisher::where('scraper', $scraper)
            ->with('publications')
            ->get()
            ->pluck('publications')
            ->flatten()
            ->unique('id');

        return response()->json(ScraperPublicationsResource::collection($publications));
    }

    public function storeManyPublications(StoreManyPublicationsRequest $request)
    {
        $validPublications = $request->validPublications();
        $skippedPublications = $request->skippedPublications();

        $storedPublications = [];
        $processedTitles = [];

        foreach ($validPublications as $publicationData) {
            if ($this->skipDuplicate($publicationData, $processedTitles)) {
                continue;
            }

            $publication = Publication::create($publicationData);

            event(new PublicationPublishersEvent($publication, $publicationData['publishers'], 'attach'));

            $storedPublications[] = $publication->title;
        }

        $response = ['message' => 'Multiple publications store'];
        if(!empty($skippedPublications)) {
            $response['skipped_publications'] = $skippedPublications;
        }
        if (!empty($storedPublications)) {
            $response['created_publications'] = $storedPublications;
        }
        return response()->json($response, 201);
    }

    public function updateManyPublications(UpdateManyPublicationsRequest $request)
    {
        $validPublications = $request->validPublications();
        $skippedPublications = $request->skippedPublications();
        $updatedPublications = [];
        $skippedVolumes = [];
        $skippedIssues = [];
        $skippedArticles = [];

        foreach ($validPublications as $publicationData) {
            $publication = $this->publication($publicationData['id']);
            $publication->update($publicationData);

            if (isset($publicationData['publishers'])) {
                event(new PublicationPublishersEvent($publication, $publicationData['publishers'], 'attach'));
            }

            if (isset($publicationData['volumes'])) {
                foreach ($publicationData['volumes'] as $volumeData) {
                    $volumeData['publication_id'] = $publication->id;
                    $volumeResult = $this->processVolume($publication, $volumeData);

                    if ($volumeResult['skipped']) {
                        $skippedVolumes[] = $volumeResult['skipped'];
                    }

                    if ($volumeResult['volume'] && isset($volumeData['issues'])) {
                        foreach ($volumeData['issues'] as $issueData) {
                            $issueResult = $this->processIssue($volumeResult['volume'], $issueData);

                            if ($issueResult['skipped']) {
                                $skippedIssues[] = $issueResult['skipped'];
                            }

                            if ($issueResult['issue'] && isset($issueData['articles'])) {
                                foreach ($issueData['articles'] as $articleData) {
                                    $articleResult = $this->processArticle($issueResult['issue'], $articleData);

                                    if ($articleResult['skipped']) {
                                        $skippedArticles[] = $articleResult['skipped'];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $updatedPublications[] = $publication->title;
        }

        return response()->json([
            'message' => 'Multiple publications update',
            'skipped_publications' => $skippedPublications,
            'updated_publications' => $updatedPublications,
            'skipped_volumes' => $skippedVolumes,
            'skipped_issues' => $skippedIssues,
            'skipped_articles' => $skippedArticles,
        ], 201);
    }

    public function storeManyArticles(StoreManyArticlesRequest $request, $publicationId, $volumeNumber, $issueName)
    {
        $issue = $this->issue($publicationId, $volumeNumber, $issueName);

        $validArticles = $request->validArticles();
        $skippedArticles = $request->skippedArticles();

        $createdArticles = [];
        foreach ($validArticles as $articleData) {
            if (!empty($articleData['published_date'])) {
                $articleData['published_date'] = Carbon::createFromFormat('d-m-Y', $articleData['published_date'])->format('Y-m-d');
            } else {
                $articleData['published_date'] = null;
            }
            $articleData['issue_id'] = $issue->id;

            $authors = $articleData['authors'] ?? [];
            $keywords = $articleData['keywords'] ?? [];

            $article = Article::create($articleData);

            event(new ArticleAuthorsEvent($article, $authors, 'attach'));
            event(new ArticleKeywordsEvent($article, $keywords, 'attach'));

            $createdArticles[] = $article->title;
        }

        $response = ['message' => 'Multiple articles store'];
        if(!empty($skippedArticles)) {
            $response['skipped_articles'] = $skippedArticles;
        }
        if (!empty($createdArticles)) {
            $response['created_articles'] = $createdArticles;
        }
        return response()->json($response, 201);
    }
}
