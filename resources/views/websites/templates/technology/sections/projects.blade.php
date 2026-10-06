{{-- Technology projects: large horizontal rows with index, details and wide image. --}}
<section id="projects" class="py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <p class="font-mono text-sm text-primary">// proyek</p>
                <h2 class="mt-4 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Dikirim ke produksi' }}</h2>
                @if ($section->subtitle)<p class="mt-5 text-slate-400">{{ $section->subtitle }}</p>@endif
            </div>
            <p class="font-mono text-xs text-slate-500">{{ $company->projects->count() }} entri · diurutkan terbaru</p>
        </div>

        <div class="mt-14 border-t border-white/10">
            @foreach ($company->projects as $project)
                <article class="group grid gap-6 border-b border-white/10 py-10 transition md:grid-cols-12 md:gap-8 lg:py-12">
                    <div class="font-mono text-sm text-slate-600 transition group-hover:text-primary md:col-span-1">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="flex flex-col md:col-span-5">
                        <div class="flex flex-wrap gap-2 font-mono text-[11px]">
                            @if ($project->category)<span class="rounded border border-primary/30 bg-primary/10 px-2 py-0.5 text-primary">{{ $project->category }}</span>@endif
                            @if ($project->year)<span class="rounded border border-white/10 px-2 py-0.5 text-slate-400">{{ $project->year }}</span>@endif
                        </div>
                        <h3 class="mt-4 font-heading text-2xl font-semibold text-white transition group-hover:text-primary sm:text-3xl">
                            @if ($project->url)
                                <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-start gap-2">{{ $project->title }} <x-icon name="arrow-up-right" class="mt-1 size-5 shrink-0" /></a>
                            @else
                                {{ $project->title }}
                            @endif
                        </h3>
                        <p class="mt-4 leading-relaxed text-slate-400">{{ $project->description }}</p>
                        <dl class="mt-auto grid grid-cols-2 gap-4 pt-8 font-mono text-xs">
                            @if ($project->client)<div><dt class="text-slate-600">client</dt><dd class="mt-1 text-slate-300">{{ $project->client }}</dd></div>@endif
                            @if ($project->location)<div><dt class="text-slate-600">location</dt><dd class="mt-1 text-slate-300">{{ $project->location }}</dd></div>@endif
                        </dl>
                    </div>
                    <div class="md:col-span-6">
                        <div class="relative overflow-hidden rounded-brand border border-white/10">
                            <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[16/9] w-full object-cover grayscale-[60%] transition duration-700 group-hover:scale-[1.03] group-hover:grayscale-0" />
                            <div class="absolute inset-0 bg-linear-to-tr from-primary/20 to-transparent opacity-0 mix-blend-screen transition group-hover:opacity-100"></div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
