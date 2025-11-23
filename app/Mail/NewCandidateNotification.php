<?php

namespace App\Mail;

use App\Models\Miss;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCandidateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public Miss $miss;

    public function __construct(Miss $miss)
    {
        $this->miss = $miss;
    }

    public function build(): self
    {
        return $this
            ->subject('Nouvelle candidature - Reine ESGIS ' . date('Y'))
            ->view('emails.admin.new_candidate')
            ->with([
                'miss' => $this->miss,
            ]);
    }
}
