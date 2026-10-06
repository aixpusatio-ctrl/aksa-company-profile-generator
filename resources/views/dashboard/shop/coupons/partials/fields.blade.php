{{-- Coupon form fields. $coupon may be null (create). --}}
@php($dt = fn ($v) => $v ? $v->format('Y-m-d\TH:i') : null)
<div class="grid gap-4 sm:grid-cols-2" x-data="{ type: @js($coupon?->type ?? 'percentage') }">
    <div>
        <label class="form-label">Kode kupon <span class="text-rose-500">*</span></label>
        <input type="text" name="code" value="{{ $coupon?->code }}" required maxlength="40" pattern="[A-Za-z0-9_\-]+" placeholder="HEMAT10" class="form-input font-mono uppercase">
    </div>
    <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-input">
            <option value="active" @selected(($coupon?->status ?? 'active') === 'active')>Aktif</option>
            <option value="inactive" @selected($coupon?->status === 'inactive')>Nonaktif</option>
        </select>
    </div>
    <div class="sm:col-span-2">
        <label class="form-label">Deskripsi</label>
        <input type="text" name="description" value="{{ $coupon?->description }}" maxlength="255" placeholder="Diskon 10% untuk pelanggan baru" class="form-input">
    </div>
    <div>
        <label class="form-label">Tipe</label>
        <select name="type" class="form-input" x-model="type">
            @foreach (\App\Models\Shop\Coupon::TYPES as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div x-show="type !== 'free_shipping'">
        <label class="form-label">Nilai <span x-text="type === 'percentage' ? '(%)' : '(nominal)'"></span></label>
        <input type="number" name="value" step="any" min="0" :max="type === 'percentage' ? 100 : null" value="{{ $coupon ? (float) $coupon->value : '' }}" class="form-input" :required="type !== 'free_shipping'" :disabled="type === 'free_shipping'">
    </div>
    <div>
        <label class="form-label">Minimum belanja</label>
        <input type="number" name="min_purchase" step="any" min="0" value="{{ $coupon?->min_purchase !== null ? (float) $coupon->min_purchase : '' }}" class="form-input">
    </div>
    <div x-show="type === 'percentage'">
        <label class="form-label">Maks. potongan</label>
        <input type="number" name="max_discount" step="any" min="0" value="{{ $coupon?->max_discount !== null ? (float) $coupon->max_discount : '' }}" class="form-input">
    </div>
    <div>
        <label class="form-label">Batas pemakaian total</label>
        <input type="number" name="usage_limit" min="1" value="{{ $coupon?->usage_limit }}" placeholder="Tanpa batas" class="form-input">
    </div>
    <div>
        <label class="form-label">Batas per pelanggan</label>
        <input type="number" name="usage_limit_per_customer" min="1" value="{{ $coupon?->usage_limit_per_customer }}" placeholder="Tanpa batas" class="form-input">
    </div>
    <div>
        <label class="form-label">Mulai</label>
        <input type="datetime-local" name="starts_at" value="{{ $dt($coupon?->starts_at) }}" class="form-input">
    </div>
    <div>
        <label class="form-label">Berakhir</label>
        <input type="datetime-local" name="ends_at" value="{{ $dt($coupon?->ends_at) }}" class="form-input">
    </div>
</div>
