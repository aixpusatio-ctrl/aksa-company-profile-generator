<?php

namespace App\Http\Controllers\Shop;

use App\Models\Shop\Product;
use App\Services\MediaService;
use App\Services\Shop\CartService;
use App\Services\Shop\ReviewService;
use Illuminate\Http\Request;

class ReviewController extends ShopController
{
    public function store(Request $request, string $slug, ReviewService $reviews, CartService $carts, MediaService $media)
    {
        $company = $this->company();
        $product = Product::query()->where('company_profile_id', $company->id)->visible()->where('slug', $slug)->firstOrFail();
        $customer = $carts->customer($company);

        $data = $request->validateWithBag('review', [
            'name' => [$customer ? 'nullable' : 'required', 'string', 'max:120'],
            'email' => [$customer ? 'nullable' : 'required', 'email', 'max:150'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:120'],
            'body' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', MediaService::imageRule(2048)],
            'website_url' => ['prohibited'],
        ]);

        if ($request->hasFile('image')) {
            // Stored under the seller's media library owner, linked to the company.
            $data['image'] = $media->upload($request->file('image'), $company->user, $company, 'images')->path;
        }

        $review = $reviews->submit($company, $product, $data, $customer);

        return back()->with('shop_toast', $review->status === 'approved' ? 'Terima kasih! Ulasan Anda sudah tampil.' : 'Terima kasih! Ulasan Anda akan tampil setelah dimoderasi.');
    }
}
