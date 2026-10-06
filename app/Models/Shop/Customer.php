<?php

namespace App\Models\Shop;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Shop customer. Customers belong to ONE company profile (shop) and log in
 * with the separate "customer" guard on that shop's website — they can never
 * reach the seller dashboard or the admin panel.
 */
class Customer extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['company_profile_id', 'name', 'email', 'phone', 'whatsapp', 'password', 'last_login_at'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function companyProfile(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class)->orderByDesc('is_default')->latest();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function isRegistered(): bool
    {
        return $this->password !== null;
    }

    public function defaultAddress(): ?CustomerAddress
    {
        return $this->addresses->firstWhere('is_default', true) ?? $this->addresses->first();
    }
}
