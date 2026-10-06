<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Review;
use App\Services\Shop\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends SellerController
{
    public function __construct(private readonly ReviewService $reviews) {}

    public function index(Request $request, CompanyProfile $company): View
    {
        $this->authorizeShop($company);
        $status = $request->string('status')->toString() ?: 'pending';

        return view('dashboard.shop.reviews.index', [
            'company' => $company,
            'reviews' => $company->productReviews()->with('product')->when($status !== 'all', fn ($q) => $q->where('status', $status))->paginate(20)->withQueryString(),
            'status' => $status,
            'counts' => $company->productReviews()->reorder()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function status(Request $request, CompanyProfile $company, Review $productReview): RedirectResponse
    {
        $this->authorizeShop($company);
        $data = $request->validate(['status' => ['required', 'in:approved,rejected,pending']]);
        $this->reviews->moderate($productReview, $data['status']);

        return back()->with('success', 'Ulasan '.($data['status'] === 'approved' ? 'disetujui' : ($data['status'] === 'rejected' ? 'ditolak' : 'dikembalikan ke antrean')).'.');
    }

    public function destroy(CompanyProfile $company, Review $productReview): RedirectResponse
    {
        $this->authorizeShop($company);
        $this->reviews->delete($productReview);

        return back()->with('success', 'Ulasan dihapus.');
    }
}
