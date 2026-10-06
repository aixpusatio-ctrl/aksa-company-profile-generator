{{-- About: Leader Message — portrait of the first team member, large quote, full message and signature. --}}
@php
    $leader = $company->team->first();
    $aboutHtml = $company->about ?: '<p>'.e($company->description).'</p>';
    preg_match('/<p[^>]*>(.*?)<\/p>/s', $aboutHtml, $firstP);
    $quote = $company->vision ?: \Illuminate\Support\Str::limit(trim(strip_tags($firstP[1] ?? $aboutHtml)), 220);
@endphp
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-12 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-5">
            <div class="lg:sticky lg:top-28" {!! $ds->reveal(0, 'left') !!}>
                <div class="relative pr-4 pb-4 sm:pr-6 sm:pb-6">
                    <div class="absolute top-6 right-0 bottom-0 left-6 rounded-brand bg-primary/15 sm:top-10 sm:left-10" aria-hidden="true"></div>
                    <x-site.img :src="$leader?->url('photo')" :alt="$leader?->name ?: $company->name" icon="user" class="{{ $ds->img('relative aspect-[4/5] w-full object-cover') }}" />
                </div>
                @if ($leader)
                    <div class="mt-10 flex items-center justify-between gap-4 border-b border-line pb-5">
                        <div class="min-w-0">
                            <p class="heading truncate text-lg">{{ $leader->name }}</p>
                            <p class="text-sm text-muted">{{ $leader->position }}</p>
                        </div>
                        @if ($leader->linkedin)
                            <a href="{{ $leader->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-11 shrink-0 items-center justify-center rounded-full border border-line text-muted transition hover:border-primary hover:text-primary" aria-label="LinkedIn {{ $leader->name }}">
                                <x-icon name="linkedin" class="size-4" />
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-7">
            <div {!! $ds->reveal(0) !!}>
                {!! $ds->eyebrow($leader ? 'Sambutan Pimpinan' : 'Tentang Kami', $index) !!}
                <h2 class="heading mt-4 text-h2">{{ $section->title ?: ($leader ? 'Pesan dari pimpinan kami' : 'Mengenal '.$company->name) }}</h2>
            </div>

            <figure class="relative mt-10" {!! $ds->reveal(1) !!}>
                <x-icon name="quote" class="size-10 text-primary" />
                <blockquote class="heading mt-5 text-[clamp(1.5rem,2.8vw,2.25rem)] leading-snug">{{ $quote }}</blockquote>
            </figure>

            @if ($section->subtitle)
                <p class="mt-8 text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $section->subtitle }}</p>
            @endif

            <div class="site-prose mt-10 border-t border-line pt-10 text-muted" {!! $ds->reveal(2) !!}>{!! $aboutHtml !!}</div>

            @if ($company->missionItems())
                <ul class="mt-8 space-y-3" {!! $ds->reveal(3) !!}>
                    @foreach (array_slice($company->missionItems(), 0, 4) as $item)
                        <li class="flex gap-3 text-ink"><x-icon name="check" class="mt-1 size-4 shrink-0 text-primary" /><span>{{ $item }}</span></li>
                    @endforeach
                </ul>
            @endif

            @if ($leader)
                <div class="mt-12" {!! $ds->reveal(4) !!}>
                    <p class="text-sm text-muted">Hormat kami,</p>
                    <p class="mt-2 font-heading text-[clamp(2rem,4vw,2.75rem)] leading-tight text-primary italic" style="font-weight:400;letter-spacing:-0.01em">{{ $leader->name }}</p>
                    <p class="mt-1 text-xs font-semibold tracking-[0.18em] text-muted uppercase">{{ $leader->position }} · {{ $company->name }}</p>
                </div>
            @endif
        </div>
    </div>
</section>
