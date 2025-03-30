<?php

namespace App\Http\Controllers;

use App\Events\PublicationPublishersEvent;
use App\Helpers\EntitiesFind;
use App\Helpers\Searchable;
use App\Http\Requests\StorePublicationRequest;
use App\Http\Requests\UpdatePublicationRequest;
use App\Http\Resources\PublicationResource;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PublicationController extends Controller implements HasMiddleware
{
    use EntitiesFind, Searchable;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store','update', 'destroy', 'syncPublisher']),
            new Middleware('role:admin', only: ['store', 'update', 'destroy', 'syncPublisher']),
        ];
    }

    public function index(Request $request)
    {
        $allowedSortFields = ['id', 'title', 'year_published', 'created_at', 'updated_at'];

        $query = $request->input('query');

        $publicationsQuery = Publication::query();

        if ($query) {
            $publicationsQuery = $this->searchPublication($query);
        }

        $publications = $publicationsQuery
            ->sort($request, $allowedSortFields, 'id', 'asc')
            ->paginateData($request, 20);

        return PublicationResource::collection($publications);
    }

    public function store(StorePublicationRequest $request)
    {
        $publicationData = $request->validated();

        $publishers = $publicationData['publishers'] ?? [];
        unset($publicationData['publishers']);

        $publication = Publication::create($publicationData);

        event(new PublicationPublishersEvent($publication, $publishers, 'attach'));

        return response()->json([
            'message' => 'Publication ' . $publication->title . ' created successfully.',
            'publication' => new PublicationResource($publication)
        ], 201);
    }

    public function show($id)
    {
        $publication = $this->publication($id);
        return new PublicationResource($publication);
    }

    public function update(UpdatePublicationRequest $request, $id)
    {
        $publicationData = $request->validated();

        $publishers = $publicationData['publishers'] ?? [];
        unset($publicationData['publishers']);

        $publication = $this->publication($id);
        $publication->update($publicationData);

        if (!empty($publishers)) {
            event(new PublicationPublishersEvent($publication, $publishers, 'attach'));
        }

        return response()->json([
            'message' => 'Publication ' . $publication->title . ' updated successfully.',
            'publication' => new PublicationResource($publication)
        ], 201);
    }

    public function destroy($id)
    {
        $publication = $this->publication($id);
        $publication->delete();

        return response()->json(['message' => 'Publication ' . $publication->title . ' deleted']);
    }

    public function syncPublisher(UpdatePublicationRequest $request, $id, string $action)
    {
        $publisherData = $request->validated();
        $publishers = $publisherData['publishers'] ?? [];

        $publication = $this->publication($id);

        if (!empty($publishers) && in_array($action, ['attach', 'detach'])) {
            event(new PublicationPublishersEvent($publication, $publishers, $action));

            $publisherNames = collect($publishers)->pluck('name')->join(', ');
            $message = ($action === 'attach')
                ? "Publisher: {$publisherNames} attached to Publication {$publication->title}"
                : "Publisher: {$publisherNames} detached from Publication {$publication->title}";

            return response()->json(['message' => $message]);
        }

        return response()->json(['message' => 'Invalid action or no publishers provided'], 400);
    }
}
