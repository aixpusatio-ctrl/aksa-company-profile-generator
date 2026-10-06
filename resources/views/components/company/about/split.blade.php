{{-- About: Split Screen — half-width brand panel (heading + vision) beside the story and mission list. --}}
@php
    $mission = array_slice($company->missionItems() ?: $company->valueItems(), 0, 5);
    $panelTone = ($ds->isDark() || $tone === 'inverse') ? 'tone-primary bg-primary text-on-primary' : 'tone-inverse bg-surface text-ink';
@endphp
<section id="about" class="{{ $ds->section($tone, 'py-0!') }}">
    <div class="grid lg:grid-cols-2">
        <div class="{{ $panelTone }} relative overflow-hidden">
            <div class="pointer-events-none absolute -bottom-24 -left-24 size-96 rounded-full border border-line"></div>
            <div class="pointer-events-none absolute -bottom-40 -left-40 size-[34rem] rounded-full border border-line"></div>
            <div class="relative flex h-full flex-col justify-between gap-16 px-5 py-section sm:px-8 lg:ml-auto lg:max-w-[40rem] lg:px-14">
                <div {!! $ds->reveal(0, 'left') !!}>
                    @if (str_starts_with($panelTone, 'tone-primary'))
                        <span class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.22em] uppercase opacity-80"><span class="h-px w-10 bg-on-primary"></span>Tentang Kami</span>
                    @else
                        {!! $ds->eyebrow('Tentang Kami', $index) !!}
                    @endif
                    <h2 class="heading mt-5 text-h2">{{ $section->title ?: 'Mengenal '.$company->name }}</h2>
                    @if ($section->subtitle)
                        <p class="mt-5 text-lead text-muted">{{ $section->subtitle }}</p>
                    @endif
                </div>
                @if ($company->vision)
                    <blockquote {!! $ds->reveal(1, 'left') !!}>
                        <p class="text-xs font-semibold tracking-[0.22em] text-muted uppercase">Visi kami</p>
                        <p class="heading mt-4 text-2xl leading-snug sm:text-3xl">{{ $company->vision }}</p>
                    </blockquote>
                @endif
            </div>
        </div>

        <div class="px-5 py-section sm:px-8 lg:max-w-[40rem] lg:px-14">
            <div class="site-prose text-lead text-muted" {!! $ds->reveal(1, 'right') !!}>{!! $company->about ?: e($company->description) !!}</div>
            @if ($mission)
                <div class="mt-12" {!! $ds->reveal(2, 'right') !!}>
                    <h3 class="heading text-h3">{{ $company->missionItems() ? 'Misi' : 'Nilai' }}</h3>
                    <ol class="mt-5 divide-y divide-line border-y border-line">
                        @foreach ($mission as $i => $item)
                            <li class="flex gap-5 py-4">
                                <span class="font-mono text-sm text-primary">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-ink">{{ $item }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
            <div class="mt-10" {!! $ds->reveal(3) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Bicara dengan Kami', 'kind' => 'primary'])
            </div>
        </div>
    </div>
</section>
