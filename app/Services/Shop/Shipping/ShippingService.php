<?php

namespace App\Services\Shop\Shipping;

use App\Models\CompanyProfile;
use App\Models\Shop\ShippingMethod;
use Illuminate\Support\Collection;

/**
 * Collects shipping quotes from registered providers. Checkout only talks
 * to this service, never to a concrete provider.
 */
class ShippingService
{
    /** @var array<string, ShippingProviderInterface> */
    private array $providers = [];

    public function __construct(iterable $providers = [])
    {
        foreach ($providers as $provider) {
            $this->register($provider);
        }
    }

    public function register(ShippingProviderInterface $provider): void
    {
        $this->providers[$provider->key()] = $provider;
    }

    public function provider(string $key): ?ShippingProviderInterface
    {
        return $this->providers[$key] ?? null;
    }

    /**
     * @return Collection<int, ShippingQuote>
     */
    public function quotes(CompanyProfile $company, ShippingContext $context): Collection
    {
        return $company->shippingMethods()->where('is_active', true)->get()
            ->map(fn (ShippingMethod $method) => $this->provider($method->provider)?->quote($method, $context))
            ->filter()
            ->values();
    }

    public function quote(CompanyProfile $company, int $methodId, ShippingContext $context): ?ShippingQuote
    {
        return $this->quotes($company, $context)->firstWhere('methodId', $methodId);
    }
}
