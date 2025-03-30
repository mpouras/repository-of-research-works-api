<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class UserVerifiedMail extends Mailable
{
    use Queueable, SerializesModels, Dispatchable;

    public $user;
    public $frontendUrl;
    public $appName;

    public function __construct($user)
    {
        $this->user = $user;
        $this->frontendUrl = env('FRONTEND_URL');
        $this->appName = env('APP_NAME');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You are now verified',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verified',
            with: [
                'user' => $this->user,
                'FRONTEND_URL' => $this->frontendUrl,
                'APP_NAME' => $this->appName,
            ],
        );
    }
}
