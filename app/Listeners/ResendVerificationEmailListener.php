<?php

namespace App\Listeners;

use App\Events\ResendVerificationEmailEvent;
use App\Mail\CustomVerificationMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class ResendVerificationEmailListener
{
    public function handle(ResendVerificationEmailEvent $event): void
    {
        $user = $event->user;

        Mail::to($user->email)->send(new CustomVerificationMail($user));
    }
}
