{{-- Hero: Gradient — soft mesh-gradient backdrop, centered headline, tilted floating image card. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $services = $company->services->take(2);
    $a = $ds->isDark() ? 38 : 34;
@endphp
<section id="hero" class="relative isolate overflow-hidden bg-surface">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true"
        style="background:
            radial-gradient(40rem 30rem at 12% 8%, color-mix(in oklab, var(--brand-primary) {{ $a }}%, transparent), transparent 70%),
            radial-gradient(36rem 28rem at 88% 4%, color-mix(in oklab, var(--brand-secondary) {{ $a }}%, transparent), transparent 70%),
            radial-gradient(44rem 30rem at 50% 62%, color-mix(in oklab, var(--brand-primary) {{ round($a * 0.6) }}%, transparent), transparent 70%),
            radial-gradient(30rem 24rem at 80% 70%, color-mix(in oklab, var(--brand-secondary) {{ round($a * 0.7) }}%, transparent), transparent 70%);"></div>
    <div class="pointer-events-none absolute inset-x-0 bottom-0 -z-10 h-1/3 bg-gradient-to-t from-surface to-transparent" aria-hidden="true"></div>

    <div class="{{ $ds->container() }} relative pt-36 text-center lg:pt-44">
        <div {!! $ds->reveal(0) !!}>{!! $ds->eyebrow($company->city ? $company->name.' · '.$company->city : $company->name) !!}</div>
        <h1 class="heading mx-auto mt-6 max-w-4xl text-display max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
        @if ($lead)
            <p class="mx-auto mt-6 max-w-2xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
        @endif
        <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row" {!! $ds->reveal(3) !!}>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'primary'])
            @include('components.company.partials.button', ['href' => $site->anchor('services'), 'label' => 'Lihat Layanan', 'kind' => 'secondary'])
        </div>
    </div>

    <div class="{{ $ds->container() }} relative mt-8 pt-8 pb-16 lg:mt-12 lg:pb-24 [perspective:2000px]" {!! $ds->reveal(4) !!}>
        <div class="group relative mx-auto max-w-5xl transition duration-700 [transform:rotateX(14deg)_scale(.97)] [transform-origin:50%_0%] hover:[transform:rotateX(0deg)_scale(1)]">
            <div class="absolute -inset-4 -z-10 rounded-[calc(var(--brand-radius)*2.5)] bg-gradient-to-br from-primary/30 to-secondary/30 opacity-70 blur-2xl"></div>
            <div class="overflow-hidden rounded-[calc(var(--brand-radius)*2)] bg-card p-2 shadow-2xl shadow-black/20 ring-1 ring-line">
                <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="aspect-[16/9] w-full rounded-[calc(var(--brand-radius)*1.6)] object-cover" />
            </div>
            @foreach ($services as $service)
                <div class="absolute hidden max-w-[15rem] items-center gap-3 rounded-full border border-line bg-card/80 py-2 pr-5 pl-2 text-left shadow-xl backdrop-blur-md md:flex {{ $loop->first ? '-top-6 -left-6 animate-float' : '-right-6 bottom-10 animate-float [animation-delay:-3s]' }}">
                    <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-primary text-on-primary"><x-icon :name="$service->icon ?: 'sparkles'" class="size-4" /></span>
                    <span class="truncate text-sm font-semibold text-ink">{{ $service->title }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>
