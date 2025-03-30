<?php

namespace App\Listeners;

use App\Events\UserCreatedEvent;
use App\Mail\CustomVerificationMail;
use App\Notifications\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class UserCreatedListener
{
    public function handle(UserCreatedEvent $event): void
    {
        $user = $event->user;

        if ($user->role === 'user') {
            $user->info()->create([]);

            Mail::to($user->email)->send(new CustomVerificationMail($user));

            $user->notify(new UserRegistered());
        }
    }
}
