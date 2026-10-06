<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $fillable = ['customer_id', 'label', 'name', 'phone', 'address', 'city', 'province', 'postal_code', 'country', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function toSnapshot(): array
    {
        return $this->only(['name', 'phone', 'address', 'city', 'province', 'postal_code', 'country']);
    }

    public function oneLine(): string
    {
        return collect([$this->address, $this->city, $this->province, $this->postal_code, $this->country])->filter()->implode(', ');
    }
}
