{{-- About: Editorial — magazine spread with drop-cap lede, two-column copy, oversized vision pull quote and captioned image. --}}
@php
    $image = $company->gallery->first();
    $imageSrc = $image?->url('image') ?: $company->url('hero_image');
    $caption = $image?->title ?: $company->name.($company->city ? ', '.$company->city : '');
@endphp
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <header class="grid gap-6 border-b border-line pb-10 lg:grid-cols-12 lg:items-end" {!! $ds->reveal(0) !!}>
            <div class="lg:col-span-8">
                {!! $ds->eyebrow('Tentang Kami', $index) !!}
                <h2 class="heading mt-5 text-[clamp(2.4rem,6vw,5rem)]">{{ $section->title ?: 'Mengenal '.$company->name }}</h2>
            </div>
            <div class="flex flex-wrap gap-x-8 gap-y-2 text-xs font-semibold tracking-[0.18em] text-muted uppercase lg:col-span-4 lg:justify-end">
                @if ($company->established_year)<span>Est. {{ $company->established_year }}</span>@endif
                @if ($company->city)<span>{{ $company->city }}</span>@endif
            </div>
        </header>

        @if ($section->subtitle || $company->description)
            <p class="heading mt-12 max-w-4xl text-2xl leading-snug sm:text-3xl" {!! $ds->reveal(1) !!}>{{ $section->subtitle ?: $company->description }}</p>
        @endif

        <div class="mt-12 grid gap-12 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-7">
                <div class="site-prose text-muted md:columns-2 md:gap-10 [&>*]:break-inside-avoid-column [&>p:first-child]:first-letter:float-left [&>p:first-child]:first-letter:mt-1.5 [&>p:first-child]:first-letter:mr-3 [&>p:first-child]:first-letter:font-heading [&>p:first-child]:first-letter:text-7xl [&>p:first-child]:first-letter:leading-[0.8] [&>p:first-child]:first-letter:font-bold [&>p:first-child]:first-letter:text-primary" {!! $ds->reveal(2) !!}>
                    {!! $company->about ?: '<p>'.e($company->description).'</p>' !!}
                </div>
            </div>
            <figure class="lg:col-span-5" {!! $ds->reveal(3, 'right') !!}>
                <x-site.img :src="$imageSrc" :alt="$caption" icon="photo" class="{{ $ds->img('aspect-[4/5] w-full object-cover') }}" />
                <figcaption class="mt-3 flex items-start gap-3 text-xs text-muted">
                    <span class="mt-1.5 h-px w-6 shrink-0 bg-ink/40"></span>
                    <span>{{ $caption }}</span>
                </figcaption>
            </figure>
        </div>

        @if ($company->vision)
            <figure class="mt-16 border-y border-line py-12 sm:py-16 lg:mt-20" {!! $ds->reveal(0) !!}>
                <blockquote class="heading mx-auto max-w-5xl text-center text-[clamp(1.75rem,4.2vw,3.5rem)] leading-[1.12]">
                    <span class="text-primary">&ldquo;</span>{{ $company->vision }}<span class="text-primary">&rdquo;</span>
                </blockquote>
                <figcaption class="mt-6 text-center text-xs font-semibold tracking-[0.22em] text-muted uppercase">Visi {{ $company->name }}</figcaption>
            </figure>
        @endif

        @if ($company->missionItems())
            <ol class="mt-12 grid gap-x-10 gap-y-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach (array_slice($company->missionItems(), 0, 6) as $i => $item)
                    <li class="flex gap-4 border-t border-line pt-5" {!! $ds->reveal($i) !!}>
                        <span class="font-mono text-xs text-primary">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-sm text-ink">{{ $item }}</span>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</section>
