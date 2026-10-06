<div class="space-y-6">
    <x-form.rich name="about" label="Tentang perusahaan" :value="$company->about" help="Profil perusahaan, apa yang dikerjakan, dan keunggulan Anda." />
    <x-form.textarea name="vision" label="Visi" :value="$company->vision" rows="2" />
    <x-form.rich name="mission" label="Misi" :value="$company->mission" help="Gunakan bullet list — setiap poin menjadi satu misi." />
    <x-form.rich name="history" label="Sejarah perusahaan" :value="$company->history" />
    <x-form.rich name="company_values" label="Nilai-nilai perusahaan" :value="$company->company_values" help="Gunakan bullet list, contoh: Integritas — bekerja jujur dan transparan." />

    <div class="border-t border-slate-100 pt-6">
        <p class="text-sm font-semibold text-slate-900">Angka & pencapaian</p>
        <p class="form-help !mt-0.5">Ditampilkan di section statistik (contoh: <strong>250+</strong> Proyek selesai). Kosongkan untuk dihitung otomatis dari data Anda.</p>
        @php($highlights = old('highlights', $company->highlights ?? []))
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            @for ($i = 0; $i < 4; $i++)
                <div class="flex gap-2">
                    <input type="text" name="highlights[{{ $i }}][value]" value="{{ $highlights[$i]['value'] ?? '' }}" placeholder="250+" maxlength="20" class="form-input w-28 font-semibold" aria-label="Angka {{ $i + 1 }}">
                    <input type="text" name="highlights[{{ $i }}][label]" value="{{ $highlights[$i]['label'] ?? '' }}" placeholder="Proyek selesai" maxlength="60" class="form-input" aria-label="Label {{ $i + 1 }}">
                </div>
            @endfor
        </div>
        @error('highlights')<p class="form-error">{{ $message }}</p>@enderror
    </div>
</div>
