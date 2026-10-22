<?php

namespace App\Mail;

use App\Models\Estudio;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AvisoInformeListo extends Mailable
{
    use SerializesModels;

    public function __construct(public Estudio $estudio)
    {
        $this->estudio->loadMissing('tipoEstudio');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tu informe ya está listo para retirar',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.aviso-informe-listo',
        );
    }
}
