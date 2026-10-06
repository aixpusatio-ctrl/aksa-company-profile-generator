<x-website-layout :company="$company" title="Pembayaran">
    @include('dashboard.shop.partials.nav')

    <div class="card">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Metode pembayaran</h2>
                <p class="text-xs text-slate-500">Transfer bank manual & bayar di tempat (COD). Konfirmasi pembayaran dari halaman detail pesanan.</p>
            </div>
            <button type="button" class="btn btn-primary btn-sm" @click="$dispatch('open-modal', 'payment-create')"><x-icon name="plus" class="size-4" /> Tambah metode</button>
        </div>
        @if ($methods->isEmpty())
            <div class="p-6"><x-empty-state title="Belum ada metode pembayaran" description="Tambahkan rekening bank atau aktifkan COD." icon="credit-card" /></div>
        @else
            <ul class="divide-y divide-slate-100">
                @foreach ($methods as $method)
                    <li class="flex flex-col gap-3 px-4 py-4 sm:flex-row sm:items-start sm:px-6">
                        <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-xl {{ $method->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}"><x-icon :name="$method->type === 'cod' ? 'banknotes' : 'credit-card'" class="size-5" /></span>
                        <div class="min-w-0 flex-1">
                            <p class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-900">
                                {{ $method->name }}
                                <span class="badge badge-slate">{{ \App\Models\Shop\PaymentMethod::TYPES[$method->type] ?? $method->type }}</span>
                                @unless ($method->is_active)<span class="badge badge-amber">Nonaktif</span>@endunless
                            </p>
                            @if ($method->type === 'bank_transfer' && ! empty($method->config))
                                <p class="mt-1 font-mono text-sm text-slate-700">{{ $method->config['bank'] ?? '' }} {{ $method->config['account_number'] ?? '' }} <span class="font-sans text-slate-500">a.n. {{ $method->config['account_name'] ?? '—' }}</span></p>
                            @endif
                            @if ($method->instructions)<p class="mt-1 text-xs text-slate-500">{{ $method->instructions }}</p>@endif
                        </div>
                        <div class="flex items-center gap-1 self-end sm:self-auto">
                            <button type="button" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" @click="$dispatch('open-modal', 'payment-edit-{{ $method->id }}')" title="Edit"><x-icon name="pencil" class="size-4" /></button>
                            <x-confirm-delete :action="route('websites.shop.payments.destroy', [$company, $method])" message="Hapus metode pembayaran ini?" />
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <x-modal name="payment-create" title="Tambah metode pembayaran" max-width="max-w-2xl">
        <form method="POST" action="{{ route('websites.shop.payments.store', $company) }}" class="space-y-6">
            @csrf
            @include('dashboard.shop.payments.partials.fields', ['method' => null])
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
        </form>
    </x-modal>
    @foreach ($methods as $method)
        <x-modal :name="'payment-edit-'.$method->id" title="Edit metode pembayaran" max-width="max-w-2xl">
            <form method="POST" action="{{ route('websites.shop.payments.update', [$company, $method]) }}" class="space-y-6">
                @csrf
                @method('PUT')
                @include('dashboard.shop.payments.partials.fields', ['method' => $method])
                <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-primary">Simpan</button></div>
            </form>
        </x-modal>
    @endforeach
</x-website-layout>
