@php
    use App\Support\Shop\Money;

    $icons = ['pickup' => 'storefront', 'flat' => 'truck', 'free' => 'sparkles', 'custom' => 'map-pin'];
@endphp
<x-website-layout :company="$company" title="Pengiriman">
    @include('dashboard.shop.partials.nav')

    <div class="card">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Metode pengiriman</h2>
                <p class="text-xs text-slate-500">Ambil di toko, flat rate, gratis ongkir, atau tarif custom per kota / per kg.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" @click="$dispatch('open-modal', 'shipping-create')"><x-icon name="plus" class="size-4" /> Tambah metode</button>
        </div>
        @if ($methods->isEmpty())
            <div class="p-6"><x-empty-state title="Belum ada metode pengiriman" description="Tambahkan minimal satu metode agar pelanggan dapat checkout." icon="truck" /></div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach ($methods as $method)
                    <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-center sm:px-6">
                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl {{ $method->is_active ? 'bg-brand-50 text-brand-600' : 'bg-slate-100 text-slate-400' }}"><x-icon :name="$icons[$method->type] ?? 'truck'" class="size-5" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-900">
                                {{ $method->name }}
                                <span class="badge badge-slate">{{ \App\Models\Shop\ShippingMethod::TYPES[$method->type] ?? $method->type }}</span>
                                @unless ($method->is_active)<span class="badge badge-amber">Nonaktif</span>@endunless
                            </p>
                            <p class="text-xs text-slate-500">
                                {{ collect([$method->description, $method->estimate])->filter()->implode(' · ') }}
                            </p>
                            <p class="mt-1 text-xs text-slate-600">
                                @switch($method->type)
                                    @case('pickup') Gratis — diambil pelanggan @break
                                    @case('free') Gratis ongkir{{ $method->min_order ? ' untuk belanja ≥ '.Money::format($method->min_order) : '' }} @break
                                    @case('custom')
                                        Dasar {{ Money::format($method->config['base'] ?? $method->cost) }}
                                        @if (! empty($method->config['per_kg'])) + {{ Money::format($method->config['per_kg']) }}/kg @endif
                                        @if (! empty($method->config['cities'])) · {{ count($method->config['cities']) }} tarif kota @endif
                                        @break
                                    @default {{ Money::format($method->cost) }}{{ $method->min_order ? ' · gratis mulai '.Money::format($method->min_order) : '' }}
                                @endswitch
                            </p>
                        </div>
                        <div class="flex items-center gap-1 self-end sm:self-auto">
                            <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="$dispatch('open-modal', 'shipping-edit-{{ $method->id }}')" title="Edit"><x-icon name="pencil" class="size-4" /></button>
                            <x-confirm-delete :action="route('websites.shop.shipping.destroy', [$company, $method])" message="Hapus metode pengiriman ini?" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <x-modal name="shipping-create" title="Tambah metode pengiriman" max-width="max-w-2xl">
        <form method="POST" action="{{ route('websites.shop.shipping.store', $company) }}" class="space-y-6">
            @csrf
            @include('dashboard.shop.shipping.partials.fields', ['method' => null])
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
        </form>
    </x-modal>
    @foreach ($methods as $method)
        <x-modal :name="'shipping-edit-'.$method->id" title="Edit metode pengiriman" max-width="max-w-2xl">
            <form method="POST" action="{{ route('websites.shop.shipping.update', [$company, $method]) }}" class="space-y-6">
                @csrf
                @method('PUT')
                @include('dashboard.shop.shipping.partials.fields', ['method' => $method])
                <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </x-modal>
    @endforeach
</x-website-layout>
