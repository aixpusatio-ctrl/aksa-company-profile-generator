{{-- Projects: List hover — text index rows (year, title, client, category); hovering a row reveals a floating image preview that follows the cursor (desktop), thumbnails on mobile. --}}
<section id="projects" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Portofolio', 'title' => $section->title ?: 'Indeks Proyek', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14" {!! $ds->reveal(1) !!}>
            <div class="hidden grid-cols-[5rem_minmax(0,1fr)_minmax(0,14rem)_minmax(0,11rem)_2.5rem] gap-6 border-b border-line pb-3 text-[11px] font-semibold tracking-[0.16em] text-muted uppercase lg:grid">
                <span>Tahun</span><span>Proyek</span><span>Klien</span><span>Kategori</span><span></span>
            </div>
            <ul class="border-b border-line">
                @foreach ($company->projects as $project)
                    @php($tag = $project->url ? 'a' : 'div')
                    <li class="group relative border-t border-line first:border-t-0 hover:z-10 lg:first:border-t-0"
                        x-data @mousemove="$el.style.setProperty('--mx', ($event.clientX - $el.getBoundingClientRect().left) + 'px')">
                        <{{ $tag }} @if ($project->url) href="{{ $project->url }}" target="_blank" rel="noopener noreferrer" @endif
                            class="flex items-center gap-4 py-5 transition lg:grid lg:grid-cols-[5rem_minmax(0,1fr)_minmax(0,14rem)_minmax(0,11rem)_2.5rem] lg:gap-6 lg:py-7">
                            <div class="shrink-0 overflow-hidden rounded-brand lg:hidden">
                                <x-site.img :src="$project->url('image')" :alt="$project->title" class="size-20 object-cover" />
                            </div>
                            <span class="hidden font-mono text-sm text-muted lg:block">{{ $project->year ?: '—' }}</span>
                            <div class="min-w-0 flex-1">
                                <h3 class="heading text-xl leading-tight break-words transition duration-300 group-hover:text-primary sm:text-2xl lg:text-4xl lg:group-hover:translate-x-2">{{ $project->title }}</h3>
                                <p class="mt-1 text-sm text-muted lg:hidden">{{ collect([$project->year, $project->client, $project->category])->filter()->join(' · ') }}</p>
                            </div>
                            <span class="hidden truncate text-sm text-muted lg:block">{{ $project->client ?: '—' }}</span>
                            <span class="hidden lg:block">
                                @if ($project->category)<span class="inline-flex max-w-full truncate rounded-full border border-line px-3 py-1 text-xs text-muted">{{ $project->category }}</span>@endif
                            </span>
                            <span class="hidden size-10 items-center justify-center rounded-full border border-line text-ink transition group-hover:border-primary group-hover:bg-primary group-hover:text-on-primary lg:inline-flex">
                                <x-icon :name="$project->url ? 'arrow-up-right' : 'arrow-right'" class="size-4" />
                            </span>
                        </{{ $tag }}>
                        <div class="pointer-events-none absolute top-1/2 left-[var(--mx,60%)] z-20 hidden w-72 -translate-x-1/2 -translate-y-1/2 scale-90 rotate-[-3deg] overflow-hidden opacity-0 shadow-2xl transition-[opacity,transform] duration-300 ease-out group-hover:scale-100 group-hover:rotate-0 group-hover:opacity-100 lg:block xl:w-80 {{ $ds->img() }}" aria-hidden="true">
                            <x-site.img :src="$project->url('image')" alt="" class="aspect-[4/3] w-full object-cover" />
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
