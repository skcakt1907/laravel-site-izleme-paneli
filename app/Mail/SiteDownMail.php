<?php

namespace App\Mail;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SiteDownMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Site $site,             // Çöken site bilgisi
        public ?int $statusCode,       // HTTP durum kodu
        public ?string $errorMessage,  // Hata mesajı
        public int $failures,          // Ard arda hata sayısı
    ) {}

    // Mail konusu ve gönderen bilgisi
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "⚠ {$this->site->name} sitesi çöktü!",
        );
    }

    // Mail içeriği
    public function content(): Content
    {
        return new Content(
            view: 'emails.site-down',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
