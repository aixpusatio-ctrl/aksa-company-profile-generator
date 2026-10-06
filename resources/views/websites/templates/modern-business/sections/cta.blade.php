{{-- Modern Business CTA: centered gradient panel with orbit rings. --}}
<section id="cta" class="py-16 lg:py-24">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="relative isolate overflow-hidden rounded-brand bg-linear-to-br from-primary via-primary to-secondary px-6 py-16 text-center text-on-primary shadow-2xl shadow-primary/25 sm:px-16 lg:py-24">
            <div class="absolute top-1/2 left-1/2 -z-10 size-[40rem] -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/15"></div>
            <div class="absolute top-1/2 left-1/2 -z-10 size-[28rem] -translate-x-1/2 -translate-y-1/2 rounded-full border border-white/20"></div>
            <div class="absolute top-1/2 left-1/2 -z-10 size-[16rem] -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/10 blur-2xl"></div>
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-semibold ring-1 ring-white/25"><x-icon name="rocket" class="size-3.5" /> Mari mulai</span>
            <h2 class="mx-auto mt-6 max-w-3xl font-heading text-3xl font-semibold tracking-tight sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Siap Membawa Bisnis Anda ke Level Berikutnya?' }}</h2>
            <p class="mx-auto mt-5 max-w-xl text-on-primary/80">{{ $section->subtitle ?: 'Jadwalkan sesi konsultasi gratis dan temukan solusi yang paling tepat untuk tim Anda.' }}</p>
            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-white px-7 py-3.5 text-sm font-semibold text-slate-900 shadow-xl shadow-black/15 transition hover:-translate-y-0.5">Jadwalkan Konsultasi <x-icon name="arrow-right" class="size-4" /></a>
                @if ($company->whatsappUrl())
                    <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-btn bg-white/10 px-7 py-3.5 text-sm font-semibold ring-1 ring-white/40 backdrop-blur transition hover:bg-white/20"><x-icon name="whatsapp" class="size-4" /> Chat WhatsApp</a>
                @endif
            </div>
        </div>
    </div>
</section>
