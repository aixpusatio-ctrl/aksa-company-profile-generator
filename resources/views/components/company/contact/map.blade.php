{{-- Contact: Map — full-width map background with an overlaid contact card and form card (stacked on mobile). --}}
@php($hasMap = (bool) $company->mapEmbedUrl())
<section id="contact" class="{{ $ds->section($tone, 'overflow-hidden lg:min-h-[44rem]') }}">
    @if ($hasMap)
        <div class="pointer-events-none absolute inset-0 z-[1] hidden bg-gradient-to-r from-surface/70 via-surface/10 to-transparent lg:block"></div>
    @else
        <div class="absolute inset-0 hidden bg-[radial-gradient(circle,color-mix(in_oklab,var(--ink)_14%,transparent)_1px,transparent_1px)] bg-[size:22px_22px] lg:block"></div>
    @endif
    <div class="{{ $ds->container() }} relative z-[2] grid gap-5 lg:grid-cols-12 lg:items-start">
        <div class="rounded-brand bg-card p-6 shadow-2xl ring-1 ring-line sm:p-8 lg:col-span-4" {!! $ds->reveal(0, 'left') !!}>
            {!! $ds->eyebrow('Kontak', $index) !!}
            <h2 class="heading mt-4 text-[clamp(1.8rem,3vw,2.4rem)]">{{ $section->title ?: 'Kunjungi Kami' }}</h2>
            @if ($section->subtitle)<p class="mt-3 text-muted">{{ $section->subtitle }}</p>@endif
            @include('components.company.partials.contact-details', ['class' => 'mt-8'])
        </div>
        <div class="rounded-brand bg-card p-6 shadow-2xl ring-1 ring-line sm:p-8 lg:col-span-5" {!! $ds->reveal(1, 'left') !!}>
            <h3 class="heading text-h3">Kirim Pesan</h3>
            <p class="mt-2 text-sm text-muted">Kami akan merespons dalam 1x24 jam kerja.</p>
            <div class="mt-6">
                @include('components.company.partials.form')
            </div>
        </div>
    </div>
    @if ($hasMap)
        <div class="{{ $ds->container() }} mt-5 lg:mt-0 lg:contents">
            @include('websites.partials.map', ['mapClass' => $ds->img('h-80 w-full border-0').' lg:absolute lg:inset-0 lg:z-0 lg:h-full lg:rounded-none'])
        </div>
    @endif
</section>
