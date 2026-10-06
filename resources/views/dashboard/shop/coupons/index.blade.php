@php use App\Support\Shop\Money; @endphp
<x-website-layout :company="$company" title="Kupon">
    @include('dashboard.shop.partials.nav')

    <div class="card">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Kupon diskon</h2>
                <p class="text-xs text-slate-500">Persentase, potongan nominal, atau gratis ongkir — dengan minimum belanja, kuota & jadwal.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" @click="$dispatch('open-modal', 'coupon-create')"><x-icon name="plus" class="size-4" /> Buat kupon</button>
        </div>
        @if ($coupons->isEmpty())
            <div class="p-6">
                <x-empty-state title="Belum ada kupon" description="Buat kode promo untuk menarik pelanggan, mis. HEMAT10." icon="ticket">
                    <button type="button" class="btn btn-primary" @click="$dispatch('open-modal', 'coupon-create')">Buat kupon</button>
                </x-empty-state>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Kode</th><th>Diskon</th><th>Syarat</th><th>Pemakaian</th><th>Periode</th><th>Status</th><th class="text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($coupons as $coupon)
                            @php($used = $coupon->usages()->count())
                            @php($expired = $coupon->ends_at && $coupon->ends_at->isPast())
                            <tr>
                                <td>
                                    <span class="rounded-md border border-dashed border-brand-300 bg-brand-50 px-2 py-1 font-mono text-xs font-bold text-brand-700">{{ $coupon->code }}</span>
                                    @if ($coupon->description)<p class="mt-1 max-w-[14rem] truncate text-xs text-slate-500">{{ $coupon->description }}</p>@endif
                                </td>
                                <td class="font-semibold whitespace-nowrap text-slate-900">{{ $coupon->label() }}
                                    @if ($coupon->max_discount && $coupon->type === 'percentage')<p class="text-xs font-normal text-slate-500">maks {{ Money::format($coupon->max_discount) }}</p>@endif
                                </td>
                                <td class="text-xs whitespace-nowrap text-slate-500">{{ $coupon->min_purchase ? 'Min. '.Money::format($coupon->min_purchase) : 'Tanpa minimum' }}@if ($coupon->usage_limit_per_customer)<br>{{ $coupon->usage_limit_per_customer }}×/pelanggan @endif</td>
                                <td class="text-xs whitespace-nowrap">
                                    <span class="font-medium text-slate-800">{{ $used }}{{ $coupon->usage_limit ? ' / '.$coupon->usage_limit : '' }}</span>
                                    @if ($coupon->usages_sum_discount)<p class="text-slate-500">{{ Money::format($coupon->usages_sum_discount) }} diberikan</p>@endif
                                </td>
                                <td class="text-xs whitespace-nowrap text-slate-500">
                                    {{ $coupon->starts_at?->format('d M Y') ?? 'Sekarang' }} – {{ $coupon->ends_at?->format('d M Y') ?? '∞' }}
                                    @if ($expired)<p class="font-medium text-rose-600">Kedaluwarsa</p>@endif
                                </td>
                                <td><x-status-badge :status="$coupon->status" /></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="$dispatch('open-modal', 'coupon-edit-{{ $coupon->id }}')" title="Edit"><x-icon name="pencil" class="size-4" /></button>
                                        <x-confirm-delete :action="route('websites.shop.coupons.destroy', [$company, $coupon])" message="Hapus kupon ini?" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <x-modal name="coupon-create" title="Buat kupon" max-width="max-w-2xl">
        <form method="POST" action="{{ route('websites.shop.coupons.store', $company) }}" class="space-y-6">
            @csrf
            @include('dashboard.shop.coupons.partials.fields', ['coupon' => null])
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
        </form>
    </x-modal>
    @foreach ($coupons as $coupon)
        <x-modal :name="'coupon-edit-'.$coupon->id" :title="'Edit kupon '.$coupon->code" max-width="max-w-2xl">
            <form method="POST" action="{{ route('websites.shop.coupons.update', [$company, $coupon]) }}" class="space-y-6">
                @csrf
                @method('PUT')
                @include('dashboard.shop.coupons.partials.fields', ['coupon' => $coupon])
                <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </x-modal>
    @endforeach
</x-website-layout>
