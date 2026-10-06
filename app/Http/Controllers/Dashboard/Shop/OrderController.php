<?php

namespace App\Http\Controllers\Dashboard\Shop;

use App\Models\CompanyProfile;
use App\Models\Shop\Order;
use App\Services\Shop\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends SellerController
{
    public function __construct(private readonly OrderService $orders) {}

    public function index(Request $request, CompanyProfile $company): View
    {
        $this->authorizeShop($company);
        $filter = $request->string('filter')->toString();
        $search = $request->string('q')->trim()->toString();

        $query = $company->orders()->withCount('items')
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('order_number', 'like', "%{$search}%")->orWhere('customer_name', 'like', "%{$search}%")->orWhere('customer_email', 'like', "%{$search}%")));

        match ($filter) {
            'paid' => $query->where('payment_status', 'paid'),
            'pending', 'processing', 'shipped', 'completed', 'cancelled' => $query->where('status', $filter),
            default => null,
        };

        return view('dashboard.shop.orders.index', [
            'company' => $company,
            'orders' => $query->paginate(20)->withQueryString(),
            'filter' => $filter,
            'search' => $search,
            'counts' => $company->orders()->reorder()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(CompanyProfile $company, Order $order): View
    {
        $this->authorizeShop($company);
        $order->load(['items.product', 'histories.user', 'payment', 'shipment', 'customer']);

        return view('dashboard.shop.orders.show', [
            'company' => $company,
            'order' => $order,
            'timeline' => $this->orders->timeline($order),
            'transitions' => $this->orders->allowedTransitions($order),
        ]);
    }

    public function invoice(CompanyProfile $company, Order $order): View
    {
        $this->authorizeShop($company);

        return view('dashboard.shop.orders.invoice', ['company' => $company, 'order' => $order->load(['items', 'payment', 'shipment'])]);
    }

    public function updateStatus(Request $request, CompanyProfile $company, Order $order): RedirectResponse
    {
        $this->authorizeShop($company);
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::STATUSES))],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $this->orders->updateStatus($order, $data['status'], $data['note'] ?? null, $request->user()->id);

        return back()->with('success', 'Status pesanan diperbarui.');
    }

    public function markPaid(Request $request, CompanyProfile $company, Order $order): RedirectResponse
    {
        $this->authorizeShop($company);
        $this->orders->markPaid($order, $request->input('note'), $request->user()->id);

        return back()->with('success', 'Pembayaran ditandai lunas.');
    }

    public function tracking(Request $request, CompanyProfile $company, Order $order): RedirectResponse
    {
        $this->authorizeShop($company);
        $data = $request->validate([
            'courier' => ['nullable', 'string', 'max:60'],
            'tracking_number' => ['required', 'string', 'max:100'],
        ]);

        $this->orders->setTracking($order, $data['courier'] ?? null, $data['tracking_number'], $request->user()->id);

        return back()->with('success', 'Nomor resi disimpan.');
    }
}
