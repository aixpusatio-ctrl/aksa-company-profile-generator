<?php

namespace App\Services\Shop\Payment;

use App\Models\CompanyProfile;
use App\Models\Shop\Order;
use App\Models\Shop\Payment;
use App\Models\Shop\PaymentMethod;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class PaymentService
{
    /** @var array<string, PaymentProviderInterface> */
    private array $providers = [];

    public function __construct(iterable $providers = [])
    {
        foreach ($providers as $provider) {
            $this->register($provider);
        }
    }

    public function register(PaymentProviderInterface $provider): void
    {
        $this->providers[$provider->key()] = $provider;
    }

    /** Active payment methods whose provider is available. */
    public function methods(CompanyProfile $company): Collection
    {
        return $company->paymentMethods()->where('is_active', true)->get()
            ->filter(fn (PaymentMethod $m) => isset($this->providers[$m->provider]) && in_array($m->type, $this->providers[$m->provider]->types(), true))
            ->values();
    }

    public function initiate(Order $order, PaymentMethod $method): Payment
    {
        $provider = $this->providers[$method->provider] ?? throw new InvalidArgumentException("Unknown payment provider [{$method->provider}]");

        return $provider->initiate($order, $method);
    }
}
