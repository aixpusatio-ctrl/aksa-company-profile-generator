<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_USER = 'user';

    protected $fillable = ['name', 'email', 'password', 'role', 'phone', 'avatar'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'suspended_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function companyProfiles(): HasMany
    {
        return $this->hasMany(CompanyProfile::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', self::ROLE_ADMIN);
    }

    /**
     * Current plan key ("free" when there is no running subscription).
     */
    public function planKey(): string
    {
        $subscription = $this->subscription;

        return $subscription && $subscription->isRunning() ? $subscription->plan : 'free';
    }

    public function plan(): array
    {
        return config('platform.plans.'.$this->planKey()) ?? config('platform.plans.free');
    }

    public function canCreateWebsite(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $max = $this->plan()['max_websites'] ?? null;

        return $max === null || $this->companyProfiles()->count() < $max;
    }

    public function canUseCustomDomain(): bool
    {
        return $this->isAdmin() || (bool) ($this->plan()['custom_domain'] ?? false);
    }

    public function initials(): string
    {
        return collect(explode(' ', trim($this->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
