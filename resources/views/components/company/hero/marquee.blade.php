{{-- Hero: Marquee — giant headline over two endlessly scrolling image strips moving in opposite directions. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $images = $company->gallery->map(fn ($g) => ['src' => $g->url('image'), 'alt' => $g->title ?: $company->name])
        ->merge($company->projects->map(fn ($p) => ['src' => $p->url('image'), 'alt' => $p->title]))
        ->filter(fn ($i) => filled($i['src']))
        ->values();
    if ($images->isEmpty()) {
        $images = collect([['src' => $company->url('hero_image'), 'alt' => $company->name]]);
    }
    $half = (int) ceil($images->count() / 2);
    $rows = $images->count() >= 4 ? [$images->take($half)->values(), $images->slice($half)->values()] : [$images, $images->reverse()->values()];
    // Make each strip wide enough to loop seamlessly.
    $rows = array_map(function ($row) {
        while ($row->count() < 6) {
            $row = $row->merge($row);
        }
        return $row;
    }, $rows);
@endphp
<section id="hero" class="relative overflow-hidden bg-surface pb-16 lg:pb-24">
    <div class="{{ $ds->container('wide') }} pt-32 lg:pt-40">
        <div {!! $ds->reveal(0) !!}>{!! $ds->eyebrow($company->name) !!}</div>
        <h1 class="heading mt-6 max-w-[16ch] text-[clamp(3rem,8vw,7.5rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
        <div class="mt-10 flex flex-col gap-8 border-t border-line pt-8 lg:flex-row lg:items-end lg:justify-between" {!! $ds->reveal(2) !!}>
            @if ($lead)<p class="max-w-xl text-lead text-muted">{{ $lead }}</p>@endif
            <div class="flex shrink-0 flex-col gap-3 sm:flex-row">
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Mulai Proyek', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('projects'), 'label' => 'Lihat Karya', 'kind' => 'secondary'])
            </div>
        </div>
    </div>

    <div class="mt-14 space-y-4 lg:mt-20 [mask-image:linear-gradient(to_right,transparent,#000_8%,#000_92%,transparent)]" {!! $ds->reveal(3) !!}>
        @foreach ($rows as $r => $row)
            <div class="group flex overflow-hidden">
                <ul class="flex w-max shrink-0 gap-4 pr-4 group-hover:[animation-play-state:paused] {{ $r === 0 ? 'animate-marquee' : 'animate-marquee-reverse' }} motion-reduce:animate-none" style="animation-duration: {{ $r === 0 ? 45 : 55 }}s">
                    @foreach ([0, 1] as $copy)
                        @foreach ($row as $img)
                            <li class="w-56 shrink-0 sm:w-72 lg:w-80" @if ($copy) aria-hidden="true" @endif>
                                <x-site.img :src="$img['src']" :alt="$copy ? '' : $img['alt']" icon="photo" class="{{ $ds->img('aspect-[4/3] w-full object-cover') }}" />
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</section>
