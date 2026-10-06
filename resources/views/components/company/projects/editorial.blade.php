{{-- Projects: Editorial — magazine-style asymmetric 12-column grid of large and small items with numbered captions. --}}
@php
    // Repeating asymmetric pattern (literal classes for the Tailwind scanner).
    $layout = [
        ['span' => 'lg:col-span-7', 'ratio' => 'aspect-[4/3]', 'offset' => ''],
        ['span' => 'lg:col-span-5', 'ratio' => 'aspect-[4/5]', 'offset' => 'lg:mt-32'],
        ['span' => 'lg:col-span-4 lg:col-start-2', 'ratio' => 'aspect-[3/4]', 'offset' => ''],
        ['span' => 'lg:col-span-6 lg:col-start-7', 'ratio' => 'aspect-[16/11]', 'offset' => 'lg:mt-24'],
        ['span' => 'lg:col-span-8 lg:col-start-3', 'ratio' => 'aspect-[16/9]', 'offset' => ''],
    ];
@endphp
<section id="projects" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="grid gap-6 border-b border-line pb-10 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-8" {!! $ds->reveal(0) !!}>
                {!! $ds->eyebrow('Portofolio', $index) !!}
                <h2 class="heading mt-4 text-h2">{{ $section->title ?: 'Karya yang Kami Banggakan' }}</h2>
            </div>
            @if ($section->subtitle)
                <p class="text-muted lg:col-span-4" {!! $ds->reveal(1) !!}>{{ $section->subtitle }}</p>
            @endif
        </div>

        <div class="mt-14 grid gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-12 lg:gap-y-20">
            @foreach ($company->projects as $project)
                @php($cell = $layout[$loop->index % count($layout)])
                @php($tag = $project->url ? 'a' : 'div')
                <article class="group {{ $cell['span'] }} {{ $cell['offset'] }} {{ $loop->index % 5 === 4 ? 'sm:col-span-2' : '' }}" {!! $ds->reveal($loop->index % 2) !!}>
                    <{{ $tag }} @if ($project->url) href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" @endif class="block overflow-hidden {{ $ds->img() }}">
                        <x-site.img :src="$project->url('image')" :alt="$project->title" class="{{ $cell['ratio'] }} w-full object-cover transition duration-[1400ms] group-hover:scale-[1.04]" />
                    </{{ $tag }}>
                    <div class="mt-5 grid grid-cols-[auto_1fr] gap-x-5">
                        <span class="pt-1.5 font-mono text-sm text-primary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <div class="min-w-0 border-l border-line pl-5">
                            <h3 class="heading text-2xl leading-tight break-words sm:text-3xl">
                                @if ($project->url)<a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="bg-[linear-gradient(currentColor,currentColor)] bg-[length:0%_1px] bg-left-bottom bg-no-repeat transition-[background-size] duration-500 group-hover:bg-[length:100%_1px]">{{ $project->title }}</a>@else{{ $project->title }}@endif
                            </h3>
                            <p class="mt-2 text-xs tracking-[0.14em] text-muted uppercase">
                                {{ collect([$project->category, $project->client, $project->year])->filter()->join(' / ') }}
                            </p>
                            @if ($project->description)<p class="mt-3 line-clamp-3 max-w-xl text-sm leading-relaxed text-muted">{{ $project->description }}</p>@endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
