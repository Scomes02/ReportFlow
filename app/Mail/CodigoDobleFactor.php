<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CodigoDobleFactor extends Mailable
{
    use SerializesModels;

    public function __construct(
        public string $codigo,
        public User $user
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "ReportFlow — Tu código de verificación de acceso: {$this->codigo}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.codigo-2fa',
        );
    }
}
