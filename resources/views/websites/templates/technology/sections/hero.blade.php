{{-- Technology hero: grid background with glow, big headline, terminal card, mono stats strip. --}}
@php
    $slug = \Illuminate\Support\Str::of($company->name)->replace(['PT. ', 'CV. ', 'PT ', 'CV '], '')->slug()->toString();
    $years = $company->established_year ? max(date('Y') - $company->established_year, 1) : null;
@endphp
<section id="hero" class="relative overflow-hidden pt-36 lg:pt-44">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,rgb(255_255_255/0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.05)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_70%_60%_at_50%_0%,black,transparent)]"></div>
    <div class="absolute -top-40 left-1/2 h-[30rem] w-[60rem] max-w-[140%] -translate-x-1/2 rounded-full bg-primary/20 blur-[120px]"></div>
    <div class="absolute top-40 -right-40 size-[28rem] rounded-full bg-secondary/20 blur-[120px]"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-5 sm:px-6 lg:grid-cols-12">
        <div class="lg:col-span-7">
            <a href="{{ $site->anchor('services') }}" class="group inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 py-1 pr-3 pl-1 font-mono text-xs text-slate-300 backdrop-blur transition hover:border-primary/40">
                <span class="rounded-full bg-primary/15 px-2.5 py-0.5 text-primary">{{ $years ? 'v'.$years.'.0' : 'new' }}</span>
                {{ $company->city ? 'Dibangun di '.$company->city : 'Teknologi untuk bisnis Anda' }}
                <x-icon name="arrow-right" class="size-3.5 transition group-hover:translate-x-0.5" />
            </a>
            <h1 class="mt-8 font-heading text-4xl leading-[1.05] font-bold tracking-tight text-white sm:text-5xl lg:text-[4.25rem]">
                <span class="bg-linear-to-br from-white via-white to-white/50 bg-clip-text text-transparent">{{ $section->title ?: ($company->tagline ?: $company->name) }}</span>
            </h1>
            <p class="mt-7 max-w-xl text-base leading-relaxed text-slate-400 sm:text-lg">{{ $section->subtitle ?: $company->description }}</p>
            <div class="mt-10 flex flex-wrap gap-3">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-primary px-6 py-3.5 text-sm font-semibold text-on-primary shadow-[0_0_40px_-8px_var(--brand-primary)] transition hover:shadow-[0_0_50px_-4px_var(--brand-primary)]">
                    Mulai Proyek <x-icon name="arrow-right" class="size-4" />
                </a>
                <a href="{{ $site->anchor('projects') }}" class="inline-flex items-center gap-2 rounded-btn border border-white/15 bg-white/5 px-6 py-3.5 font-mono text-sm text-slate-200 backdrop-blur transition hover:border-white/30 hover:bg-white/10">
                    <span class="text-primary">$</span> lihat --portofolio
                </a>
            </div>
        </div>

        {{-- Terminal card --}}
        <div class="relative lg:col-span-5">
            <div class="absolute -inset-px rounded-brand bg-linear-to-br from-primary/60 via-white/5 to-secondary/60 blur-sm"></div>
            <div class="relative overflow-hidden rounded-brand border border-white/10 bg-slate-900/90 shadow-2xl shadow-black/60 backdrop-blur">
                <div class="flex items-center gap-2 border-b border-white/10 bg-white/[0.03] px-4 py-3">
                    <span class="size-3 rounded-full bg-red-400/80"></span>
                    <span class="size-3 rounded-full bg-amber-400/80"></span>
                    <span class="size-3 rounded-full bg-emerald-400/80"></span>
                    <span class="ml-3 truncate font-mono text-xs text-slate-500">~/{{ $slug }} — zsh</span>
                </div>
                <div class="space-y-3 p-5 font-mono text-[13px] leading-relaxed sm:p-6">
                    <p><span class="text-primary">➜</span> <span class="text-secondary">~</span> <span class="text-slate-200">whoami</span></p>
                    <p class="text-slate-300">{{ $company->name }}</p>
                    <p><span class="text-primary">➜</span> <span class="text-secondary">~</span> <span class="text-slate-200">cat services.json</span></p>
                    <div class="text-slate-400">
                        <p>[</p>
                        @foreach ($company->services->take(4) as $service)
                            <p class="pl-4"><span class="text-emerald-300">"{{ $service->title }}"</span>{{ $loop->last ? '' : ',' }}</p>
                        @endforeach
                        <p>]</p>
                    </div>
                    <p><span class="text-primary">➜</span> <span class="text-secondary">~</span> <span class="text-slate-200">status --all</span></p>
                    <p class="flex items-center gap-2 text-emerald-300"><span class="size-2 rounded-full bg-emerald-400 shadow-[0_0_10px_2px_rgb(52_211_153/0.6)]"></span> online{{ $company->city ? ' · '.$company->city : '' }}</p>
                    <p><span class="text-primary">➜</span> <span class="text-secondary">~</span> <span class="inline-block h-4 w-2 translate-y-0.5 animate-pulse bg-primary"></span></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Mono stats strip --}}
    <div class="relative mx-auto mt-20 max-w-7xl px-5 sm:px-6 lg:mt-28">
        <dl class="grid grid-cols-2 divide-white/10 overflow-hidden rounded-brand border border-white/10 bg-white/[0.02] backdrop-blur md:grid-cols-4 md:divide-x">
            @foreach ([
                ['uptime', $years ? $years.'+ th' : '—', 'Pengalaman'],
                ['services', $company->services->count(), 'Layanan'],
                ['projects', $company->projects->count(), 'Proyek'],
                ['products', $company->products->count(), 'Produk'],
            ] as [$key, $value, $label])
                <div class="border-white/10 p-6 max-md:odd:border-r max-md:[&:nth-child(-n+2)]:border-b">
                    <dt class="font-mono text-xs text-slate-500"><span class="text-primary">.</span>{{ $key }}</dt>
                    <dd class="mt-2 font-heading text-3xl font-bold text-white sm:text-4xl">{{ $value }}</dd>
                    <dd class="mt-1 text-xs text-slate-500">{{ $label }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
