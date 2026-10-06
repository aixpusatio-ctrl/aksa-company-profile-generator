{{-- Payment method fields. $method may be null (create). --}}
<div class="grid gap-4 sm:grid-cols-2" x-data="{ type: @js($method?->type ?? 'bank_transfer') }">
    <div>
        <label class="form-label">Tipe</label>
        <select name="type" class="form-input" x-model="type">
            @foreach (\App\Models\Shop\PaymentMethod::TYPES as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label">Nama <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ $method?->name }}" required maxlength="100" placeholder="Transfer BCA" class="form-input">
    </div>
    <template x-if="type === 'bank_transfer'">
        <div class="grid gap-4 sm:col-span-2 sm:grid-cols-3">
            <div>
                <label class="form-label">Bank</label>
                <input type="text" name="bank" value="{{ $method?->config['bank'] ?? '' }}" maxlength="60" placeholder="BCA" class="form-input">
            </div>
            <div>
                <label class="form-label">No. rekening</label>
                <input type="text" name="account_number" value="{{ $method?->config['account_number'] ?? '' }}" maxlength="40" pattern="[0-9\-\s]+" inputmode="numeric" class="form-input">
            </div>
            <div>
                <label class="form-label">Atas nama</label>
                <input type="text" name="account_name" value="{{ $method?->config['account_name'] ?? '' }}" maxlength="120" class="form-input">
            </div>
        </div>
    </template>
    <div class="sm:col-span-2">
        <label class="form-label">Instruksi pembayaran</label>
        <textarea name="instructions" rows="3" maxlength="1000" placeholder="Transfer sesuai total pesanan lalu kirim bukti via WhatsApp." class="form-input">{{ $method?->instructions }}</textarea>
        <p class="form-help">Ditampilkan setelah checkout dan di halaman status pesanan.</p>
    </div>
    <label class="flex items-center gap-3 sm:col-span-2">
        <input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked($method?->is_active ?? true)>
        <span class="text-sm font-medium text-slate-800">Aktif di checkout</span>
    </label>
</div>
