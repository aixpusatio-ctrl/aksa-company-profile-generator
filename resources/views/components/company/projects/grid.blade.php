{{-- Projects: Grid — category filter + 3-column project cards. --}}
@php($categories = $company->projects->pluck('category')->filter()->unique()->values())
<section id="projects" class="{{ $ds->section($tone) }}" x-data="{ filter: 'all' }">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Portofolio', 'title' => $section->title ?: 'Proyek Pilihan', 'subtitle' => $section->subtitle, 'number' => $index])
        @if ($categories->count() > 1)
            <div class="mt-10 flex flex-wrap gap-2 {{ $ds->align() === 'center' ? 'justify-center' : '' }}" {!! $ds->reveal(1) !!}>
                <button type="button" @click="filter = 'all'" class="rounded-btn border px-4 py-2 text-sm font-medium transition" :class="filter === 'all' ? 'border-primary bg-primary text-on-primary' : 'border-line text-muted hover:text-ink'">Semua</button>
                @foreach ($categories as $category)
                    <button type="button" @click="filter = @js($category)" class="rounded-btn border px-4 py-2 text-sm font-medium transition" :class="filter === @js($category) ? 'border-primary bg-primary text-on-primary' : 'border-line text-muted hover:text-ink'">{{ $category }}</button>
                @endforeach
            </div>
        @endif
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->projects as $project)
                <article x-show="filter === 'all' || filter === @js($project->category)" x-transition.opacity class="{{ $ds->card('group overflow-hidden') }}" {!! $ds->reveal($loop->index) !!}>
                    <div class="relative overflow-hidden">
                        <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[4/3] w-full object-cover transition duration-700 group-hover:scale-105" />
                        @if ($project->year)<span class="absolute top-4 left-4 rounded-full bg-black/60 px-3 py-1 text-xs font-semibold text-white backdrop-blur">{{ $project->year }}</span>@endif
                    </div>
                    <div class="p-6">
                        @if ($project->category)<p class="text-xs font-semibold tracking-wide text-primary uppercase">{{ $project->category }}</p>@endif
                        <h3 class="heading mt-1 text-lg">
                            @if ($project->url)<a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary">{{ $project->title }}</a>@else{{ $project->title }}@endif
                        </h3>
                        @if ($project->description)<p class="mt-2 line-clamp-2 text-sm text-muted">{{ $project->description }}</p>@endif
                        <p class="mt-4 flex flex-wrap gap-x-4 gap-y-1 border-t border-line pt-4 text-xs text-muted">
                            @if ($project->client)<span class="inline-flex items-center gap-1"><x-icon name="building" class="size-3.5" /> {{ $project->client }}</span>@endif
                            @if ($project->location)<span class="inline-flex items-center gap-1"><x-icon name="map-pin" class="size-3.5" /> {{ $project->location }}</span>@endif
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
