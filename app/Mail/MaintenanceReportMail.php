<?php

namespace App\Mail;

use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MaintenanceReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Site $site,
        public array $results,
    ) {}

    public function envelope(): Envelope
    {
        $pluginCount = count($this->results['plugins']);
        $themeCount  = count($this->results['themes']);

        return new Envelope(
            subject: "🔧 {$this->site->name} — Bakım Raporu ({$pluginCount} plugin, {$themeCount} tema)",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.maintenance-report');
    }

    public function attachments(): array
    {
        return [];
    }
}
