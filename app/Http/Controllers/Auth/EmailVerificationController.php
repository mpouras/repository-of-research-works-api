<?php

namespace App\Http\Controllers\Auth;

use App\Events\ResendVerificationEmailEvent;
use App\Events\UserEmailVerifiedEvent;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EmailVerificationController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum', only: ['resendVerificationEmail']),
            new Middleware('throttle:6,1', only: ['resendVerificationEmail']),
            new Middleware('signed', only: ['verify']),
        ];
    }
    public function verify(Request $request, $id, $hash)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid verification link.'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified.']);
        }

        $user->markEmailAsVerified();

        event(new UserEmailVerifiedEvent($user));

        event(new Verified($user));

        return response()->json(['message' => 'Email verified successfully!']);
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.']);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.']);
        }

        event(new ResendVerificationEmailEvent($user));

        return response()->json(['message' => 'Verification link sent!']);
    }
}
