{{-- Creative portfolio: asymmetric grid with large/small tiles and hover zoom. --}}
@php($pattern = [
    ['lg:col-span-7', 'aspect-[4/3]'],
    ['lg:col-span-5 lg:mt-24', 'aspect-[4/5]'],
    ['lg:col-span-5', 'aspect-square'],
    ['lg:col-span-7 lg:-mt-24', 'aspect-[16/11]'],
    ['lg:col-span-4', 'aspect-[4/5]'],
    ['lg:col-span-8', 'aspect-[16/10]'],
])
<section id="projects" class="py-20 lg:py-28">
    <div class="mx-auto max-w-[90rem] px-6 sm:px-10">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <h2 class="font-heading text-5xl leading-[0.9] font-extrabold tracking-tighter text-neutral-950 md:text-7xl lg:text-8xl">
                {{ $section->title ?: 'Karya pilihan' }}<span class="text-primary">.</span>
            </h2>
            <p class="max-w-sm text-neutral-600">{{ $section->subtitle ?: 'Beberapa proyek favorit yang membuat kami bangga — dan klien kami tersenyum lebar.' }}</p>
        </div>

        <div class="mt-16 grid gap-6 md:grid-cols-2 lg:grid-cols-12 lg:gap-8">
            @foreach ($company->projects as $project)
                @php([$span, $aspect] = $pattern[$loop->index % count($pattern)])
                <article class="group {{ $span }}">
                    @if ($project->url)<a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="block">@endif
                    <div class="relative overflow-hidden rounded-brand bg-neutral-200">
                        <x-site.img :src="$project->url('image')" :alt="$project->title" class="{{ $aspect }} w-full object-cover transition duration-700 ease-out group-hover:scale-110" />
                        <div class="absolute inset-0 bg-secondary/0 transition duration-500 group-hover:bg-secondary/20"></div>
                        <span class="absolute top-5 left-5 rounded-full bg-white px-3 py-1 text-xs font-bold text-neutral-950">{{ $project->category ?: 'Proyek' }}</span>
                        <span class="absolute right-5 bottom-5 inline-flex size-16 scale-0 items-center justify-center rounded-full bg-primary text-on-primary transition duration-500 group-hover:scale-100 group-hover:rotate-45">
                            <x-icon name="arrow-up-right" class="size-6 -rotate-45" />
                        </span>
                    </div>
                    <div class="mt-5 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-heading text-2xl font-extrabold tracking-tight text-neutral-950 md:text-3xl">{{ $project->title }}</h3>
                            <p class="mt-1 text-sm text-neutral-500">{{ collect([$project->client, $project->location])->filter()->implode(' · ') }}</p>
                        </div>
                        @if ($project->year)<span class="shrink-0 rounded-full border border-neutral-300 px-3 py-1 text-sm font-bold text-neutral-600">{{ $project->year }}</span>@endif
                    </div>
                    @if ($project->url)</a>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
