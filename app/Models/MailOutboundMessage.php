<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailOutboundMessage extends Model
{
    public const SOURCE_PRODUCT_INQUIRY = 'product_inquiry_request';

    public const STATUS_QUEUED = 'queued';

    public const STATUS_SENT = 'sent';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'admin_id',
        'source_type',
        'source_id',
        'recipient_email',
        'subject',
        'body',
        'status',
        'error_message',
        'attempts',
        'sent_at',
        'failed_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function isRetryable(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }
}
