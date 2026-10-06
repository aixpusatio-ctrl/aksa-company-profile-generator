@php $accountTitle = 'Alamat'; @endphp
@extends('websites.shop.account.layout')

@section('account')
    @php
        $fields = [
            'label' => ['Label (mis. Rumah, Kantor)', false, 40],
            'name' => ['Nama penerima', true, 120],
            'phone' => ['No. HP', true, 30],
            'address' => ['Alamat lengkap', true, 500],
            'city' => ['Kota / Kabupaten', true, 100],
            'province' => ['Provinsi', false, 100],
            'postal_code' => ['Kode pos', false, 20],
            'country' => ['Negara', false, 100],
        ];
    @endphp

    <div class="space-y-6" x-data="{ editing: {{ $errors->any() ? (int) old('_address_id', 0) : 'null' }}, adding: {{ $errors->any() && ! old('_address_id') ? 'true' : ($addresses->isEmpty() ? 'true' : 'false') }} }">
        @if ($addresses->isNotEmpty())
            <ul class="grid gap-4 md:grid-cols-2">
                @foreach ($addresses as $a)
                    <li class="{{ $ds->card('p-5', false) }} border {{ $a->is_default ? 'border-primary' : 'border-line' }}">
                        <div x-show="editing !== {{ $a->id }}">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-ink">{{ $a->label ?: $a->name }}</p>
                                @if ($a->is_default)
                                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[11px] font-semibold text-primary">Utama</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm text-ink">{{ $a->name }} · {{ $a->phone }}</p>
                            <p class="text-sm text-muted">{{ $a->oneLine() }}</p>
                            <div class="mt-4 flex flex-wrap items-center gap-2">
                                <button type="button" @click="editing = {{ $a->id }}; adding = false" class="{{ $ds->btn('secondary', '!px-3.5 !py-2 !text-xs') }}"><x-icon name="pencil" class="size-3.5" /> Ubah</button>
                                @unless ($a->is_default)
                                    <form method="POST" action="{{ $site->account('addresses/'.$a->id) }}">
                                        @csrf
                                        @method('PUT')
                                        @foreach (array_keys($fields) as $key)
                                            <input type="hidden" name="{{ $key }}" value="{{ $a->{$key} }}">
                                        @endforeach
                                        <input type="hidden" name="is_default" value="1">
                                        <button type="submit" class="{{ $ds->btn('ghost', '!px-3 !py-2 !text-xs') }}">Jadikan utama</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ $site->account('addresses/'.$a->id) }}" class="ml-auto" onsubmit="return confirm('Hapus alamat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex size-9 items-center justify-center rounded-full text-muted hover:bg-rose-50 hover:text-rose-600" aria-label="Hapus alamat"><x-icon name="trash" class="size-4" /></button>
                                </form>
                            </div>
                        </div>

                        {{-- Edit form --}}
                        <form x-cloak x-show="editing === {{ $a->id }}" method="POST" action="{{ $site->account('addresses/'.$a->id) }}" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="_address_id" value="{{ $a->id }}">
                            @include('websites.shop.partials.address-fields', ['addr' => $a, 'prefix' => 'ed'.$a->id, 'useOld' => (int) old('_address_id') === $a->id])
                            <div class="flex gap-2">
                                <button type="submit" class="{{ $ds->btn('primary', '!py-2.5') }}">Simpan</button>
                                <button type="button" @click="editing = null" class="{{ $ds->btn('ghost', '!py-2.5') }}">Batal</button>
                            </div>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

        <div>
            <button type="button" x-show="!adding" @click="adding = true; editing = null" class="{{ $ds->btn('secondary') }}"><x-icon name="plus" class="size-4" /> Tambah alamat</button>
            <form x-show="adding" @if ($addresses->isNotEmpty() && ! ($errors->any() && ! old('_address_id'))) x-cloak @endif method="POST" action="{{ $site->account('addresses') }}" class="{{ $ds->card('space-y-3 p-5 sm:p-6', false) }} border border-line">
                @csrf
                <h2 class="font-heading text-lg font-bold text-ink">Alamat baru</h2>
                @include('websites.shop.partials.address-fields', ['addr' => null, 'prefix' => 'new', 'useOld' => ! old('_address_id')])
                <div class="flex gap-2">
                    <button type="submit" class="{{ $ds->btn('primary', '!py-2.5') }}">Simpan Alamat</button>
                    @if ($addresses->isNotEmpty())
                        <button type="button" @click="adding = false" class="{{ $ds->btn('ghost', '!py-2.5') }}">Batal</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection
