<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
trait Sortable
{
    /**
     * Apply sorting dynamically.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $allowedSortFields
     * @param string|null $defaultSortField
     * @param string $defaultSortOrder
     * @return Builder
     */
    public function scopeSort(Builder $query, Request $request, array $allowedSortFields, string $defaultSortField = null, string $defaultSortOrder = 'asc')
    {
        $sortBy = $request->query('sort_by', $defaultSortField);
        $sortOrder = $request->query('sort_order', $defaultSortOrder);

        if ($sortBy && in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query;
    }
}
