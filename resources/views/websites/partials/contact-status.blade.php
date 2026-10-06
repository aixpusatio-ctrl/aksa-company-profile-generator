{{-- Contact form feedback (success / validation errors / preview notice). --}}
@if (! $site->canSubmitForms())
    <div class="mb-4 rounded-brand border border-amber-300/60 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        Mode preview — formulir kontak aktif setelah website dipublikasikan.
    </div>
@endif
@if (session('contact_success'))
    <div class="mb-4 rounded-brand border border-emerald-300/60 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" role="status">
        Terima kasih! Pesan Anda sudah kami terima dan akan segera kami balas.
    </div>
@endif
@if ($errors->contact->any())
    <div class="mb-4 rounded-brand border border-rose-300/60 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
        <ul class="list-inside list-disc space-y-0.5">
            @foreach ($errors->contact->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
