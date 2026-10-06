{{-- CTA: Image — full-width photograph with dark overlay, centered headline and actions. --}}
@php($image = $company->url('hero_image') ?? $company->gallery->first()?->url('image'))
<section id="cta" class="relative isolate overflow-hidden bg-[#0b0f17] py-[calc(var(--section-py)*1.25)] text-white">
    @if ($image)
        <img src="{{ $image }}" alt="" loading="lazy" decoding="async" class="animate-ken-burns absolute inset-0 -z-20 size-full object-cover">
    @else
        <div class="absolute inset-0 -z-20 bg-gradient-to-br from-primary to-secondary"></div>
    @endif
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-black/70 via-black/55 to-black/75"></div>
    <div class="{{ $ds->container('narrow') }} text-center">
        <p class="text-xs font-semibold tracking-[0.25em] text-white/70 uppercase" {!! $ds->reveal() !!}>{{ $company->name }}</p>
        <h2 class="heading mx-auto mt-5 max-w-4xl text-[clamp(2.2rem,5.5vw,4.5rem)] text-white" {!! $ds->reveal(1) !!}>{{ $section->title ?: 'Bangun Masa Depan Bersama Kami' }}</h2>
        <p class="mx-auto mt-6 max-w-2xl text-lead text-white/80" {!! $ds->reveal(2) !!}>{{ $section->subtitle ?: 'Diskusikan kebutuhan Anda dengan tim kami dan dapatkan solusi terbaik.' }}</p>
        <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row" {!! $ds->reveal(3) !!}>
            <a href="{{ $site->anchor('contact') }}" class="ds-btn ds-btn-light">Hubungi Kami <x-icon name="arrow-right" class="size-4" /></a>
            @if ($company->whatsappUrl())
                <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="ds-btn border border-white/40 text-white backdrop-blur-sm hover:bg-white/10"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
            @endif
        </div>
    </div>
</section>
