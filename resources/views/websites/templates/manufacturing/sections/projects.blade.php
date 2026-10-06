{{-- Manufacturing projects: client case list in table-like rows. --}}
<section id="projects" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <p class="font-mono text-xs font-semibold tracking-widest text-primary uppercase">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }} — Referensi Klien</p>
                <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight text-slate-900 uppercase md:text-4xl">{{ $section->title ?: 'Proyek & Pengiriman' }}</h2>
                @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
            </div>
            <p class="font-mono text-xs text-slate-500 uppercase">{{ $company->projects->count() }} studi kasus terpilih</p>
        </div>

        <div class="mt-10 border-t-2 border-slate-900">
            <div class="hidden grid-cols-12 gap-6 border-b border-slate-200 py-3 font-mono text-[11px] font-semibold tracking-widest text-slate-500 uppercase md:grid">
                <span class="col-span-1">No.</span>
                <span class="col-span-5">Proyek</span>
                <span class="col-span-3">Klien</span>
                <span class="col-span-2">Lokasi</span>
                <span class="col-span-1 text-right">Tahun</span>
            </div>
            @foreach ($company->projects as $project)
                <article x-data="{ open: false }" class="border-b border-slate-200">
                    <button type="button" @click="open = !open" class="group grid w-full grid-cols-12 items-center gap-x-6 gap-y-1 py-5 text-left transition hover:bg-slate-50 md:px-0">
                        <span class="col-span-2 font-mono text-sm text-primary md:col-span-1">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="col-span-10 md:col-span-5">
                            <span class="block font-heading text-lg font-bold text-slate-900 group-hover:text-primary">{{ $project->title }}</span>
                            @if ($project->category)<span class="font-mono text-[11px] text-slate-500 uppercase">{{ $project->category }}</span>@endif
                        </span>
                        <span class="col-span-10 col-start-3 text-sm text-slate-700 md:col-span-3 md:col-start-auto">{{ $project->client ?: '—' }}</span>
                        <span class="col-span-10 col-start-3 text-sm text-slate-500 md:col-span-2 md:col-start-auto">{{ $project->location ?: '—' }}</span>
                        <span class="col-span-12 flex items-center justify-end gap-3 font-mono text-sm text-slate-900 md:col-span-1">
                            {{ $project->year }}
                            <x-icon name="plus" class="size-4 text-slate-400 transition" ::class="open && 'rotate-45 text-primary'" />
                        </span>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <div class="grid gap-6 pb-8 md:grid-cols-12">
                            <div class="md:col-span-4 md:col-start-2">
                                <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[16/10] w-full rounded-brand object-cover" />
                            </div>
                            <div class="md:col-span-6">
                                <p class="text-sm leading-relaxed text-slate-600">{{ $project->description }}</p>
                                @if ($project->url)
                                    <a href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex items-center gap-2 font-mono text-xs font-semibold tracking-wider text-primary uppercase">Lihat detail <x-icon name="arrow-up-right" class="size-4" /></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
