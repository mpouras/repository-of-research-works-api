<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => "Welcome to " . env('APP_NAME') . '!',
            'message' => 'Welcome ' . $notifiable->first_name . '! Please verify your email address.',
            'paragraph' => 'If your email link is not working, click the "Resend" button below.',
            'action' => 'Resend verification email',
            'action_method' => 'POST',
            'action_url' => route('verification.resend'),
        ];
    }
}
