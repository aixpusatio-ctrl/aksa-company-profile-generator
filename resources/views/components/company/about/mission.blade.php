{{-- About: Vision & Mission — intro text above two large cards "Visi" and "Misi", with an optional history accordion. --}}
@php
    $mission = $company->missionItems();
    $values = $company->valueItems();
@endphp
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end lg:gap-16">
            <div class="lg:col-span-6">
                @include('components.company.partials.heading', ['eyebrow' => 'Tentang Kami', 'title' => $section->title ?: 'Visi & misi '.$company->name, 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            </div>
            <div class="site-prose text-muted lg:col-span-6" {!! $ds->reveal(1) !!}>{!! $company->about ?: e($company->description) !!}</div>
        </div>

        @if ($company->vision || $mission)
            <div class="mt-14 grid gap-5 {{ $company->vision && $mission ? 'lg:grid-cols-2' : '' }}">
                @if ($company->vision)
                    <article class="tone-primary relative flex flex-col overflow-hidden rounded-brand bg-primary p-8 text-on-primary sm:p-12" {!! $ds->reveal(0) !!}>
                        <div class="pointer-events-none absolute -right-16 -bottom-16 size-72 rounded-full border-[40px] border-on-primary/10"></div>
                        <div class="relative flex items-center gap-4">
                            <span class="inline-flex size-12 items-center justify-center rounded-full bg-on-primary/15"><x-icon name="eye" class="size-6" /></span>
                            <h3 class="heading text-3xl">Visi</h3>
                        </div>
                        <p class="heading relative mt-10 text-[clamp(1.5rem,2.6vw,2.25rem)] leading-snug">{{ $company->vision }}</p>
                    </article>
                @endif
                @if ($mission)
                    <article class="rounded-brand border border-line bg-card p-8 sm:p-12" {!! $ds->reveal(1) !!}>
                        <div class="flex items-center gap-4">
                            <span class="inline-flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary"><x-icon name="rocket" class="size-6" /></span>
                            <h3 class="heading text-3xl">Misi</h3>
                        </div>
                        <ol class="mt-8 space-y-5">
                            @foreach ($mission as $i => $item)
                                <li class="flex gap-4">
                                    <span class="inline-flex size-7 shrink-0 items-center justify-center rounded-full border border-line font-mono text-xs text-primary">{{ $i + 1 }}</span>
                                    <span class="pt-0.5 text-ink">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </article>
                @endif
            </div>
        @endif

        @if ($values)
            <div class="mt-5 flex flex-wrap items-center gap-2" {!! $ds->reveal(2) !!}>
                <span class="mr-2 text-xs font-semibold tracking-[0.2em] text-muted uppercase">Nilai kami</span>
                @foreach (array_slice($values, 0, 8) as $value)
                    <span class="rounded-full border border-line px-3.5 py-1.5 text-sm text-ink">{{ \Illuminate\Support\Str::limit(trim(preg_split('/\s+[—–-]\s+|:\s+/u', $value, 2)[0]), 40) }}</span>
                @endforeach
            </div>
        @endif

        @if ($company->history)
            <div class="mt-10 border-y border-line" x-data="{ open: false }" {!! $ds->reveal(3) !!}>
                <button type="button" class="flex min-h-16 w-full items-center justify-between gap-4 py-5 text-left" @click="open = ! open" :aria-expanded="open.toString()" aria-controls="about-history">
                    <span class="heading text-xl">Sejarah perusahaan @if ($company->established_year)<span class="text-muted">· sejak {{ $company->established_year }}</span>@endif</span>
                    <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full border transition" :class="open ? 'rotate-45 bg-primary text-on-primary border-primary' : 'border-line'">
                        <x-icon name="plus" class="size-4" />
                    </span>
                </button>
                <div id="about-history" x-show="open" x-collapse x-cloak>
                    <div class="site-prose max-w-3xl pb-8 text-muted">{!! $company->history !!}</div>
                </div>
            </div>
        @endif
    </div>
</section>
