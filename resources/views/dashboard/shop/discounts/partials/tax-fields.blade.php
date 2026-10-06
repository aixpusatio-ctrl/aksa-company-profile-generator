<div class="grid grid-cols-[1fr_7rem] gap-3">
    <div>
        <label class="mb-1 block text-[11px] font-medium text-slate-500">Nama</label>
        <input type="text" name="name" value="{{ $tax?->name }}" required maxlength="60" placeholder="PPN" class="form-input py-1.5 text-sm">
    </div>
    <div>
        <label class="mb-1 block text-[11px] font-medium text-slate-500">Tarif (%)</label>
        <input type="number" name="rate" value="{{ $tax ? (float) $tax->rate : '' }}" required step="0.01" min="0" max="100" placeholder="11" class="form-input py-1.5 text-sm">
    </div>
</div>
<div class="flex flex-wrap gap-x-5 gap-y-2">
    <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="inclusive" value="1" class="form-checkbox" @checked($tax?->inclusive)> Harga sudah termasuk pajak</label>
    <label class="flex items-center gap-2 text-sm text-slate-700"><input type="checkbox" name="is_active" value="1" class="form-checkbox" @checked($tax?->is_active ?? true)> Aktif</label>
</div>
