{{-- Consulting projects: insight-style article cards (feature + list). --}}
@php($featured = $company->projects->first())
<section id="projects" class="border-t border-stone-200 bg-white py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex flex-col justify-between gap-6 border-b border-stone-200 pb-10 md:flex-row md:items-end">
            <div>
                <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Studi Kasus & Insight</p>
                <h2 class="mt-6 font-heading text-4xl leading-tight text-stone-900 md:text-5xl">{{ $section->title ?: 'Dampak yang terukur' }}</h2>
                @if ($section->subtitle)<p class="mt-4 max-w-xl text-stone-500">{{ $section->subtitle }}</p>@endif
            </div>
            <a href="{{ $site->anchor('contact') }}" class="text-sm tracking-wide text-stone-900 underline decoration-stone-300 underline-offset-8 hover:decoration-stone-900">Diskusikan tantangan Anda</a>
        </div>

        @if ($featured)
            <article class="group mt-14 grid gap-10 lg:grid-cols-12 lg:items-center">
                <div class="overflow-hidden lg:col-span-7">
                    <x-site.img :src="$featured->url('image')" :alt="$featured->title" class="aspect-[16/10] w-full object-cover transition duration-700 group-hover:scale-[1.02]" />
                </div>
                <div class="lg:col-span-5">
                    <p class="flex flex-wrap items-center gap-3 text-xs tracking-[0.2em] text-stone-400 uppercase">
                        <span class="text-primary">{{ $featured->category ?: 'Studi Kasus' }}</span>
                        @if ($featured->year)<span class="h-px w-5 bg-stone-300"></span> {{ $featured->year }}@endif
                    </p>
                    <h3 class="mt-5 font-heading text-3xl leading-tight text-stone-900 md:text-4xl">{{ $featured->title }}</h3>
                    <p class="mt-5 leading-relaxed text-stone-600">{{ $featured->description }}</p>
                    @if ($featured->client)<p class="mt-6 text-sm text-stone-500">Klien — <span class="text-stone-900">{{ $featured->client }}</span></p>@endif
                    @if ($featured->url)
                        <a href="{{ $featured->url }}" target="_blank" rel="noopener noreferrer" class="mt-6 inline-flex items-center gap-2 text-sm text-primary">Baca selengkapnya <x-icon name="arrow-right" class="size-4" /></a>
                    @endif
                </div>
            </article>
        @endif

        <div class="mt-20 grid gap-x-10 gap-y-14 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->projects->skip(1) as $project)
                <article class="group">
                    <div class="overflow-hidden">
                        <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[3/2] w-full object-cover grayscale-[40%] transition duration-700 group-hover:scale-[1.03] group-hover:grayscale-0" />
                    </div>
                    <p class="mt-6 flex flex-wrap items-center gap-3 text-[11px] tracking-[0.2em] text-stone-400 uppercase">
                        <span class="text-primary">{{ $project->category ?: 'Studi Kasus' }}</span>
                        @if ($project->year)<span class="h-px w-4 bg-stone-300"></span> {{ $project->year }}@endif
                    </p>
                    <h3 class="mt-3 font-heading text-2xl leading-snug text-stone-900">
                        @if ($project->url)
                            <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="bg-[linear-gradient(currentColor,currentColor)] bg-[length:0_1px] bg-left-bottom bg-no-repeat transition-[background-size] duration-500 group-hover:bg-[length:100%_1px]">{{ $project->title }}</a>
                        @else
                            <span class="bg-[linear-gradient(currentColor,currentColor)] bg-[length:0_1px] bg-left-bottom bg-no-repeat transition-[background-size] duration-500 group-hover:bg-[length:100%_1px]">{{ $project->title }}</span>
                        @endif
                    </h3>
                    <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-stone-500">{{ $project->description }}</p>
                    @if ($project->client)<p class="mt-4 text-xs text-stone-400">{{ $project->client }}{{ $project->location ? ' · '.$project->location : '' }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
