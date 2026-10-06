{{-- About: Bento — mosaic of tiles: story, vision, mission, figures, photo and values. --}}
@php
    $stats = array_slice($company->stats(), 0, 2);
    $mission = array_slice($company->missionItems(), 0, 4);
    $values = array_slice($company->valueItems(), 0, 6);
    $image = $company->gallery->first()?->url('image') ?: $company->url('hero_image');
    $valueLabel = fn ($v) => trim(preg_split('/\s+[—–-]\s+|:\s+/u', $v, 2)[0]);
@endphp
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Tentang Kami', 'title' => $section->title ?: 'Mengenal '.$company->name, 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 grid auto-rows-auto gap-4 sm:grid-cols-2 lg:grid-cols-6">
            {{-- Story --}}
            <article class="{{ $ds->card('p-7 sm:p-9 sm:col-span-2 lg:row-span-2', false) }} {{ $company->vision ? 'lg:col-span-4' : 'lg:col-span-6' }}" {!! $ds->reveal(0) !!}>
                <span class="inline-flex size-11 items-center justify-center rounded-brand bg-primary/10 text-primary"><x-icon name="building" class="size-5" /></span>
                <h3 class="heading mt-6 text-2xl">{{ $company->tagline ?: $company->name }}</h3>
                <div class="site-prose mt-4 text-muted">{!! $company->about ?: e($company->description) !!}</div>
            </article>

            {{-- Vision --}}
            @if ($company->vision)
                <article class="tone-primary relative flex flex-col justify-between overflow-hidden rounded-brand bg-primary p-7 text-on-primary sm:col-span-2 lg:col-span-2 lg:row-span-2" {!! $ds->reveal(1) !!}>
                    <div class="absolute -top-10 -right-10 size-40 rounded-full bg-on-primary/10"></div>
                    <x-icon name="eye" class="relative size-6 opacity-80" />
                    <div class="relative mt-10">
                        <p class="text-xs font-semibold tracking-[0.2em] uppercase opacity-80">Visi</p>
                        <p class="heading mt-3 text-xl leading-snug sm:text-2xl">{{ $company->vision }}</p>
                    </div>
                </article>
            @endif

            {{-- Stats --}}
            @foreach ($stats as $i => $stat)
                <div class="{{ $ds->card('flex flex-col justify-between gap-6 p-7 lg:col-span-1', false) }} {{ count($stats) === 1 ? 'sm:col-span-2 lg:col-span-2' : '' }}" {!! $ds->reveal($i + 2) !!}>
                    <p class="heading text-4xl text-primary sm:text-5xl" data-count>{{ $stat['value'] }}</p>
                    <p class="text-sm text-muted">{{ $stat['label'] }}</p>
                </div>
            @endforeach

            {{-- Image --}}
            <div class="relative min-h-64 overflow-hidden rounded-brand sm:col-span-2 {{ $stats ? 'lg:col-span-2' : 'lg:col-span-4' }}" {!! $ds->reveal(4) !!}>
                <x-site.img :src="$image" :alt="$company->name" icon="photo" class="absolute inset-0 size-full object-cover transition duration-700 hover:scale-105" />
                @if ($company->established_year)
                    <span class="absolute bottom-4 left-4 rounded-full bg-black/55 px-3 py-1 text-xs font-semibold text-white backdrop-blur">Sejak {{ $company->established_year }}</span>
                @endif
            </div>

            {{-- Mission --}}
            @if ($mission)
                <article class="{{ $ds->card('p-7 sm:col-span-2 lg:col-span-2', false) }}" {!! $ds->reveal(5) !!}>
                    <p class="text-xs font-semibold tracking-[0.2em] text-primary uppercase">Misi</p>
                    <ul class="mt-5 space-y-3">
                        @foreach ($mission as $item)
                            <li class="flex gap-3 text-sm text-ink">
                                <x-icon name="check-circle" class="mt-0.5 size-4 shrink-0 text-primary" />
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </article>
            @endif

            {{-- Values --}}
            @if ($values)
                <article class="{{ $ds->card('p-7 sm:col-span-2', false) }} {{ $mission ? 'lg:col-span-6' : 'lg:col-span-2' }}" {!! $ds->reveal(6) !!}>
                    <p class="text-xs font-semibold tracking-[0.2em] text-primary uppercase">Nilai Perusahaan</p>
                    <ul class="mt-5 flex flex-wrap gap-2">
                        @foreach ($values as $value)
                            <li class="rounded-full border border-line px-3.5 py-1.5 text-sm text-ink">{{ \Illuminate\Support\Str::limit($valueLabel($value), 40) }}</li>
                        @endforeach
                    </ul>
                </article>
            @endif
        </div>
    </div>
</section>
