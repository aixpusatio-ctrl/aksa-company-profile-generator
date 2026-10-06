<?php

namespace App\Models\Shop;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    public const STATUSES = [
        'pending' => 'Pending', 'confirmed' => 'Confirmed', 'processing' => 'Processing', 'packed' => 'Packed',
        'shipped' => 'Shipped', 'completed' => 'Completed', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded',
    ];

    public const PAYMENT_STATUSES = ['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed', 'refunded' => 'Refunded'];

    public const SHIPPING_STATUSES = ['pending' => 'Pending', 'ready' => 'Ready', 'shipped' => 'Shipped', 'delivered' => 'Delivered', 'returned' => 'Returned'];

    protected $fillable = [
        'company_profile_id', 'customer_id', 'order_number', 'access_token', 'channel', 'status', 'payment_status',
        'shipping_status', 'currency', 'subtotal', 'discount', 'shipping_cost', 'tax', 'total', 'tax_name',
        'tax_inclusive', 'coupon_code', 'customer_name', 'customer_email', 'customer_phone', 'customer_whatsapp',
        'shipping_address', 'shipping_method', 'payment_method', 'notes', 'paid_at', 'shipped_at', 'completed_at', 'cancelled_at',
    ];

    protected $hidden = ['access_token'];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'tax_inclusive' => 'boolean',
            'paid_at' => 'datetime',
            'shipped_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'order_number';
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at')->orderBy('id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class)->latestOfMany();
    }

    public function shipment(): HasOne
    {
        return $this->hasOne(Shipment::class)->latestOfMany();
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed', 'processing', 'packed'], true);
    }

    public function isOpen(): bool
    {
        return ! in_array($this->status, ['completed', 'cancelled', 'refunded'], true);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function itemsCount(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
