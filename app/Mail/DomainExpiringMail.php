<?php

namespace App\Mail;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class DomainExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Site $site,
        public int $daysRemaining,
        public Carbon $expiresAt,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->daysRemaining > 0
            ? "🌐 {$this->site->name} — domain {$this->daysRemaining} gün içinde sona eriyor"
            : "🚨 {$this->site->name} — domain süresi dolmuş!";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.domain-expiring');
    }

    public function attachments(): array
    {
        return [];
    }
}
