<?php

namespace App\Services\Mail;

use App\Mail\ProductInquiryReplyMailable;
use App\Models\MailOutboundMessage;
use Illuminate\Support\Facades\Mail;

class ProductInquiryReplyMailer
{
    public function send(MailOutboundMessage $outbound): void
    {
        Mail::to($outbound->recipient_email)->send(
            new ProductInquiryReplyMailable(
                replySubject: $outbound->subject,
                replyBody: $outbound->body,
            )
        );
    }
}
