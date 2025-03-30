<?php

namespace App\Observers;

use App\Events\ResendVerificationEmailEvent;
use App\Events\UserCreatedEvent;
use App\Events\UserDeletedEvent;
use App\Events\UserEmailChangedEvent;
use App\Events\UserEmailVerifiedEvent;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        event(new UserCreatedEvent($user));
    }

    public function updated(User $user)
    {
        //
    }

    public function deleted(User $user)
    {
        event(new UserDeletedEvent($user));
    }
}
