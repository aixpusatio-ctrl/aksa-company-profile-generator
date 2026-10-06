<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFYING = 'verifying';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_FAILED = 'failed';

    public const STATUSES = [self::STATUS_PENDING, self::STATUS_VERIFYING, self::STATUS_ACTIVE, self::STATUS_FAILED];

    /** Root domain such as example.com (A record). */
    public const TYPE_APEX = 'apex';

    /** Sub domain such as www.example.com (CNAME record). */
    public const TYPE_SUBDOMAIN = 'subdomain';

    protected $fillable = [
        'company_profile_id', 'domain', 'type', 'verification_token', 'status', 'is_primary',
        'failure_reason', 'last_checked_at', 'verified_at',
    ];

    protected $hidden = ['verification_token'];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'verified_at' => 'datetime',
            'last_checked_at' => 'datetime',
        ];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function url(): string
    {
        return config('platform.scheme').'://'.$this->domain;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            self::STATUS_ACTIVE => 'green',
            self::STATUS_VERIFYING => 'blue',
            self::STATUS_FAILED => 'red',
            default => 'amber',
        };
    }
}
