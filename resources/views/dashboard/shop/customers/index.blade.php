<x-website-layout :company="$company" title="Pelanggan">
    @include('dashboard.shop.partials.nav')

    <div class="card">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Pelanggan <span class="ml-1 text-sm font-normal text-slate-400">({{ $customers->total() }})</span></h2>
                <p class="text-xs text-slate-500">Pelanggan terdaftar & tamu yang pernah checkout.</p>
            </div>
            <form method="GET" class="flex gap-2">
                <div class="relative flex-1 sm:w-64">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ $search }}" placeholder="Nama, email, telepon…" class="form-input pl-9">
                </div>
                <button class="btn btn-secondary">Cari</button>
            </form>
        </div>

        @if ($customers->isEmpty())
            <div class="p-6"><x-empty-state title="Belum ada pelanggan" description="Pelanggan akan tercatat otomatis saat checkout atau mendaftar di toko Anda." icon="users" /></div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Pelanggan</th><th>Kontak</th><th>Pesanan</th><th>Total belanja</th><th>Pesanan terakhir</th><th>Tipe</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($customers as $customer)
                            <tr>
                                <td class="min-w-[12rem]">
                                    <a href="{{ route('websites.shop.customers.show', [$company, $customer]) }}" class="flex items-center gap-3">
                                        <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-50 text-sm font-bold text-brand-600">{{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}</span>
                                        <span class="font-semibold text-slate-900 hover:text-brand-600">{{ $customer->name }}</span>
                                    </a>
                                </td>
                                <td class="text-xs text-slate-500"><span class="block">{{ $customer->email }}</span>{{ $customer->phone }}</td>
                                <td>{{ $customer->orders_count }}</td>
                                <td class="font-medium whitespace-nowrap">{{ \App\Support\Shop\Money::format($customer->total_spent ?? 0) }}</td>
                                <td class="text-xs whitespace-nowrap text-slate-500">{{ $customer->last_order_at ? \Illuminate\Support\Carbon::parse($customer->last_order_at)->format('d M Y') : '—' }}</td>
                                <td><span class="badge {{ $customer->isRegistered() ? 'badge-brand' : 'badge-slate' }}">{{ $customer->isRegistered() ? 'Terdaftar' : 'Tamu' }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($customers->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $customers->links() }}</div>
            @endif
        @endif
    </div>
</x-website-layout>
