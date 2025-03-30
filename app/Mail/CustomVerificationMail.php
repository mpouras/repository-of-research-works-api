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

class CustomVerificationMail extends Mailable
{
    use Queueable, SerializesModels, Dispatchable;

    public $user;
    public $frontendUrl;

    public function __construct($user)
    {
        $this->user = $user;
        $this->frontendUrl = env('FRONTEND_URL');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verify your email address',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.verification',
            with: [
                'user' => $this->user,
                'url' => $this->verificationUrl($this->user),
                'FRONTEND_URL' => $this->frontendUrl,
            ],
        );
    }

    protected function verificationUrl($user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
