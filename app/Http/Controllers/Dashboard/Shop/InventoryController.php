<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Product;
use App\Services\Shop\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends SellerController
{
    public function __construct(private readonly InventoryService $inventory) {}

    public function index(Request $request, CompanyProfile $company): View
    {
        $this->authorizeShop($company);
        $filter = $request->string('filter')->toString();
        $threshold = $company->shopSetting->low_stock_threshold;

        $products = $company->shopProducts()->with(['variants', 'images'])->where('track_stock', true)
            ->where('status', '!=', Product::STATUS_ARCHIVED)
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%")))
            ->orderBy('name')->get();

        $products = match ($filter) {
            'low' => $products->filter(fn (Product $p) => $p->availableStock() > 0 && $p->availableStock() <= ($p->low_stock_threshold ?? $threshold)),
            'out' => $products->filter(fn (Product $p) => $p->availableStock() <= 0),
            default => $products,
        };

        return view('dashboard.shop.inventory.index', [
            'company' => $company,
            'products' => $products->values(),
            'threshold' => $threshold,
            'filter' => $filter,
            'movements' => $company->inventoryMovements()->with(['product', 'variant', 'user'])->take(30)->get(),
        ]);
    }

    public function adjust(Request $request, CompanyProfile $company, Product $shopProduct): RedirectResponse
    {
        $this->authorizeShop($company);

        $data = $request->validate([
            'variant_id' => ['nullable', 'integer'],
            'direction' => ['required', 'in:add,remove'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000000'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $variant = ! empty($data['variant_id']) ? $shopProduct->variants()->findOrFail($data['variant_id']) : null;
        $quantity = $data['direction'] === 'add' ? $data['quantity'] : -$data['quantity'];

        $this->inventory->adjust($shopProduct, $variant, $quantity, $data['reason'], $request->user()->id);

        return back()->with('success', 'Stok diperbarui.');
    }
}
