{{-- Corporate CTA: primary color band. --}}
<section id="cta" class="py-16">
    <div class="mx-auto max-w-7xl px-6">
        <div class="relative overflow-hidden rounded-brand bg-primary px-8 py-14 md:px-16">
            <div class="absolute -top-24 -right-24 size-72 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-32 left-1/3 size-72 rounded-full bg-black/10"></div>
            <div class="relative flex flex-col items-start justify-between gap-8 md:flex-row md:items-center">
                <div class="max-w-2xl">
                    <h2 class="font-heading text-3xl font-extrabold text-on-primary md:text-4xl">{{ $section->title ?: 'Siap Bertumbuh Bersama Kami?' }}</h2>
                    <p class="mt-3 text-on-primary/80">{{ $section->subtitle ?: 'Diskusikan kebutuhan Anda dengan tim kami dan dapatkan solusi terbaik untuk perusahaan Anda.' }}</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ $site->anchor('contact') }}" class="rounded-btn bg-white px-7 py-3.5 text-sm font-bold text-slate-900 shadow-lg transition hover:bg-slate-100">Hubungi Kami</a>
                    @if ($company->whatsappUrl())
                        <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-btn border border-white/40 px-7 py-3.5 text-sm font-bold text-on-primary transition hover:bg-white/10"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
