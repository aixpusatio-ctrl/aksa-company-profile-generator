{{-- Consulting CTA: quiet centered statement on primary with hairline frame. --}}
<section id="cta" class="bg-primary py-24 text-on-primary lg:py-32">
    <div class="mx-auto max-w-5xl px-6">
        <div class="border border-current/20 px-6 py-16 text-center md:px-16">
            <p class="text-xs tracking-[0.3em] uppercase opacity-60">Langkah berikutnya</p>
            <h2 class="mx-auto mt-6 max-w-3xl font-heading text-4xl leading-tight md:text-5xl">{{ $section->title ?: 'Mari pikirkan masa depan organisasi Anda bersama.' }}</h2>
            <p class="mx-auto mt-6 max-w-xl opacity-75">{{ $section->subtitle ?: 'Sesi diskusi awal tanpa biaya untuk memahami tantangan dan peluang Anda.' }}</p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-3 rounded-btn bg-white px-8 py-4 text-sm tracking-wide text-stone-900 transition hover:bg-stone-100">Jadwalkan Diskusi <x-icon name="arrow-right" class="size-4" /></a>
                @if ($company->whatsappUrl())
                    <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-4 text-sm tracking-wide underline decoration-current/30 underline-offset-8 hover:decoration-current"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                @endif
            </div>
        </div>
    </div>
</section>
