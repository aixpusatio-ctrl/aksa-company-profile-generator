{{-- Modern Business hero: full-bleed primary→secondary gradient, headline + pill CTAs, image with floating mockup cards, stats & client strip. --}}
@php
    $years = $company->established_year ? max(date('Y') - $company->established_year, 1) : null;
    $rating = $company->testimonials->count() ? number_format($company->testimonials->avg('rating'), 1) : null;
    $clients = $company->projects->pluck('client')->merge($company->testimonials->pluck('company'))->filter()->unique()->take(6);
@endphp
<section id="hero" class="relative overflow-hidden bg-linear-to-br from-primary via-primary to-secondary text-on-primary">
    {{-- decorative layers --}}
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgb(255_255_255/0.16)_1px,transparent_0)] bg-[size:30px_30px] [mask-image:radial-gradient(ellipse_at_top_right,black,transparent_70%)]"></div>
    <div class="absolute -top-32 -left-32 size-[30rem] rounded-full bg-white/10 blur-3xl"></div>
    <div class="absolute right-0 bottom-0 size-[36rem] translate-x-1/3 translate-y-1/3 rounded-full bg-secondary/60 blur-3xl"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-16 px-5 pt-32 pb-20 sm:px-6 lg:grid-cols-12 lg:pt-40 lg:pb-28">
        <div class="lg:col-span-6">
            <span class="inline-flex items-center gap-2 rounded-full bg-white/15 py-1 pr-4 pl-1 text-xs font-medium ring-1 ring-white/25 backdrop-blur">
                <span class="rounded-full bg-white px-2.5 py-0.5 font-semibold text-slate-900">{{ $years ? $years.'+ tahun' : 'Baru' }}</span>
                {{ $company->city ? 'Berbasis di '.$company->city : 'Mitra bisnis terpercaya' }}
            </span>
            <h1 class="mt-7 font-heading text-4xl leading-[1.08] font-semibold tracking-tight sm:text-5xl lg:text-6xl">
                {{ $section->title ?: ($company->tagline ?: $company->name) }}
            </h1>
            <p class="mt-6 max-w-xl text-base leading-relaxed text-on-primary/80 sm:text-lg">
                {{ $section->subtitle ?: $company->description }}
            </p>
            <div class="mt-9 flex flex-wrap items-center gap-3">
                <a href="{{ $site->anchor('contact') }}" class="group inline-flex items-center gap-3 rounded-btn bg-white py-2 pr-2 pl-6 text-sm font-semibold text-slate-900 shadow-xl shadow-black/15 transition hover:-translate-y-0.5">
                    Konsultasi Gratis
                    <span class="inline-flex size-9 items-center justify-center rounded-full bg-linear-to-br from-primary to-secondary text-on-primary transition group-hover:rotate-45"><x-icon name="arrow-up-right" class="size-4" /></span>
                </a>
                <a href="{{ $site->anchor('services') }}" class="inline-flex items-center gap-2 rounded-btn px-6 py-3.5 text-sm font-semibold ring-1 ring-white/40 transition hover:bg-white/10">
                    <x-icon name="play" class="size-4" /> Lihat Layanan
                </a>
            </div>

            <dl class="mt-12 grid max-w-lg grid-cols-3 divide-x divide-white/20">
                <div class="pr-4">
                    <dd class="font-heading text-3xl font-semibold sm:text-4xl">{{ $years ? $years.'+' : '—' }}</dd>
                    <dt class="mt-1 text-xs text-on-primary/70 sm:text-sm">Tahun pengalaman</dt>
                </div>
                <div class="px-4">
                    <dd class="font-heading text-3xl font-semibold sm:text-4xl">{{ $company->projects->count() ?: $company->services->count() }}</dd>
                    <dt class="mt-1 text-xs text-on-primary/70 sm:text-sm">{{ $company->projects->count() ? 'Proyek unggulan' : 'Layanan' }}</dt>
                </div>
                <div class="pl-4">
                    <dd class="font-heading text-3xl font-semibold sm:text-4xl">{{ $rating ?: '100%' }}</dd>
                    <dt class="mt-1 text-xs text-on-primary/70 sm:text-sm">{{ $rating ? 'Rating klien' : 'Komitmen' }}</dt>
                </div>
            </dl>
        </div>

        {{-- Visual: framed image + floating mockup cards --}}
        <div class="relative lg:col-span-6">
            <div class="relative mx-auto max-w-xl rotate-1 rounded-[calc(var(--brand-radius)+10px)] bg-white/15 p-2.5 ring-1 ring-white/25 backdrop-blur-sm transition duration-700 hover:rotate-0">
                <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="aspect-[4/3.4] w-full rounded-brand object-cover" />
            </div>

            {{-- growth card --}}
            <div class="absolute -top-6 -left-2 hidden w-56 rounded-2xl bg-white p-4 text-slate-900 shadow-2xl shadow-black/20 sm:block lg:-left-10">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-slate-500">Pertumbuhan bisnis</p>
                    <span class="inline-flex size-6 items-center justify-center rounded-full bg-emerald-50 text-emerald-600"><x-icon name="chart" class="size-3.5" /></span>
                </div>
                <div class="mt-4 flex h-16 items-end gap-1.5">
                    @foreach ([35, 50, 42, 64, 58, 78, 92] as $h)
                        <span class="flex-1 rounded-t-md {{ $loop->last ? 'bg-linear-to-t from-primary to-secondary' : 'bg-primary/15' }}" style="height: {{ $h }}%"></span>
                    @endforeach
                </div>
            </div>

            {{-- testimonial / rating card --}}
            <div class="absolute -bottom-8 left-4 w-64 rounded-2xl bg-white p-4 text-slate-900 shadow-2xl shadow-black/20 sm:left-10">
                <div class="flex -space-x-2">
                    @foreach ($company->testimonials->take(4) as $t)
                        <x-site.img :src="$t->url('photo')" :alt="$t->customer_name" icon="user" class="size-9 rounded-full object-cover ring-2 ring-white" />
                    @endforeach
                    @if ($company->testimonials->isEmpty())
                        <span class="inline-flex size-9 items-center justify-center rounded-full bg-primary/10 text-primary ring-2 ring-white"><x-icon name="users" class="size-4" /></span>
                    @endif
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <x-site.stars :rating="$rating ? (int) round($rating) : 5" />
                    <span class="text-xs font-semibold">{{ $rating ?: '5.0' }}/5</span>
                </div>
                <p class="mt-1 text-xs text-slate-500">Dipercaya klien di berbagai industri</p>
            </div>

            {{-- small pill card --}}
            <div class="absolute top-1/2 -right-2 hidden items-center gap-3 rounded-full bg-white py-2 pr-5 pl-2 text-slate-900 shadow-2xl shadow-black/20 md:flex lg:-right-6">
                <span class="inline-flex size-10 items-center justify-center rounded-full bg-linear-to-br from-primary to-secondary text-on-primary"><x-icon name="shield" class="size-5" /></span>
                <div>
                    <p class="text-sm font-semibold leading-tight">Terpercaya</p>
                    <p class="text-[11px] text-slate-500">{{ $company->services->count() }} layanan profesional</p>
                </div>
            </div>
        </div>
    </div>

    @if ($clients->isNotEmpty())
        <div class="relative border-t border-white/15 bg-black/5 backdrop-blur-sm">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-6 sm:px-6 lg:flex-row lg:items-center lg:gap-10">
                <p class="shrink-0 text-xs font-semibold tracking-widest text-on-primary/60 uppercase">Dipercaya oleh</p>
                <div class="flex flex-wrap items-center gap-x-8 gap-y-3">
                    @foreach ($clients as $client)
                        <span class="font-heading text-sm font-semibold text-on-primary/75 sm:text-base">{{ $client }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</section>
