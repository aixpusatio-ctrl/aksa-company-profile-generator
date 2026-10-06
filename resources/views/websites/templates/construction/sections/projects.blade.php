{{-- Construction projects: masonry grid with category overlay tags and filter. --}}
@php
    $categories = $company->projects->pluck('category')->filter()->unique()->values();
    $ratios = ['aspect-[4/5]', 'aspect-[4/3]', 'aspect-square', 'aspect-[3/4]', 'aspect-[16/11]', 'aspect-[4/5]'];
@endphp
<section id="projects" class="bg-stone-100 py-20 lg:py-32" x-data="{ filter: 'all' }">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="flex flex-col justify-between gap-8 lg:flex-row lg:items-end">
            <div>
                <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase"><span class="h-1 w-10 bg-primary"></span> Portofolio</p>
                <h2 class="mt-5 font-heading text-4xl leading-[0.95] font-bold tracking-tight text-stone-950 uppercase sm:text-5xl lg:text-6xl">{{ $section->title ?: 'Proyek yang Telah Berdiri' }}</h2>
                @if ($section->subtitle)<p class="mt-5 max-w-xl text-stone-500">{{ $section->subtitle }}</p>@endif
            </div>
            @if ($categories->count() > 1)
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="filter = 'all'" class="px-4 py-2.5 font-heading text-sm font-semibold tracking-widest uppercase transition" :class="filter === 'all' ? 'bg-stone-950 text-white' : 'bg-white text-stone-700 hover:bg-stone-200'">Semua</button>
                    @foreach ($categories as $category)
                        <button type="button" @click="filter = @js($category)" class="px-4 py-2.5 font-heading text-sm font-semibold tracking-widest uppercase transition" :class="filter === @js($category) ? 'bg-stone-950 text-white' : 'bg-white text-stone-700 hover:bg-stone-200'">{{ $category }}</button>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-12 gap-4 sm:columns-2 lg:columns-3">
            @foreach ($company->projects as $project)
                <article x-show="filter === 'all' || filter === @js($project->category)" x-transition.opacity class="group relative mb-4 break-inside-avoid overflow-hidden bg-stone-900">
                    <x-site.img :src="$project->url('image')" :alt="$project->title" class="{{ $ratios[$loop->index % count($ratios)] }} w-full object-cover transition duration-700 group-hover:scale-105" />
                    <div class="absolute inset-0 bg-linear-to-t from-stone-950 via-stone-950/30 to-transparent opacity-90 transition group-hover:opacity-100"></div>
                    @if ($project->category)
                        <span class="absolute top-0 left-0 bg-primary px-4 py-2 font-heading text-xs font-bold tracking-[0.2em] text-on-primary uppercase">{{ $project->category }}</span>
                    @endif
                    @if ($project->year)
                        <span class="absolute top-0 right-0 bg-stone-950/80 px-3 py-2 font-heading text-sm font-bold text-white">{{ $project->year }}</span>
                    @endif
                    <div class="absolute inset-x-0 bottom-0 p-6">
                        <h3 class="font-heading text-2xl leading-tight font-bold tracking-wide text-white uppercase">
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary">{{ $project->title }}</a>
                            @else
                                {{ $project->title }}
                            @endif
                        </h3>
                        <div class="grid grid-rows-[0fr] transition-all duration-500 group-hover:grid-rows-[1fr]">
                            <p class="overflow-hidden text-sm text-stone-300"><span class="block pt-2">{{ $project->description }}</span></p>
                        </div>
                        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-1 border-t border-white/20 pt-3 text-xs font-medium tracking-wider text-stone-300 uppercase">
                            @if ($project->location)<span class="flex items-center gap-1.5"><x-icon name="map-pin" class="size-3.5 text-primary" /> {{ $project->location }}</span>@endif
                            @if ($project->client)<span class="flex items-center gap-1.5"><x-icon name="building" class="size-3.5 text-primary" /> {{ $project->client }}</span>@endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
