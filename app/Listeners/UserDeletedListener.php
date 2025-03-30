<?php

namespace App\Listeners;

use App\Events\UserDeletedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UserDeletedListener
{
    public function handle(UserDeletedEvent $event): void
    {
        $user = $event->user;

        $user->info()->delete();
        $user->library()->delete();
        $user->notifications()->delete();

        Log::info("User deleted: " . $user->id);
    }
}
