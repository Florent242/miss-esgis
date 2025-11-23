<?php

namespace App\Mail;

use App\Models\Miss;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CandidateSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public Miss $miss;

    /**
     * Create a new message instance.
     */
    public function __construct(Miss $miss)
    {
        $this->miss = $miss;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject('Votre candidature a été reçue - Reine ESGIS')
            ->view('emails.candidate_submitted')
            ->with([
                'miss' => $this->miss,
            ]);
    }
}
