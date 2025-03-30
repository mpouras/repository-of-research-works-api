<?php

namespace App\Http\Controllers;

use App\Helpers\EntitiesFind;
use App\Helpers\Searchable;
use App\Http\Resources\AuthorResource;
use App\Models\Author;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AuthorController extends Controller implements HasMiddleware
{
    use EntitiesFind, Searchable;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['store', 'update', 'destroy']),
            new Middleware('role:admin', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index(Request $request)
    {
        $allowedSortFields = ['id', 'name', 'created_at', 'updated_at'];

        $query = $request->input('query');

        $authorsQuery = Author::query();

        if ($query) {
            $authorsQuery = $this->searchAuthor($query);
        }

        $authors = $authorsQuery
            ->sort($request, $allowedSortFields, 'id', 'asc')
            ->paginateData($request, 20);

        return response()->json($authors);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'university' => 'nullable|string',
            'profile_link' => 'required|url|unique:authors,profile_link',
            'orcid_link' => 'nullable|url|unique:authors,orcid_link',
        ]);

        $author = Author::create($validatedData);

        return response()->json([
            'message' => 'Author created successfully',
            'author' => $author
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $allowedSortFields = ['id', 'title', 'published_date', 'created_at', 'updated_at'];

        $author = $this->author($id);

        $articles = $author->articles()
            ->select('articles.*')
            ->sort($request, $allowedSortFields, 'id', 'desc')
            ->paginateData($request, 20);

        $author->articles_paginated = $articles;

        return new AuthorResource($author);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string',
            'university' => 'nullable|string',
            'profile_link' => 'nullable|url|unique:authors,profile_link',
            'orcid_link' => 'nullable|url|unique:authors,orcid_link',
        ]);

        $author = $this->author($id);
        $author->update($validatedData);

        return response()->json([
            'message' => 'Author updated successfully',
            'author' => $author
        ]);
    }

    public function destroy($id)
    {
        $author = $this->author($id);

        $author->delete();
        return response()->json(['message' =>'Author ' . $author['name'] . ' deleted successfully.']);
    }
}
