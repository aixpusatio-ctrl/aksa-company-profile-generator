{{-- CTA: Gradient — rich primary→secondary panel with grid texture, noise and glow, headline and actions. --}}
<section id="cta" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="tone-primary relative isolate overflow-hidden rounded-[calc(var(--brand-radius)*2)] bg-primary px-6 py-16 text-center text-on-primary sm:px-16 sm:py-24" {!! $ds->reveal() !!}>
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(135deg,var(--brand-primary)_0%,color-mix(in_oklab,var(--brand-primary)_55%,var(--brand-secondary))_55%,var(--brand-secondary)_120%)]"></div>
            <div class="absolute inset-0 -z-10 bg-black/10"></div>
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(to_right,rgba(255,255,255,.09)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,.09)_1px,transparent_1px)] bg-[size:44px_44px] [mask-image:radial-gradient(ellipse_70%_80%_at_50%_40%,black,transparent)]"></div>
            <div class="absolute inset-0 -z-10 opacity-[0.18] mix-blend-overlay" style="background-image:url(&quot;data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)'/%3E%3C/svg%3E&quot;)"></div>
            <div class="absolute -top-32 left-1/2 -z-10 h-72 w-[42rem] max-w-[120%] -translate-x-1/2 rounded-full bg-white/25 blur-3xl"></div>

            <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-semibold backdrop-blur"><x-icon name="sparkles" class="size-3.5" /> {{ $company->name }}</span>
            <h2 class="heading mx-auto mt-6 max-w-3xl text-[clamp(2.1rem,5vw,4rem)]">{{ $section->title ?: 'Siap Melangkah Lebih Jauh?' }}</h2>
            <p class="mx-auto mt-5 max-w-2xl text-lead text-muted">{{ $section->subtitle ?: 'Mari diskusikan bagaimana kami dapat membantu bisnis Anda tumbuh.' }}</p>
            <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row">
                <a href="{{ $site->anchor('contact') }}" class="ds-btn ds-btn-light shadow-lg shadow-black/10">Hubungi Kami <x-icon name="arrow-right" class="size-4" /></a>
                @if ($company->whatsappUrl())
                    <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="ds-btn border border-white/30 bg-white/5 text-on-primary backdrop-blur hover:bg-white/15"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                @endif
            </div>
        </div>
    </div>
</section>
