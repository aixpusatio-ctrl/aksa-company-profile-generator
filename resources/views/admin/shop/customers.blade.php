@php use App\Support\Shop\Money; @endphp
<x-layouts.admin title="Customers">
    <x-page-header title="Customers" description="Semua pelanggan toko online. Setiap pelanggan terikat pada satu toko." />

    <form method="GET" class="card card-body mb-6 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau email..." class="form-input pl-9">
        </div>
        @include('admin.shop.partials.shop-filter')
        <div class="flex gap-2">
            <button class="btn btn-dark">Filter</button>
            @if ($search || $shop)
                <a href="{{ route('admin.shop.customers') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($customers->isEmpty())
        <x-empty-state icon="users" title="Tidak ada pelanggan" description="Belum ada pelanggan yang cocok dengan filter Anda." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Toko</th>
                            <th>Akun</th>
                            <th class="text-right">Pesanan</th>
                            <th class="text-right">Total Belanja</th>
                            <th>Login Terakhir</th>
                            <th>Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($customers as $customer)
                            <tr>
                                <td>
                                    <p class="font-medium text-slate-900">{{ $customer->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $customer->email }}</p>
                                    @if ($customer->phone || $customer->whatsapp)
                                        <p class="text-xs text-slate-500">{{ $customer->phone ?: $customer->whatsapp }}</p>
                                    @endif
                                </td>
                                <td>
                                    @if ($customer->companyProfile)
                                        <a href="{{ route('admin.companies.show', $customer->companyProfile) }}" class="text-slate-700 hover:text-brand-600">{{ $customer->companyProfile->name }}</a>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($customer->isRegistered())
                                        <span class="badge badge-brand"><span class="size-1.5 rounded-full bg-current"></span>Terdaftar</span>
                                    @else
                                        <span class="badge badge-slate"><span class="size-1.5 rounded-full bg-current"></span>Tamu</span>
                                    @endif
                                </td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="{{ route('admin.shop.orders', ['q' => $customer->email, 'shop' => $customer->company_profile_id]) }}" class="text-slate-700 hover:text-brand-600">{{ number_format($customer->orders_count) }}</a>
                                </td>
                                <td class="text-right font-medium whitespace-nowrap text-slate-900">{{ Money::format($customer->total_spent ?? 0, $customer->companyProfile?->shopSetting?->currency) }}</td>
                                <td class="whitespace-nowrap text-slate-500">{{ $customer->last_login_at?->diffForHumans() ?? '—' }}</td>
                                <td class="whitespace-nowrap text-slate-500">{{ $customer->created_at?->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $customers->links() }}</div>
    @endif
</x-layouts.admin>
