<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserAccountMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public ?string $plainPassword;
    public bool $isUpdate;

    public function __construct(
        User $user,
        ?string $plainPassword = null,
        bool $isUpdate = false
    ) {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->isUpdate = $isUpdate;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->isUpdate
                ? 'Your Account Has Been Updated'
                : 'Your Account Has Been Created'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.email',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
