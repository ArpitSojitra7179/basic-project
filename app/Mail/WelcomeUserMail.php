<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class WelcomeUserMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $userName;
    public string $appName;
    public int $userId;
    public int $user;

    public function __construct($userName, $userId, $user)
    {
        $this->userName = $userName;
        $this->appName = config('app.name');
        $this->userId = $userId;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome To ' . $this->appName,
            cc: ['adell.sawayn@ethereal.email'],
            bcc: ['mohan8552@gmail.com'],
            tags: ['Welcome'],
            metadata: [
                'user_id' => $this->userId,
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'email.welcome-user',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(
                base_path('/storage/logs/laravel.log'),
            ),
        ];
    }
}
