<?php

namespace App\Http\Controllers\User;

use App\Helpers\Searchable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Article;
use App\Models\Author;
use App\Models\Publication;
use App\Models\Publisher;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminController extends Controller implements HasMiddleware
{
    use Searchable;

    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum'),
            new Middleware('role:admin'),
        ];
    }

    public function index(Request $request)
    {
        $allowedSortFields = ['id', 'first_name', 'email', 'role', 'created_at', 'updated_at'];

        $query = $request->input('query');

        $usersQuery = User::where('id', '!=', auth()->id());

        if ($query) {
            $usersQuery = $this->searchUser($query);
        }

        $users = $usersQuery
            ->sort($request, $allowedSortFields, 'id', 'asc')
            ->paginateData($request, 20);

        return UserResource::collection($users);
    }

    public function show($userId)
    {
        $user = User::where('id', '!=', auth()->id())->findOrFail($userId);
        return new UserResource($user);
    }

    public function dashboard()
    {
        $models = [
            'users' => User::class,
            'publishers' => Publisher::class,
            'publications' => Publication::class,
            'articles' => Article::class,
            'authors' => Author::class,
        ];

        $dashboardData = [];

        foreach ($models as $key => $model) {
            $dashboardData[$key] = [
                'count' => $model::count(),
                'weekCount' => $model::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()])->count(),
                'todayCount' => $model::whereBetween('created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()])->count(),
                'latest' => $model::orderBy('created_at', 'desc')->limit(3)->get(),
            ];
        }

        return response()->json($dashboardData);
    }

    public function updateUser(AdminUpdateUserRequest $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validatedData = $request->validated();

        $user->update($validatedData);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => new UserResource($user)
        ]);
    }

    public function deleteUser($userId)
    {
        $user = User::findOrFail($userId);

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
