{{-- Modern Business about: alternating left/right rows (story / vision & values) + animated stats counters. --}}
@php
    $years = $company->established_year ? max(date('Y') - $company->established_year, 1) : 0;
    $stats = array_values(array_filter([
        $years ? ['value' => $years, 'suffix' => '+', 'label' => 'Tahun Pengalaman', 'icon' => 'calendar'] : null,
        $company->projects->count() ? ['value' => $company->projects->count(), 'suffix' => '', 'label' => 'Proyek Unggulan', 'icon' => 'rocket'] : null,
        $company->services->count() ? ['value' => $company->services->count(), 'suffix' => '', 'label' => 'Layanan Profesional', 'icon' => 'briefcase'] : null,
        $company->team->count() ? ['value' => $company->team->count(), 'suffix' => '', 'label' => 'Pemimpin Berpengalaman', 'icon' => 'users'] : null,
    ]));
    $images = $company->gallery;
    $statCols = [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4'][count($stats)] ?? 'lg:grid-cols-4';
@endphp
<section id="about" class="bg-slate-50 py-20 lg:py-32">
    <div class="mx-auto max-w-7xl space-y-20 px-5 sm:px-6 lg:space-y-28">

        {{-- Row 1: image left, story right --}}
        <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
            <div class="relative">
                <x-site.img :src="$images->get(0)?->url('image') ?? $company->url('hero_image')" :alt="$company->name" class="aspect-[5/4] w-full rounded-brand object-cover shadow-2xl shadow-slate-900/10" />
                <div class="absolute -right-3 -bottom-8 flex items-center gap-4 rounded-2xl bg-white p-5 shadow-xl shadow-slate-900/10 sm:-right-8">
                    <span class="font-heading text-4xl font-semibold text-transparent bg-linear-to-br from-primary to-secondary bg-clip-text">{{ $company->established_year ?: '—' }}</span>
                    <span class="text-sm leading-tight text-slate-500">Tahun<br>berdiri</span>
                </div>
            </div>
            <div>
                <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold text-primary"><x-icon name="building" class="size-3.5" /> Tentang Kami</span>
                <h2 class="mt-5 font-heading text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Kami Membantu Bisnis Bertumbuh Lebih Cepat' }}</h2>
                @if ($section->subtitle)
                    <p class="mt-5 text-lg text-slate-500">{{ $section->subtitle }}</p>
                @endif
                <div class="site-prose mt-6 text-slate-600">{!! $company->about ?: e($company->description) !!}</div>
                @if ($company->missionItems())
                    <ul class="mt-8 grid gap-3 sm:grid-cols-2">
                        @foreach (array_slice($company->missionItems(), 0, 4) as $mission)
                            <li class="flex gap-3 rounded-2xl bg-white p-4 text-sm shadow-sm ring-1 ring-slate-100">
                                <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-primary to-secondary text-on-primary"><x-icon name="check" class="size-3.5" /></span>
                                <span>{{ $mission }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        {{-- Row 2: vision & values left, image right --}}
        @if ($company->vision || $company->valueItems() || $company->history)
            <div class="grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
                <div class="lg:order-2">
                    <div class="relative">
                        <x-site.img :src="$images->get(1)?->url('image')" :alt="$company->name" class="aspect-[5/4] w-full rounded-brand object-cover shadow-2xl shadow-slate-900/10" />
                        <div class="absolute -top-6 -left-3 max-w-[16rem] rounded-2xl bg-linear-to-br from-primary to-secondary p-5 text-on-primary shadow-xl shadow-primary/30 sm:-left-8">
                            <x-icon name="quote" class="size-6 opacity-70" />
                            <p class="mt-2 text-sm font-medium leading-snug">{{ \Illuminate\Support\Str::limit($company->tagline ?: $company->description, 90) }}</p>
                        </div>
                    </div>
                </div>
                <div class="lg:order-1">
                    @if ($company->vision)
                        <p class="text-sm font-semibold text-primary">Visi Kami</p>
                        <p class="mt-3 font-heading text-2xl leading-snug font-medium text-slate-900 sm:text-3xl">“{{ $company->vision }}”</p>
                    @endif
                    @if ($company->valueItems())
                        <div class="mt-10 space-y-3">
                            @foreach ($company->valueItems() as $value)
                                <div class="group flex items-center gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-100 transition hover:-translate-y-0.5 hover:shadow-lg">
                                    <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-on-primary"><x-icon name="sparkles" class="size-5" /></span>
                                    <span class="text-sm text-slate-600">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    @elseif ($company->history)
                        <div class="site-prose mt-8 text-slate-600">{!! $company->history !!}</div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Stats counters --}}
        @if (count($stats))
            <div class="grid gap-4 rounded-brand bg-white p-4 shadow-[0_8px_30px_rgb(15_23_42/0.06)] ring-1 ring-slate-100 sm:grid-cols-2 {{ $statCols }} lg:p-6">
                @foreach ($stats as $stat)
                    <div class="flex items-center gap-4 rounded-2xl p-4 transition hover:bg-slate-50"
                         x-data="{ n: {{ $stat['value'] }}, target: {{ $stat['value'] }} }"
                         x-init="new IntersectionObserver((entries, o) => { if (entries[0].isIntersecting) { o.disconnect(); n = 0; const step = Math.max(1, Math.ceil(target / 40)); const t = setInterval(() => { n = Math.min(target, n + step); if (n >= target) clearInterval(t) }, 30) } }, { threshold: .4 }).observe($el)">
                        <span class="inline-flex size-14 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-primary to-secondary text-on-primary"><x-icon :name="$stat['icon']" class="size-6" /></span>
                        <div>
                            <p class="font-heading text-3xl font-semibold text-slate-900 sm:text-4xl"><span x-text="n">{{ $stat['value'] }}</span>{{ $stat['suffix'] }}</p>
                            <p class="text-sm text-slate-500">{{ $stat['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
