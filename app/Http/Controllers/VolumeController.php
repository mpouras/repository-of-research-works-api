<?php

namespace App\Http\Controllers;

use App\Helpers\EntitiesFind;
use App\Http\Requests\StoreVolumeRequest;
use App\Http\Requests\UpdateVolumeRequest;
use App\Models\Volume;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VolumeController extends Controller implements HasMiddleware
{
    use EntitiesFind;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
            new Middleware('role:admin', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index($publicationId)
    {
        $publication = $this->publication($publicationId);

        $volumes = $publication->volumes;
        if ($volumes->count() === 0) {
            return response()->json(['message' => 'No Volumes found for ' . $publication->title], 404);
        }

        return response()->json($volumes);
    }

    public function store(StoreVolumeRequest $request, $publicationId)
    {
        $this->publication($publicationId);

        $volumeData = $request->validated();
        $volumeData['publication_id'] = $publicationId;

        $volume = Volume::create($volumeData);

        return response()->json([
            'message' => 'Volume created successfully.',
            'volume' => $volume
        ], 201);
    }

    public function show($publicationId, $number)
    {
        $volume = $this->volume($publicationId, $number);

        return response()->json($volume);
    }

    public function update(UpdateVolumeRequest $request, $publicationId, $number)
    {
        $volume = $this->volume($publicationId, $number);

        $volumeData = $request->validated();
        $volumeData['publication_id'] = $publicationId;

        $volume->update($volumeData);

        return response()->json($volume);
    }

    public function destroy($publicationId, $number)
    {
        $volume = $this->volume($publicationId, $number);

        $volume->delete();

        return response()->json(['message' => 'Volume deleted successfully.']);
    }
}
