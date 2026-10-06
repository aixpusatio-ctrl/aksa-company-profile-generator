{{-- About: Story — long-form narrative in a narrow centered column with a large opening statement and signature line. --}}
@php
    $leader = $company->team->first();
    $image = $company->gallery->first()?->url('image');
@endphp
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container('text') }}">
        <div class="text-center" {!! $ds->reveal(0) !!}>
            {!! $ds->eyebrow('Cerita Kami', $index) !!}
            <h2 class="heading mt-5 text-h2">{{ $section->title ?: 'Kisah di balik '.$company->name }}</h2>
        </div>

        @if ($section->subtitle || $company->description)
            <p class="heading mt-12 text-center text-2xl leading-snug sm:text-[2rem]" {!! $ds->reveal(1) !!}>{{ $section->subtitle ?: $company->description }}</p>
        @endif

        <div class="mx-auto my-12 flex items-center justify-center gap-3 text-primary" aria-hidden="true">
            <span class="h-px w-12 bg-line"></span><x-icon name="sparkles" class="size-4" /><span class="h-px w-12 bg-line"></span>
        </div>

        @if ($company->about)
            <div class="site-prose text-lead text-muted" {!! $ds->reveal(2) !!}>{!! $company->about !!}</div>
        @endif

        @if ($image)
            <figure class="my-14 sm:-mx-12 lg:-mx-24" {!! $ds->reveal(0) !!}>
                <x-site.img :src="$image" :alt="$company->gallery->first()->title ?: $company->name" class="{{ $ds->img('aspect-[16/9] w-full object-cover') }}" />
                @if ($company->gallery->first()->title)
                    <figcaption class="mt-3 text-center text-xs text-muted">{{ $company->gallery->first()->title }}</figcaption>
                @endif
            </figure>
        @endif

        @if ($company->history)
            <div class="mt-14" {!! $ds->reveal(0) !!}>
                <h3 class="heading text-2xl sm:text-3xl">
                    Perjalanan kami @if ($company->established_year)<span class="text-muted">sejak {{ $company->established_year }}</span>@endif
                </h3>
                <div class="site-prose mt-6 text-muted">{!! $company->history !!}</div>
            </div>
        @endif

        @if ($company->vision)
            <blockquote class="mt-14 border-l-2 border-primary pl-6 sm:pl-8" {!! $ds->reveal(0) !!}>
                <p class="heading text-xl leading-snug italic sm:text-2xl">{{ $company->vision }}</p>
            </blockquote>
        @endif

        <div class="mt-14 flex items-center gap-4 border-t border-line pt-8" {!! $ds->reveal(0) !!}>
            @if ($leader)
                <x-site.img :src="$leader->url('photo')" :alt="$leader->name" icon="user" class="size-14 shrink-0 rounded-full object-cover" />
                <div>
                    <p class="heading text-2xl italic">{{ $leader->name }}</p>
                    <p class="text-sm text-muted">{{ $leader->position }}, {{ $company->name }}</p>
                </div>
            @else
                <x-site.logo :company="$company" text-class="heading text-lg" />
            @endif
        </div>
    </div>
</section>
