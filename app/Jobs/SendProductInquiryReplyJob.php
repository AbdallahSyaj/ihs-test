<?php

namespace App\Jobs;

use App\Models\MailOutboundMessage;
use App\Models\ProductInquiryRequest;
use App\Services\Mail\ProductInquiryReplyMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class SendProductInquiryReplyJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        public readonly int $outboundMessageId,
    ) {}

    public function handle(ProductInquiryReplyMailer $mailer): void
    {
        $outbound = MailOutboundMessage::query()->findOrFail($this->outboundMessageId);

        if ($outbound->status === MailOutboundMessage::STATUS_SENT) {
            return;
        }

        $outbound->increment('attempts');

        try {
            $mailer->send($outbound);
        } catch (\Throwable $exception) {
            $outbound->forceFill([
                'status' => MailOutboundMessage::STATUS_FAILED,
                'failed_at' => now(),
                'error_message' => Str::limit($exception->getMessage(), 500),
            ])->save();

            return;
        }

        $outbound->forceFill([
            'status' => MailOutboundMessage::STATUS_SENT,
            'sent_at' => now(),
            'failed_at' => null,
            'error_message' => null,
        ])->save();

        if ($outbound->source_type === MailOutboundMessage::SOURCE_PRODUCT_INQUIRY) {
            ProductInquiryRequest::query()
                ->whereKey($outbound->source_id)
                ->where('status', '!=', ProductInquiryRequest::STATUS_ARCHIVED)
                ->update(['status' => ProductInquiryRequest::STATUS_REPLIED]);
        }
    }

    public function failed(?\Throwable $exception): void
    {
        $outbound = MailOutboundMessage::query()->find($this->outboundMessageId);

        if ($outbound === null || $outbound->status === MailOutboundMessage::STATUS_SENT) {
            return;
        }

        $message = $exception?->getMessage() ?? 'The message could not be sent.';

        $outbound->forceFill([
            'status' => MailOutboundMessage::STATUS_FAILED,
            'failed_at' => now(),
            'error_message' => Str::limit($message, 500),
        ])->save();
    }
}
