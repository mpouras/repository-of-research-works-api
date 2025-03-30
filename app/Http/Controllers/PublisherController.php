<?php

namespace App\Http\Controllers;

use App\Helpers\EntitiesFind;
use App\Helpers\Searchable;
use App\Http\Requests\StorePublisherRequest;
use App\Http\Requests\UpdatePublisherRequest;
use App\Http\Resources\PublicationResource;
use App\Http\Resources\PublisherResource;
use App\Models\Publisher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PublisherController extends Controller implements HasMiddleware
{
    use EntitiesFind, Searchable;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy',]),
            new Middleware('role:admin', only: ['store', 'update', 'destroy',]),
        ];
    }

    public function index(Request $request)
    {
        $allowedSortFields = ['id', 'name', 'created_at', 'updated_at'];

        $query = $request->input('query');

        $publishersQuery = Publisher::query();

        if ($query) {
            $publishersQuery = $this->searchPublisher($query);
        }

        $publishers = $publishersQuery
            ->sort($request, $allowedSortFields, 'id', 'asc')
            ->paginateData($request, 20);

        return response()->json($publishers);
    }

    public function store(StorePublisherRequest $request)
    {
        $validatedData = $request->validated();
        $publisher = Publisher::firstOrCreate($validatedData);
        return response()->json([
            'message' => 'Publisher ' . $publisher->name . ' created',
            'publisher' => new PublisherResource($publisher)
        ], 201);
    }

    public function show($id)
    {
        $publisher = $this->publisher($id);

        return new PublisherResource($publisher);
    }

    public function update(UpdatePublisherRequest $request, $id)
    {
        $publisherData = $request->validated();
        $publisher = $this->publisher($id);

        $publisher->update($publisherData);
        return response()->json([
            'message' => 'Publisher ' . $publisher->name . ' updated',
            'publisher' => new PublisherResource($publisher)
        ]);
    }

    public function destroy($id)
    {
        $publisher = $this->publisher($id);
        $publisher->delete();

        return response()->json([
            'message' => 'Publisher ' . $publisher->name .' along with all associated publications that are not linked to any other publishers deleted successfully.'
        ]);
    }

    public function showPublications($id)
    {
        $publisher = $this->publisher($id);
        $publications = $publisher->publications;

        return response()->json(PublicationResource::collection($publications)->toArray(request()));
    }
}
