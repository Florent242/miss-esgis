<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompteRestreint extends Mailable
{
    use Queueable, SerializesModels;

    public $prenom;
    public $nom;
    public $email;

    public function __construct($miss)
    {
        $this->prenom = $miss->prenom;
        $this->nom = $miss->nom;
        $this->email = $miss->email;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Compte Restreint - Reine ESGIS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.compte_restreint',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
