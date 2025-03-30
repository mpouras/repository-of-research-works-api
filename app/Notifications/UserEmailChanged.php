<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserEmailChanged extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => "Verify your new Email",
            'message' => 'Hey ' . $notifiable->first_name . '! Your email has been changed. Please check your new email address and verify!',
            'paragraph' => 'Changed at: ' . Carbon::now()->toDayDateTimeString(),
            'action' => 'Resend verification email',
            'action_method' => 'POST',
            'action_url' => route('verification.resend'),
        ];
    }
}
