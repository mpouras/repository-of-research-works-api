<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserLibraryResource;
use App\Models\UserLibrary;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserLibraryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['index', 'show', 'store', 'destroy']),
            new Middleware('role:user', only: ['show', 'store', 'destroy']),
            new Middleware('role:admin', only: ['index']),
        ];
    }

    public function index()
    {
        $userLibraries = UserLibrary::with(['user', 'article'])->get();
        return response()->json($userLibraries);
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $userLibrary = $user->library()->with(['article'])->get();

        return UserLibraryResource::collection($userLibrary);
    }

    public function store(Request $request)
    {
        $request->validate([
            'article_id' => 'required|exists:articles,id'
        ]);

        $user = $request->user();

        $exists = UserLibrary::where('user_id', $user->id)->where('article_id', $request->article_id)->exists();
        if ($exists) {
            return response()->json(['message' => 'Article already in library.'], 409);
        }

        $userLibrary = UserLibrary::create([
            'user_id' => $user->id,
            'article_id' => $request->article_id
        ]);

        return response()->json([
            'message' => 'Article added to library',
            'data' => new UserLibraryResource($userLibrary)
        ], 201);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $userLibrary = UserLibrary::where('user_id', $user->id)->where('article_id', $id)->first();
        if (!$userLibrary) {
            return response()->json(['message' => 'Article not found in your library'], 404);
        }

        $userLibrary->delete();
        return response()->json(['message' => 'Article removed successfully from library'], 200);
    }
}
