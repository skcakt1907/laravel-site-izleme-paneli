<?php

namespace App\Mail;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SslExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Site $site,           // İlgili site
        public int $daysRemaining,   // Kalan gün sayısı
        public Carbon $expiresAt,    // Son kullanma tarihi
        public string $issuer,       // Sertifika sağlayıcı
    ) {}

    // Mail konusu
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🔒 {$this->site->name} — SSL sertifikası {$this->daysRemaining} gün içinde sona eriyor",
        );
    }

    // Mail içeriği
    public function content(): Content
    {
        return new Content(
            view: 'emails.ssl-expiring',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
