<?php

namespace App\Listeners;

use App\Events\UserEmailVerifiedEvent;
use App\Mail\UserVerifiedMail;
use App\Notifications\UserVerified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class UserEmailVerifiedListener
{
    public function handle(UserEmailVerifiedEvent $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new UserVerifiedMail($user));

        $user->notify(new UserVerified());
    }
}
