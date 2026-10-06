{{-- Hero: Dashboard — centered headline above a tilted HTML "app window" (sidebar, KPI cards, chart, service rows). --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 3);
    $services = $company->services->take(4);
    $bars = [38, 52, 46, 64, 58, 72, 66, 80, 74, 88, 82, 94];
    $nav = [['dashboard', 'Ringkasan'], ['squares', 'Layanan'], ['briefcase', 'Proyek'], ['users', 'Tim'], ['chart', 'Laporan']];
    $a = $ds->isDark() ? 30 : 16;
@endphp
<section id="hero" class="relative isolate overflow-hidden bg-surface">
    <div class="pointer-events-none absolute inset-0 -z-10" aria-hidden="true"
        style="background: radial-gradient(60rem 36rem at 50% -10%, color-mix(in oklab, var(--brand-primary) {{ $a }}%, transparent), transparent 70%), radial-gradient(40rem 30rem at 85% 60%, color-mix(in oklab, var(--brand-secondary) {{ round($a * .7) }}%, transparent), transparent 70%)"></div>

    <div class="{{ $ds->container() }} pt-32 text-center lg:pt-40">
        <div {!! $ds->reveal(0) !!}>{!! $ds->eyebrow($company->established_year ? 'Dipercaya sejak '.$company->established_year : $company->name) !!}</div>
        <h1 class="heading mx-auto mt-6 max-w-4xl text-[min(var(--display),4.75rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
        @if ($lead)
            <p class="mx-auto mt-6 max-w-2xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
        @endif
        <div class="mt-10 flex flex-col justify-center gap-3 sm:flex-row" {!! $ds->reveal(3) !!}>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Jadwalkan Demo', 'kind' => 'primary'])
            @include('components.company.partials.button', ['href' => $site->anchor('services'), 'label' => 'Lihat Solusi', 'kind' => 'secondary'])
        </div>
    </div>

    <div class="{{ $ds->container('wide') }} relative mt-16 pb-16 lg:mt-20 lg:pb-24 [perspective:2400px]" {!! $ds->reveal(4) !!}>
        <div class="mx-auto max-w-6xl [transform:rotateX(10deg)] [transform-origin:50%_0%] transition duration-700 hover:[transform:rotateX(0deg)]" aria-hidden="true">
            <div class="rounded-[calc(var(--brand-radius)*1.6)] border border-line bg-card/70 p-2 shadow-[0_40px_120px_-40px_rgba(0,0,0,.45)] backdrop-blur-xl">
                <div class="overflow-hidden rounded-[calc(var(--brand-radius)*1.2)] border border-line bg-surface text-left">
                    {{-- Window chrome --}}
                    <div class="flex items-center gap-4 border-b border-line bg-surface-alt px-4 py-3">
                        <div class="flex gap-1.5"><span class="size-2.5 rounded-full bg-ink/15"></span><span class="size-2.5 rounded-full bg-ink/15"></span><span class="size-2.5 rounded-full bg-ink/15"></span></div>
                        <div class="mx-auto flex max-w-xs flex-1 items-center justify-center gap-2 rounded-md border border-line bg-surface px-3 py-1 text-[11px] text-muted">
                            <x-icon name="lock" class="size-3" /><span class="truncate">{{ $company->name }}</span>
                        </div>
                        <span class="hidden w-12 sm:block"></span>
                    </div>
                    <div class="flex">
                        {{-- Sidebar --}}
                        <div class="hidden w-52 shrink-0 border-r border-line bg-surface-alt/60 p-4 md:block">
                            <div class="flex items-center gap-2 px-2 pb-4">
                                <span class="flex size-7 items-center justify-center rounded-md bg-primary text-xs font-bold text-on-primary">{{ mb_strtoupper(mb_substr($company->name, 0, 1)) }}</span>
                                <span class="truncate text-sm font-semibold text-ink">{{ $company->name }}</span>
                            </div>
                            <ul class="space-y-1 text-[13px]">
                                @foreach ($nav as [$icon, $label])
                                    <li class="flex items-center gap-2.5 rounded-md px-2 py-2 {{ $loop->first ? 'bg-primary/10 font-semibold text-primary' : 'text-muted' }}">
                                        <x-icon :name="$icon" class="size-4" />{{ $label }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        {{-- Main --}}
                        <div class="min-w-0 flex-1 space-y-4 p-4 sm:p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[11px] tracking-wide text-muted uppercase">Ringkasan</p>
                                    <p class="text-base font-semibold text-ink">Kinerja {{ $company->name }}</p>
                                </div>
                                <span class="rounded-full bg-primary px-3 py-1 text-[11px] font-semibold text-on-primary">{{ date('Y') }}</span>
                            </div>
                            @if ($stats)
                                <div class="grid grid-cols-2 gap-3 {{ count($stats) >= 3 ? 'sm:grid-cols-3' : '' }}">
                                    @foreach ($stats as $stat)
                                        <div class="rounded-lg border border-line bg-card p-3 sm:p-4 {{ $loop->iteration === 3 ? 'max-sm:hidden' : '' }}">
                                            <p class="truncate text-[11px] text-muted">{{ $stat['label'] }}</p>
                                            <p class="mt-1 text-xl font-bold text-ink sm:text-2xl">{{ $stat['value'] }}</p>
                                            <div class="mt-2 h-1 overflow-hidden rounded-full bg-line"><div class="h-full rounded-full bg-primary" style="width: {{ [72, 58, 86][$loop->index] }}%"></div></div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="grid gap-4 lg:grid-cols-5">
                                <div class="rounded-lg border border-line bg-card p-4 lg:col-span-3">
                                    <p class="text-xs font-semibold text-ink">Pertumbuhan</p>
                                    <div class="mt-4 flex h-28 items-end gap-1.5 sm:h-36 sm:gap-2">
                                        @foreach ($bars as $h)
                                            <div class="flex-1 rounded-t-sm {{ $loop->last ? 'bg-primary' : 'bg-primary/25' }}" style="height: {{ $h }}%"></div>
                                        @endforeach
                                    </div>
                                </div>
                                @if ($services->isNotEmpty())
                                    <div class="rounded-lg border border-line bg-card p-4 max-sm:hidden lg:col-span-2">
                                        <p class="text-xs font-semibold text-ink">Layanan</p>
                                        <ul class="mt-3 divide-y divide-line">
                                            @foreach ($services as $service)
                                                <li class="flex items-center gap-2.5 py-2">
                                                    <span class="flex size-7 shrink-0 items-center justify-center rounded-md bg-secondary/15 text-secondary"><x-icon :name="$service->icon ?: 'check'" class="size-3.5" /></span>
                                                    <span class="truncate text-xs font-medium text-ink">{{ $service->title }}</span>
                                                    <span class="ml-auto shrink-0 rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-semibold text-primary">Tersedia</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-surface to-transparent"></div>
    </div>
</section>
