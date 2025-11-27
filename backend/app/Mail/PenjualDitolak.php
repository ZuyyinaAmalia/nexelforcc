<?php

namespace App\Mail;

use App\Models\Penjual;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PenjualDitolak extends Mailable
{
    use Queueable, SerializesModels;

    public $penjual;

    /**
     * Create a new message instance.
     */
    public function __construct(Penjual $penjual)
    {
        $this->penjual = $penjual;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Pemberitahuan: Pendaftaran Akun Anda Ditolak');
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ditolak',
            with: [
                'penjual' => $this->penjual,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
