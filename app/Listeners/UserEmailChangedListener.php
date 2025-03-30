<?php

namespace App\Listeners;

use App\Events\UserEmailChangedEvent;
use App\Mail\CustomVerificationMail;
use App\Notifications\UserEmailChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class UserEmailChangedListener
{
    public function handle(UserEmailChangedEvent $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new CustomVerificationMail($user));

        $user->notify(new UserEmailChanged($user));
    }
}
