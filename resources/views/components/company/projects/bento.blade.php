{{-- Projects: Bento — bento grid of mixed tile sizes with overlaid titles. --}}
@php
    // 7-tile pattern that tiles a 4-column grid without gaps (literal classes for Tailwind).
    $tiles = [
        'sm:col-span-2 lg:col-span-2 lg:row-span-2',
        'lg:col-span-2',
        '',
        '',
        '',
        '',
        'lg:col-span-2',
    ];
    // Overrides for the last, incomplete group so the grid never ends with holes.
    $tail = [
        1 => [0 => 'sm:col-span-2 lg:col-span-4 lg:row-span-2'],
        2 => [1 => 'sm:col-span-2 lg:col-span-2 lg:row-span-2'],
        3 => [2 => 'lg:col-span-2'],
        4 => [3 => 'sm:col-span-2'],
        5 => [4 => 'lg:col-span-4'],
        6 => [4 => 'lg:col-span-2', 5 => 'sm:col-span-2 lg:col-span-2'],
    ];
    $count = $company->projects->count();
    $rem = $count % 7;
@endphp
<section id="projects" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Portofolio', 'title' => $section->title ?: 'Sorotan Proyek', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 grid auto-rows-[17rem] grid-flow-dense gap-4 sm:grid-cols-2 lg:auto-rows-[15.5rem] lg:grid-cols-4">
            @foreach ($company->projects as $project)
                @php
                    $c = $loop->index % 7;
                    $span = ($rem && $loop->index >= $count - $rem) ? ($tail[$rem][$c] ?? $tiles[$c]) : $tiles[$c];
                    $big = str_contains($span, 'row-span-2');
                @endphp
                @php($tag = $project->url ? 'a' : 'div')
                <{{ $tag }} @if ($project->url) href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" @endif
                    class="group relative isolate block overflow-hidden {{ $ds->img() }} {{ $span }}" {!! $ds->reveal($loop->index % 4) !!}>
                    <x-site.img :src="$project->url('image')" :alt="$project->title" class="absolute inset-0 -z-10 size-full object-cover transition duration-[1200ms] group-hover:scale-105" />
                    <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/80 via-black/25 to-transparent"></div>
                    <div class="flex h-full flex-col justify-between p-5 text-white sm:p-6">
                        <div class="flex items-start justify-between gap-3">
                            @if ($project->category)
                                <span class="rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold tracking-wide text-white ring-1 ring-white/25 backdrop-blur">{{ $project->category }}</span>
                            @else
                                <span></span>
                            @endif
                            @if ($project->url)<span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-white/15 backdrop-blur transition group-hover:rotate-45"><x-icon name="arrow-up-right" class="size-4" /></span>@endif
                        </div>
                        <div>
                            @if ($project->year || $project->client)
                                <p class="text-xs text-white/75">{{ collect([$project->year, $project->client])->filter()->join(' · ') }}</p>
                            @endif
                            <h3 class="heading mt-1 leading-tight break-words {{ $big ? 'text-2xl sm:text-3xl lg:text-4xl' : 'text-xl' }}">{{ $project->title }}</h3>
                            @if ($big && $project->description)
                                <p class="mt-3 line-clamp-2 max-w-md text-sm text-white/80">{{ $project->description }}</p>
                            @endif
                        </div>
                    </div>
                </{{ $tag }}>
            @endforeach
        </div>
    </div>
</section>
