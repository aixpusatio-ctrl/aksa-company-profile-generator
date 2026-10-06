{{-- Shipping method fields. $method may be null (create). --}}
@php
    $cities = collect($method?->config['cities'] ?? [])->map(fn ($cost, $city) => $city.'='.(float) $cost)->implode("\n");
@endphp
<div class="grid gap-4 sm:grid-cols-2" x-data="{ type: @js($method?->type ?? 'flat') }">
    <div>
        <label class="form-label">Tipe</label>
        <select name="type" class="form-input" x-model="type">
            @foreach (\App\Models\Shop\ShippingMethod::TYPES as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Nama <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ $method?->name }}" required maxlength="100" placeholder="Pengiriman reguler" class="form-input">
    </div>
    <div class="sm:col-span-2">
        <label class="form-label">Deskripsi</label>
        <input type="text" name="description" value="{{ $method?->description }}" maxlength="255" class="form-input">
    </div>
    <div>
        <label class="form-label">Estimasi</label>
        <input type="text" name="estimate" value="{{ $method?->estimate }}" maxlength="60" placeholder="2-5 hari kerja" class="form-input">
    </div>
    <div x-show="type === 'flat' || type === 'custom'">
        <label class="form-label" x-text="type === 'custom' ? 'Biaya dasar' : 'Biaya ongkir'"></label>
        <input type="number" name="cost" step="any" min="0" value="{{ $method ? (float) $method->cost : '' }}" class="form-input">
    </div>
    <div x-show="type !== 'pickup'">
        <label class="form-label" x-text="type === 'free' ? 'Gratis untuk belanja minimal' : 'Gratis ongkir mulai belanja'"></label>
        <input type="number" name="min_order" step="any" min="0" value="{{ $method?->min_order !== null ? (float) $method->min_order : '' }}" placeholder="Opsional" class="form-input">
    </div>
    <div x-show="type === 'custom'">
        <label class="form-label">Biaya per kg</label>
        <input type="number" name="per_kg" step="any" min="0" value="{{ isset($method?->config['per_kg']) ? (float) $method->config['per_kg'] : '' }}" class="form-input">
    </div>
    <div class="sm:col-span-2" x-show="type === 'custom'">
        <label class="form-label">Tarif per kota</label>
        <textarea name="cities" rows="4" maxlength="3000" placeholder="Jakarta=15000&#10;Bandung=20000&#10;Surabaya=25000" class="form-input font-mono text-sm">{{ $cities }}</textarea>
        <p class="form-help">Satu kota per baris dengan format <code>Kota=biaya</code>. Kota yang tidak tercantum memakai biaya dasar + per kg.</p>
    </div>
    <label class="flex items-center gap-3 sm:col-span-2">
        <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked($method?->is_active ?? true)>
        <span class="text-sm font-medium text-slate-800">Aktif di checkout</span>
    </label>
</div>
