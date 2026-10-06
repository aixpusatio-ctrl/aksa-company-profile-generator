{{-- About: Timeline — company intro on the left, vertical milestone timeline (founding year + projects) on the right. --}}
@php
    $milestones = collect();
    if ($company->established_year) {
        $milestones->push(['year' => (int) $company->established_year, 'title' => $company->name.' didirikan', 'text' => $company->city ? 'Memulai perjalanan di '.$company->city.'.' : null]);
    }
    $company->projects
        ->filter(fn ($p) => preg_match('/\d{4}/', (string) $p->year))
        ->each(function ($p) use ($milestones) {
            preg_match('/\d{4}/', (string) $p->year, $m);
            $milestones->push(['year' => (int) $m[0], 'title' => $p->title, 'text' => collect([$p->category, $p->client, $p->location])->filter()->take(2)->implode(' · ') ?: null]);
        });
    $milestones = $milestones->sortBy('year')->values()->take(7);
@endphp
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="lg:col-span-5">
            <div class="lg:sticky lg:top-28">
                @include('components.company.partials.heading', ['eyebrow' => 'Perjalanan Kami', 'title' => $section->title ?: 'Tumbuh bersama kepercayaan klien', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
                <div class="site-prose mt-8 text-muted" {!! $ds->reveal(1) !!}>{!! $company->about ?: e($company->description) !!}</div>
                @if ($company->vision)
                    <div class="{{ $ds->card('mt-10 p-6', false) }}" {!! $ds->reveal(2) !!}>
                        <p class="text-xs font-semibold tracking-[0.2em] text-primary uppercase">Visi</p>
                        <p class="heading mt-3 text-lg leading-snug">{{ $company->vision }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="lg:col-span-7">
            <ol>
                @foreach ($milestones as $i => $milestone)
                    <li class="relative grid gap-1 pb-12 pl-10 sm:grid-cols-[6.5rem_1fr] sm:gap-x-14 sm:pl-0" {!! $ds->reveal($i) !!}>
                        <span class="absolute top-2 bottom-0 left-[6px] w-px bg-line sm:left-[calc(6.5rem+1.75rem-1px)]" aria-hidden="true"></span>
                        <span class="absolute top-1 left-0 size-3.5 rounded-full border-2 border-primary bg-surface sm:top-2.5 sm:left-[calc(6.5rem+1.75rem-7px)]" aria-hidden="true"></span>
                        <span class="heading text-sm text-primary sm:text-right sm:text-2xl sm:text-ink">{{ $milestone['year'] }}</span>
                        <div>
                            <h3 class="heading text-h3">{{ $milestone['title'] }}</h3>
                            @if ($milestone['text'])
                                <p class="mt-2 text-sm text-muted">{{ $milestone['text'] }}</p>
                            @endif
                        </div>
                    </li>
                @endforeach
                <li class="relative grid gap-1 pl-10 sm:grid-cols-[6.5rem_1fr] sm:gap-x-14 sm:pl-0" {!! $ds->reveal($milestones->count()) !!}>
                    <span class="absolute top-0 left-[-5px] flex size-6 items-center justify-center rounded-full bg-primary text-on-primary ring-4 ring-primary/20 sm:top-1.5 sm:left-[calc(6.5rem+1.75rem-12px)]" aria-hidden="true">
                        <x-icon name="sparkles" class="size-3.5" />
                    </span>
                    <span class="heading text-sm text-primary sm:text-right sm:text-2xl">Hari ini</span>
                    <div>
                        <h3 class="heading text-h3">{{ $company->tagline ?: 'Terus melangkah maju' }}</h3>
                        @php($stats = array_slice($company->stats(), 0, 3))
                        @if ($stats)
                            <dl class="mt-5 flex flex-wrap gap-x-8 gap-y-4">
                                @foreach ($stats as $stat)
                                    <div>
                                        <dd class="heading text-2xl" data-count>{{ $stat['value'] }}</dd>
                                        <dt class="text-xs text-muted">{{ $stat['label'] }}</dt>
                                    </div>
                                @endforeach
                            </dl>
                        @endif
                    </div>
                </li>
            </ol>
        </div>
    </div>

    @if ($company->history)
        <div class="{{ $ds->container() }} mt-20">
            <div class="grid gap-8 border-t border-line pt-12 lg:grid-cols-12 lg:gap-20">
                <h3 class="heading text-2xl lg:col-span-5" {!! $ds->reveal(0) !!}>Sejarah singkat</h3>
                <div class="site-prose text-muted lg:col-span-7" {!! $ds->reveal(1) !!}>{!! $company->history !!}</div>
            </div>
        </div>
    @endif
</section>
