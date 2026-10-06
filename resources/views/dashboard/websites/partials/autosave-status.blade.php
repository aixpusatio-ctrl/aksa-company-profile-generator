<span class="text-xs font-medium" aria-live="polite">
    <span x-show="status === 'pending' || status === 'saving'" class="inline-flex items-center gap-1.5 text-slate-500"><span class="size-2 animate-pulse rounded-full bg-amber-400"></span> Menyimpan…</span>
    <span x-cloak x-show="status === 'saved'" class="inline-flex items-center gap-1.5 text-emerald-600"><x-icon name="check" class="size-3.5" /> Tersimpan <span x-text="savedAt"></span></span>
    <span x-cloak x-show="status === 'error'" class="text-rose-600">Gagal autosave — klik Simpan</span>
</span>
