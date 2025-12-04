<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReviewTerima extends Mailable
{
    use Queueable, SerializesModels;

    public $namaPengunjung;
    public $produk_nama;
    public $rating;
    public $ulasan;
    public $noHpPengunjung;
    public $provinsiPengunjung;

    /**
     * Create a new message instance.
     */
    public function __construct($namaPengunjung, $produk_nama, $rating, $ulasan, $noHpPengunjung = null, $provinsiPengunjung = null)
    {
        $this->namaPengunjung = $namaPengunjung;
        $this->produk_nama = $produk_nama;
        $this->rating = $rating;
        $this->ulasan = $ulasan;
        $this->noHpPengunjung = $noHpPengunjung;
        $this->provinsiPengunjung = $provinsiPengunjung;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Terima Kasih Telah Memberikan Ulasan! ⭐'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.review-terima',
            with: [
                'namaPengunjung' => $this->namaPengunjung,
                'produk_nama' => $this->produk_nama,
                'rating' => $this->rating,
                'ulasan' => $this->ulasan,
                'noHpPengunjung' => $this->noHpPengunjung,
                'provinsiPengunjung' => $this->provinsiPengunjung,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}