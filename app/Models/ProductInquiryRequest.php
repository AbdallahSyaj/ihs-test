<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductInquiryRequest extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';

    public const STATUS_REPLIED = 'replied';

    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'items',
        'status',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function outboundMessages(): HasMany
    {
        return $this->hasMany(MailOutboundMessage::class, 'source_id')
            ->where('mail_outbound_messages.source_type', MailOutboundMessage::SOURCE_PRODUCT_INQUIRY)
            ->orderByDesc('created_at');
    }

    public function markAsRead(): void
    {
        if ($this->read_at !== null) {
            return;
        }

        $this->forceFill(['read_at' => now()])->save();
    }

    public function productSummary(): string
    {
        $items = is_array($this->items) ? $this->items : [];
        $count = count($items);

        if ($count === 0) {
            return '—';
        }

        $firstName = is_array($items[0]) ? ($items[0]['name'] ?? '') : '';
        $firstName = is_string($firstName) ? trim($firstName) : '';

        if ($count === 1 && $firstName !== '') {
            return $firstName;
        }

        if ($firstName !== '') {
            return $firstName.' +'.($count - 1);
        }

        return $count.' '.($count === 1 ? 'item' : 'items');
    }

    public function totalQuantity(): int
    {
        $items = is_array($this->items) ? $this->items : [];
        $total = 0;

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $qty = (int) ($item['quantity'] ?? 1);
            $total += max(1, min(99, $qty));
        }

        return $total;
    }
}
