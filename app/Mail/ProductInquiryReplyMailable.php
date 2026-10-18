<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProductInquiryReplyMailable extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $replySubject,
        public readonly string $replyBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->replySubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.product-inquiry-reply-text',
        );
    }
}
