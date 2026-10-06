{{-- Minimal projects: text list with year / client columns and an image revealed on hover. --}}
<section id="projects" class="mx-auto max-w-4xl px-6">
    <div class="grid gap-8 border-t border-neutral-200 py-16 md:grid-cols-4 md:py-24">
        <div>
            <p class="text-xs text-neutral-400 tabular-nums">({{ str_pad($index, 2, '0', STR_PAD_LEFT) }})</p>
            <h2 class="mt-1 text-sm font-medium text-neutral-950">Proyek</h2>
        </div>
        <div class="md:col-span-3">
            <p class="font-heading text-2xl leading-snug font-medium tracking-tight text-neutral-950 md:text-3xl">{{ $section->title ?: 'Pekerjaan terpilih.' }}</p>
            @if ($section->subtitle)<p class="mt-4 text-neutral-500">{{ $section->subtitle }}</p>@endif

            <div class="mt-10 hidden grid-cols-12 gap-4 pb-3 text-xs text-neutral-400 sm:grid">
                <span class="col-span-1">Tahun</span>
                <span class="col-span-7">Proyek</span>
                <span class="col-span-4">Klien</span>
            </div>
            <ul class="border-b border-neutral-200 max-sm:mt-10">
                @foreach ($company->projects as $project)
                    <li x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false" class="relative border-t border-neutral-200">
                        @php($tag = $project->url ? 'a' : 'div')
                        <{{ $tag }} @if ($project->url) href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" @endif class="grid grid-cols-12 items-baseline gap-x-4 gap-y-1 py-5">
                            <span class="col-span-12 text-sm text-neutral-400 tabular-nums sm:col-span-1">{{ $project->year ?: '—' }}</span>
                            <span class="col-span-12 sm:col-span-7">
                                <span class="font-heading text-lg font-medium tracking-tight text-neutral-950 transition sm:text-xl" :class="hover && 'italic'">{{ $project->title }}@if ($project->url)<x-icon name="arrow-up-right" class="ml-1 inline size-4 align-baseline text-neutral-400" />@endif</span>
                                @if ($project->category)<span class="mt-0.5 block text-sm text-neutral-400">{{ $project->category }}</span>@endif
                            </span>
                            <span class="col-span-12 text-sm text-neutral-500 sm:col-span-4">{{ $project->client ?: '—' }}</span>
                        </{{ $tag }}>
                        <div x-cloak x-show="hover" x-transition.opacity.duration.200ms class="pointer-events-none absolute top-1/2 right-0 z-10 hidden w-56 -translate-y-1/2 lg:block xl:-right-64">
                            <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[4/3] w-full object-cover grayscale" />
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
