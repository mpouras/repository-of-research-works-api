<?php

namespace App\Providers;


use App\Events\ResendVerificationEmailEvent;
use App\Events\UserCreatedEvent;
use App\Events\UserEmailChangedEvent;
use App\Events\UserEmailVerifiedEvent;
use App\Events\UserDeletedEvent;
use App\Listeners\ResendVerificationEmailListener;
use App\Listeners\UserCreatedListener;
use App\Listeners\UserEmailChangedListener;
use App\Listeners\UserEmailVerifiedListener;
use App\Listeners\UserDeletedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

    ];

    public function boot(): void
    {
        parent::boot();
    }
}
