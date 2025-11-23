<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CandidatureRejetee extends Mailable
{
    use Queueable, SerializesModels;

    public $prenom;
    public $nom;

    public function __construct($candidate)
    {
        $this->prenom = $candidate->prenom;
        $this->nom = $candidate->nom;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Réponse à votre candidature - Reine ESGIS ' . date('Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.candidature_rejete',
            with: [
                'prenom' => $this->prenom,
                'nom' => $this->nom,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
