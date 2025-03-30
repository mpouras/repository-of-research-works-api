<?php

namespace App\Http\Controllers;

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
use App\Rules\UniquePublicationPerPublisher;
use Carbon\Carbon;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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

        $validator = Validator::make(
            ['title' => $title],
            [
                'title' => [
                    'required',
                    'string',
                    new UniquePublicationPerPublisher($publishers),
                ],
            ]
        );

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
            $publication->attachPublishers($publicationData['publishers'], $publicationData['scraper']);

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

        foreach ($validPublications as $publicationData) {
            $publication = $this->publication($publicationData['id']);
            $publication->update($publicationData);

            if (isset($publicationData['publishers'])) {
                $publication->attachPublishers($publicationData['publishers'], $publicationData['scraper']);
            }

            $updatedPublications[] = $publication->title;
        }

        $response = ['message' => 'Multiple publications update'];
        if(!empty($skippedPublications)) {
            $response['skipped_publications'] = $skippedPublications;
        }
        if (!empty($updatedPublications)) {
            $response['updated_publications'] = $updatedPublications;
        }
        return response()->json($response, 201);
    }

    public function storeVolumesAndIssuesOfPublication(StoreManyVolumesIssuesRequest $request, $publicationId)
    {
        $this->publication($publicationId);

        $validVolumes = $request->validVolumes();
        $skippedVolumes = $request->skippedVolumes();

        $createdVolumes = [];
        $skippedIssues = [];

        foreach ($validVolumes as $volumeData) {
            $volumeData['publication_id'] = $publicationId;
            $volume = Volume::create($volumeData);

            $volumeWithIssues = [
                'number' => $volume->number,
                'year_published' => $volume->year_published,
                'issues' => []
            ];

            foreach ($volumeData['issues'] as $issueData) {
                $formattedMonthPublished = $this->monthPublished($issueData['month_published'], $volume->year_published);
                $issueData['month_published'] = $formattedMonthPublished;
                $issueData['volume_id'] = $volume->id;

                $issue = Issue::create($issueData);
                $volumeWithIssues['issues'][] = [
                    'name' => $issue->name,
                    'month_published' => $issue->month_published
                ];
            }
            $createdVolumes[] = $volumeWithIssues;
        }

        $response = ['message' => 'Multiple volumes and issues store'];
        if (!empty($skippedVolumes)) {
            $response['skipped_volumes'] = $skippedVolumes;
        }

        if (!empty($createdVolumes)) {
            $response['created_volumes'] = $createdVolumes;
        }

        if (!empty($skippedIssues)) {
            $response['skipped_issues'] = $skippedIssues;
        }
        return response()->json($response, 201);
    }

    public function storeManyVolumes(StoreManyVolumesRequest $request, $publicationId)
    {
        $this->publication($publicationId);

        $validVolumes = $request->validVolumes();
        $skippedVolumes = $request->skippedVolumes();

        $createdVolumes = [];
        foreach ($validVolumes as $volumeData) {
            $volumeData['publication_id'] = $publicationId;
            $volume = Volume::create($volumeData);
            $createdVolumes[] = $volume['number'];
        }

        $response = ['message' => 'Multiple volumes store'];
        if(!empty($skippedVolumes)) {
            $response['skipped_volumes'] = $skippedVolumes;
        }
        if (!empty($createdVolumes)) {
            $response['created_volumes'] = $createdVolumes;
        }
        return response()->json($response, 201);
    }

    public function storeManyIssues(StoreManyIssuesRequest $request, $publicationId, $volumeNumber)
    {
        $volume = $this->volume($publicationId, $volumeNumber);

        $validIssues = $request->validIssues();
        $skippedIssues = $request->skippedIssues();

        $createdIssues = [];
        foreach ($validIssues as $issueData) {
            $formattedMonthPublished = $this->monthPublished($issueData['month_published'], $volume->year_published);
            $issueData['month_published'] = $formattedMonthPublished;
            $issueData['volume_id'] = $volume->id;

            $issue = Issue::create($issueData);
            $createdIssues[] = $issue;
        }

        $response = ['message' => 'Multiple issues store'];
        if(!empty($skippedIssues)) {
            $response['skippedIssues'] = $skippedIssues;
        }
        if (!empty($createdIssues)) {
            $response['createdIssues'] = $createdIssues;
        }
        return response()->json($response, 201);
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

            $article = Article::create($articleData);
            $article->attachAuthorsAndKeywords($articleData);

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
