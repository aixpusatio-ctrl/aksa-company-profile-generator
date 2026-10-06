{{-- Projects: Case study — detailed case rows: large image, project summary and a meta table (client, location, year, category). --}}
@php($projects = $company->projects)
<section id="projects" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Studi Kasus', 'title' => $section->title ?: 'Bukti Nyata Hasil Kerja Kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <p class="shrink-0 font-mono text-xs text-muted" {!! $ds->reveal(1) !!}>{{ str_pad($projects->count(), 2, '0', STR_PAD_LEFT) }} studi kasus</p>
        </div>

        <div class="mt-14 border-b border-line">
            @foreach ($projects as $project)
                <article class="group grid gap-8 border-t border-line py-10 sm:py-14 lg:grid-cols-12 lg:gap-12">
                    <div class="lg:col-span-6" {!! $ds->reveal(0) !!}>
                        <div class="overflow-hidden {{ $ds->img() }}">
                            <x-site.img :src="$project->url('image')" :alt="$project->title" class="aspect-[16/10] w-full object-cover transition duration-[1200ms] group-hover:scale-[1.03]" />
                        </div>
                    </div>
                    <div class="flex flex-col lg:col-span-6" {!! $ds->reveal(1) !!}>
                        <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.18em] text-muted uppercase">
                            <span class="font-mono text-primary">Kasus {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            @if ($project->category)<span class="h-px w-8 bg-line"></span><span>{{ $project->category }}</span>@endif
                        </p>
                        <h3 class="heading mt-4 text-3xl break-words sm:text-4xl">{{ $project->title }}</h3>

                        @if ($project->description)
                            <div class="mt-6 grid gap-5 sm:grid-cols-[8rem_1fr]">
                                <p class="text-xs font-semibold tracking-[0.16em] text-ink uppercase sm:pt-1">Ringkasan</p>
                                <p class="leading-relaxed text-muted">{{ $project->description }}</p>
                            </div>
                        @endif

                        <dl class="mt-8 grid grid-cols-2 border-t border-line text-sm sm:grid-cols-4">
                            @foreach (['Klien' => $project->client, 'Lokasi' => $project->location, 'Tahun' => $project->year, 'Kategori' => $project->category] as $label => $value)
                                <div class="border-b border-line py-4 pr-4 sm:border-b-0">
                                    <dt class="text-[11px] tracking-[0.14em] text-muted uppercase">{{ $label }}</dt>
                                    <dd class="mt-1 font-medium break-words text-ink">{{ filled($value) ? $value : '—' }}</dd>
                                </div>
                            @endforeach
                        </dl>

                        @if ($project->url)
                            <div class="mt-8">@include('components.company.partials.button', ['href' => $project->url, 'label' => 'Lihat studi kasus', 'kind' => 'secondary', 'external' => true, 'class' => 'w-full sm:w-auto'])</div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
