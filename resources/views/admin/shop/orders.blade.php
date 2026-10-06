@php use App\Models\Shop\Order; use App\Support\Shop\Money; @endphp
<x-layouts.admin title="Orders">
    <x-page-header title="Orders" description="Semua pesanan dari seluruh toko online di platform." />

    <form method="GET" class="card card-body mb-6 grid gap-3 lg:grid-cols-[1fr_auto_auto_auto_auto]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ $search }}" placeholder="No. pesanan, nama atau email..." class="form-input pl-9">
        </div>
        @include('admin.shop.partials.shop-filter')
        <select name="status" class="form-input sm:w-40">
            <option value="">Semua status</option>
            @foreach (Order::STATUSES as $key => $label)
                <option value="{{ $key }}" @selected($status === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <select name="payment" class="form-input sm:w-40">
            <option value="">Semua pembayaran</option>
            @foreach (Order::PAYMENT_STATUSES as $key => $label)
                <option value="{{ $key }}" @selected($payment === $key)>{{ $label }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="btn btn-dark">Filter</button>
            @if ($search || $status || $payment || $shop)
                <a href="{{ route('admin.shop.orders') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($orders->isEmpty())
        <x-empty-state icon="list" title="Tidak ada pesanan" description="Belum ada pesanan yang cocok dengan filter Anda." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pesanan</th>
                            <th>Toko</th>
                            <th>Pelanggan</th>
                            <th class="text-right">Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.shop.orders.show', $order->id) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $order->order_number }}</a>
                                    <p class="text-xs text-slate-500">{{ $order->items_count }} item · {{ $order->channel === 'whatsapp' ? 'WhatsApp' : 'Web' }}</p>
                                </td>
                                <td>
                                    @if ($order->companyProfile)
                                        <a href="{{ route('admin.companies.show', $order->companyProfile) }}" class="text-slate-700 hover:text-brand-600">{{ $order->companyProfile->name }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td>
                                    <p class="text-slate-700">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $order->customer_email }}</p>
                                </td>
                                <td class="text-right font-medium whitespace-nowrap text-slate-900">{{ Money::format($order->total, $order->currency) }}</td>
                                <td>@include('admin.shop.partials.badge', ['type' => 'order', 'value' => $order->status])</td>
                                <td>@include('admin.shop.partials.badge', ['type' => 'payment', 'value' => $order->payment_status])</td>
                                <td class="whitespace-nowrap text-slate-500">{{ $order->created_at?->format('d M Y H:i') }}</td>
                                <td>
                                    <div class="flex items-center justify-end">
                                        <a href="{{ route('admin.shop.orders.show', $order->id) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Detail"><x-icon name="eye" class="size-4" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
</x-layouts.admin>
