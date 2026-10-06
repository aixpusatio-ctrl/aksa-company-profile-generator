{{-- Corporate portfolio: category filter + 3-column cards with overlay details. --}}
@php($categories = $company->projects->pluck('category')->filter()->unique()->values())
<section id="projects" class="bg-slate-50 py-20 lg:py-28" x-data="{ filter: 'all' }">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold tracking-widest text-primary uppercase">Portofolio</p>
            <h2 class="mt-3 font-heading text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $section->title ?: 'Proyek yang Telah Kami Kerjakan' }}</h2>
            @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
        </div>
        @if ($categories->count() > 1)
            <div class="mt-10 flex flex-wrap justify-center gap-2">
                <button type="button" @click="filter = 'all'" class="rounded-btn px-4 py-2 text-sm font-semibold transition" :class="filter === 'all' ? 'bg-primary text-on-primary' : 'bg-white text-slate-600 hover:text-primary'">Semua</button>
                @foreach ($categories as $category)
                    <button type="button" @click="filter = @js($category)" class="rounded-btn px-4 py-2 text-sm font-semibold transition" :class="filter === @js($category) ? 'bg-primary text-on-primary' : 'bg-white text-slate-600 hover:text-primary'">{{ $category }}</button>
                @endforeach
            </div>
        @endif
        <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->projects as $project)
                <article x-show="filter === 'all' || filter === @js($project->category)" x-transition class="group overflow-hidden rounded-brand bg-white shadow-sm">
                    <div class="relative overflow-hidden">
                        <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105" />
                        @if ($project->year)
                            <span class="absolute top-4 left-4 rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-slate-900">{{ $project->year }}</span>
                        @endif
                    </div>
                    <div class="p-6">
                        <p class="text-xs font-semibold tracking-wide text-primary uppercase">{{ $project->category }}</p>
                        <h3 class="mt-1 font-heading text-lg font-bold text-slate-900">
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary">{{ $project->title }}</a>
                            @else
                                {{ $project->title }}
                            @endif
                        </h3>
                        <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $project->description }}</p>
                        <div class="mt-4 flex flex-wrap gap-x-4 gap-y-1 border-t border-slate-100 pt-4 text-xs text-slate-500">
                            @if ($project->client)<span class="flex items-center gap-1"><x-icon name="building" class="size-3.5" /> {{ $project->client }}</span>@endif
                            @if ($project->location)<span class="flex items-center gap-1"><x-icon name="map-pin" class="size-3.5" /> {{ $project->location }}</span>@endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
