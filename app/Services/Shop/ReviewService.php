<?php

namespace App\Services\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Customer;
use App\Models\Shop\Order;
use App\Models\Shop\Product;
use App\Models\Shop\Review;
use Illuminate\Validation\ValidationException;

/**
 * Product reviews with moderation and "Verified Purchase" detection.
 */
class ReviewService
{
    public function __construct(private readonly ShopService $shop) {}

    public function submit(CompanyProfile $company, Product $product, array $data, ?Customer $customer): Review
    {
        $settings = $this->shop->settings($company);

        if (! $settings->option('reviews_enabled')) {
            throw ValidationException::withMessages(['review' => 'Ulasan tidak diaktifkan.']);
        }

        $email = strtolower($customer?->email ?? $data['email']);
        $order = $this->completedOrderFor($company, $product, $email);

        if ($settings->option('reviews_require_purchase') && ! $order) {
            throw ValidationException::withMessages(['review' => 'Hanya pembeli yang dapat memberikan ulasan.']);
        }

        if (Review::query()->where('product_id', $product->id)->where('email', $email)->exists()) {
            throw ValidationException::withMessages(['review' => 'Anda sudah memberikan ulasan untuk produk ini.']);
        }

        $review = Review::query()->create([
            'company_profile_id' => $company->id,
            'product_id' => $product->id,
            'customer_id' => $customer?->id,
            'order_id' => $order?->id,
            'name' => $customer?->name ?? $data['name'],
            'email' => $email,
            'rating' => (int) $data['rating'],
            'title' => $data['title'] ?? null,
            'body' => $data['body'] ?? null,
            'image' => $data['image'] ?? null,
            'status' => $settings->option('reviews_moderation') ? 'pending' : 'approved',
            'verified_purchase' => (bool) $order,
        ]);

        $this->recalculate($product);

        return $review;
    }

    public function moderate(Review $review, string $status): void
    {
        $review->update(['status' => $status]);
        $this->recalculate($review->product);
    }

    public function delete(Review $review): void
    {
        $product = $review->product;
        $review->delete();
        $this->recalculate($product);
    }

    public function recalculate(Product $product): void
    {
        $approved = $product->reviews()->approved();

        $product->forceFill([
            'rating_avg' => round((float) $approved->avg('rating'), 2),
            'rating_count' => $approved->count(),
        ])->save();
    }

    private function completedOrderFor(CompanyProfile $company, Product $product, string $email): ?Order
    {
        return Order::query()
            ->where('company_profile_id', $company->id)
            ->where('customer_email', $email)
            ->where('status', 'completed')
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->latest()
            ->first();
    }
}
