{{-- Contact: Dark — tone-inverse dark panel with glow, contact details and a form card. --}}
@php
    $outer = $ds->isDark() && $tone === 'inverse' ? 'alt' : $tone;
    $panelTone = $ds->isDark() ? '' : 'tone-inverse';
@endphp
<section id="contact" class="{{ $ds->section($outer) }}">
    <div class="{{ $ds->container() }}">
        <div class="{{ $panelTone }} relative isolate overflow-hidden rounded-[calc(var(--brand-radius)*1.5)] bg-surface text-ink ring-1 ring-line" {!! $ds->reveal() !!}>
            <div class="absolute -top-40 -left-32 -z-10 size-[30rem] rounded-full bg-primary/25 blur-3xl"></div>
            <div class="absolute -right-40 -bottom-48 -z-10 size-[28rem] rounded-full bg-secondary/20 blur-3xl"></div>
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(to_right,color-mix(in_oklab,var(--ink)_5%,transparent)_1px,transparent_1px),linear-gradient(to_bottom,color-mix(in_oklab,var(--ink)_5%,transparent)_1px,transparent_1px)] bg-[size:48px_48px] [mask-image:radial-gradient(ellipse_at_top_left,black,transparent_70%)]"></div>
            <div class="grid gap-12 p-6 sm:p-10 lg:grid-cols-12 lg:gap-14 lg:p-16">
                <div class="lg:col-span-5">
                    {!! $ds->eyebrow('Kontak', $index) !!}
                    <h2 class="heading mt-4 text-h2">{{ $section->title ?: 'Mari Wujudkan Bersama' }}</h2>
                    <p class="mt-5 text-lead text-muted">{{ $section->subtitle ?: 'Tim kami siap membantu. Kami akan merespons dalam 1x24 jam kerja.' }}</p>
                    @include('components.company.partials.contact-details', ['class' => 'mt-10'])
                    <x-site.social :company="$company" class="mt-10" />
                </div>
                <div class="lg:col-span-7">
                    <div class="rounded-brand bg-card p-6 shadow-2xl ring-1 ring-line sm:p-10">
                        @include('components.company.partials.form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
