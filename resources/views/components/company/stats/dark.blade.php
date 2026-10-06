{{-- Stats: Dark — tone-inverse dark band with a short heading line, large gradient numbers and a soft glow. --}}
@php
    $stats = $company->stats();
    $band = $ds->isDark() ? ($tone === 'inverse' ? 'alt' : $tone) : 'inverse';
@endphp
@if ($stats)
<section id="stats" class="{{ $ds->section($band, 'overflow-hidden') }}">
    <div class="pointer-events-none absolute -top-40 right-[-10%] size-[36rem] rounded-full bg-primary/20 blur-3xl"></div>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-primary/60 to-transparent"></div>
    <div class="{{ $ds->container() }} relative grid gap-12 lg:grid-cols-12">
        <div class="lg:col-span-4" {!! $ds->reveal() !!}>
            {!! $ds->eyebrow('Dalam Angka', $index) !!}
            <h2 class="heading mt-4 text-[clamp(1.75rem,3vw,2.5rem)]">{{ $section->title ?: 'Dampak yang terukur' }}</h2>
            @if ($section->subtitle)<p class="mt-4 text-muted">{{ $section->subtitle }}</p>@endif
        </div>
        <dl class="grid grid-cols-2 gap-x-6 gap-y-10 lg:col-span-8 {{ count($stats) === 4 || count($stats) <= 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-3' }}">
            @foreach ($stats as $stat)
                <div class="border-t border-line pt-6" {!! $ds->reveal($loop->index + 1) !!}>
                    <dd class="heading bg-gradient-to-b from-ink to-ink/55 bg-clip-text text-5xl text-transparent sm:text-6xl lg:text-7xl" data-count>{{ $stat['value'] }}</dd>
                    <dt class="mt-3 text-sm text-muted">{{ $stat['label'] }}</dt>
                </div>
            @endforeach
        </dl>
    </div>
</section>
@endif
