<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    public const STATUSES = ['trialing', 'active', 'past_due', 'canceled'];

    protected $fillable = ['user_id', 'plan', 'status', 'price', 'trial_ends_at', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'trial_ends_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isRunning(): bool
    {
        return match ($this->status) {
            'active' => $this->ends_at === null || $this->ends_at->isFuture(),
            'trialing' => $this->trial_ends_at === null || $this->trial_ends_at->isFuture(),
            default => false,
        };
    }

    public function planName(): string
    {
        return config("platform.plans.{$this->plan}.name", ucfirst($this->plan));
    }
}
