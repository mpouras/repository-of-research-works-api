<?php

namespace App\Http\Controllers;

use App\Helpers\EntitiesFind;
use App\Helpers\Searchable;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\KeywordResource;
use App\Models\Keyword;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    use EntitiesFind, Searchable;

    public function index(Request $request)
    {
        $allowedSortFields = ['id', 'articles_count', 'name', 'created_at'];

        $query = $request->input('query');

        $keywordsQuery = Keyword::query();

        if ($query) {
            $keywordsQuery = $this->searchKeyword($query);
        }

        $keywords = $keywordsQuery
            ->withCount('articles')
            ->sort($request, $allowedSortFields, 'articles_count', 'desc')
            ->paginateData($request, 20);

        return response()->json($keywords);
    }

    public function show(Request $request, $name)
    {
        $allowedSortFields = ['id', 'title', 'published_date', 'created_at', 'updated_at'];

        $keyword = $this->keyword($name);

        $articles = $keyword->articles()
            ->select('articles.*')
            ->sort($request, $allowedSortFields, 'id', 'desc')
            ->paginateData($request, 20);

        $keyword->articles_paginated = $articles;

        return new KeywordResource($keyword);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:2',
        ]);

        $keyword = Keyword::create($validatedData);

        return response()->json([
            'message' => 'Keyword created successfully',
            'keyword' => $keyword
        ]);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|min:2',
        ]);

        $keyword = Keyword::find($id);
        $keyword = $this->keyword($keyword->name);

        $keyword->update($validatedData);

        return response()->json([
            'message' => 'Keyword updated successfully',
            'keyword' => $keyword
        ]);
    }

    public function destroy($name)
    {
        $keyword = $this->keyword($name);

        $keyword->delete();
        return response()->json(['message' => 'Keyword "'. $keyword->name . '" deleted successfully'  ]);
    }
}
