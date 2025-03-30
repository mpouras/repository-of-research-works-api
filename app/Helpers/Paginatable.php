<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait Paginatable
{
    /**
     * Apply pagination dynamically.
     *
     * @param Builder $query
     * @param Request $request
     * @param int $defaultPerPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function scopePaginateData(Builder $query, Request $request, int $defaultPerPage = 20)
    {
        $perPage = (int) $request->query('per_page', $defaultPerPage);
        $perPage = $perPage >= 10 && $perPage <= 200 ? $perPage : $defaultPerPage;

        return $query->paginate($perPage);
    }
}
