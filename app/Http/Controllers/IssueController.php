<?php

namespace App\Http\Controllers;

use App\Helpers\EntitiesFind;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Http\Resources\IssueResource;
use App\Models\Issue;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class IssueController extends Controller implements HasMiddleware
{
    use EntitiesFind;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
            new Middleware('role:admin', only: ['store','update', 'destroy']),
        ];
    }

    public function monthPublished(int $month_published, int $year_published): string
    {
        $month = str_pad($month_published, 2, '0', STR_PAD_LEFT);

        return "{$month}-{$year_published}";
    }

    public function index($publicationId, $volumeNumber)
    {
        $volume = $this->volume($publicationId, $volumeNumber);

        $issues = $volume->issues;
        if ($issues->count() === 0) {
            return response()->json(['message' => 'No Issues found'], 404);
        }

        return IssueResource::collection($issues);
    }

    public function store(StoreIssueRequest $request, $publicationId, $volumeNumber)
    {
        $volume = $this->volume($publicationId, $volumeNumber);

        $issueData = $request->validated();
        $formattedMonthPublished = $this->monthPublished($issueData['month_published'], $volume->year_published);
        $issueData['month_published'] = $formattedMonthPublished;
        $issueData['volume_id'] = $volume->id;

        $issue = Issue::create($issueData);

        return response()->json([
            'message' => 'Issue created successfully.',
            'issue' => $issue
        ], 201);
    }

    public function show($publicationId, $volumeNumber, $name)
    {
        $issue = $this->issue($publicationId, $volumeNumber, $name);

        return response()->json($issue);
    }

    public function update(UpdateIssueRequest $request, $publicationId, $volumeNumber, $name)
    {
        $issue = $this->issue($publicationId, $volumeNumber, $name);

        $volume = $issue->volume();

        $issueData = $request->validated();
        $formattedMonthPublished = $this->monthPublished($issueData['month_published'], $volume->year_published);
        $issueData['month_published'] = $formattedMonthPublished;
        $issueData['volume_id'] = $volume->id;

        $issue->update($issueData);

        return response()->json(['message' => 'Issue updated successfully.', 'data' => $issue]);
    }

    public function destroy($publicationId, $volumeNumber, $name)
    {
        $issue = $this->issue($publicationId, $volumeNumber, $name);

        $issue->delete();

        return response()->json(['message' => 'Issue deleted successfully.']);
    }
}
