<?php

namespace App\Services\Shop\Shipping;

final class ShippingQuote
{
    public function __construct(
        public readonly int $methodId,
        public readonly string $provider,
        public readonly string $type,
        public readonly string $name,
        public readonly float $cost,
        public readonly ?string $description = null,
        public readonly ?string $estimate = null,
        public readonly bool $requiresAddress = true,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
