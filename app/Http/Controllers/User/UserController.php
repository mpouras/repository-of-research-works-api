<?php

namespace App\Http\Controllers\User;

use App\Events\UserEmailChangedEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }

    public function show(Request $request)
    {
        $user = $request->user();

        return response()->json(['user' => new UserResource($user)]);
    }

    public function update(UpdateUserRequest $request)
    {
        $user = $request->user();

        $user->update($request->only(['first_name', 'last_name', 'username', 'email']));

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $timestamp = now()->timestamp;
            $extension = $file->getClientOriginalExtension();
            $filename = "{$user->username}-{$timestamp}.{$extension}";
            $path = $file->storeAs("profile-photo", $filename);

            if ($user->info && $user->info->photo) {
                Storage::delete("{$user->info->photo}");
            }

            $user->info()->update(['photo' => $path]);
        }

        $user->info()->update($request->only(['birthday', 'gender', 'country', 'bio', 'linkedin_url']));

        return response()->json([
            'message' => 'User profile updated successfully!',
            'user' => new UserResource($user),
        ]);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|different:current_password',
            'new_password_confirmation' => 'required|same:new_password',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Incorrect password'], 403);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'message' => 'Password updated successfully',
            'user' => new UserResource($user),
        ]);
    }

    public function changeEmail(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'required|string|min:8',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'email_confirmation' => 'required|same:email',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Incorrect password'], 403);
        }

        $user->update([
           'email' => $request->email,
           'email_verified_at' => null,
        ]);

        event(new UserEmailChangedEvent($user));

        return response()->json([
            'message' => 'Email updated. Please verify your new email.',
            'user' => new UserResource($user),
        ]);
    }

    public function deactivate(LoginUserRequest $request)
    {
        $validatedData = $request->validated();

        $user = User::where('email', $validatedData['identifier'])->orWhere('username', $validatedData['identifier'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'errors' => ['The provided credentials are incorrect.']
            ], 401);
        }

        $user->update(['status' => 'inactive']);

        $user->tokens()->delete();

        return response()->json([
            'message' => 'Your account has been deactivated successfully.',
        ]);
    }

    public function activate(LoginUserRequest $request)
    {

    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|same:password',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Incorrect password'], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Account deleted successfully',
        ]);
    }
}
