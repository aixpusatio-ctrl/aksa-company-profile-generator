{{-- Modern Business services: soft-shadow icon cards, 3-column grid with hover lift. --}}
<section id="services" class="relative isolate overflow-hidden py-20 lg:py-32">
    <div class="absolute top-20 left-1/2 -z-10 h-72 w-[40rem] -translate-x-1/2 rounded-full bg-primary/10 blur-3xl"></div>
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold text-primary">
                <x-icon name="sparkles" class="size-3.5" /> Layanan Kami
            </span>
            <h2 class="mt-5 font-heading text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Semua yang Bisnis Anda Butuhkan' }}</h2>
            <p class="mt-5 text-base text-slate-500 sm:text-lg">{{ $section->subtitle ?: 'Solusi menyeluruh yang dirancang untuk mempercepat pertumbuhan dan menyederhanakan operasional Anda.' }}</p>
        </div>

        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->services as $service)
                <article class="group relative flex flex-col rounded-brand bg-white p-8 shadow-[0_8px_30px_rgb(15_23_42/0.06)] ring-1 ring-slate-100 transition duration-300 hover:-translate-y-2 hover:shadow-[0_24px_60px_-12px_color-mix(in_oklab,var(--brand-primary)_35%,transparent)]">
                    <div class="flex items-start justify-between">
                        <span class="inline-flex size-14 items-center justify-center rounded-2xl bg-linear-to-br from-primary to-secondary text-on-primary shadow-lg shadow-primary/30 transition duration-300 group-hover:scale-110 group-hover:-rotate-6">
                            <x-icon :name="$service->icon ?: 'briefcase'" class="size-7" />
                        </span>
                        <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-400">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <h3 class="mt-7 font-heading text-xl font-semibold text-slate-900">{{ $service->title }}</h3>
                    <p class="mt-3 flex-1 text-sm leading-relaxed text-slate-500">{{ $service->description }}</p>
                    <a href="{{ $site->anchor('contact') }}" class="mt-7 inline-flex items-center gap-2 text-sm font-semibold text-primary">
                        Pelajari lebih lanjut
                        <span class="inline-flex size-7 items-center justify-center rounded-full bg-primary/10 transition group-hover:translate-x-1 group-hover:bg-primary group-hover:text-on-primary"><x-icon name="arrow-right" class="size-3.5" /></span>
                    </a>
                </article>
            @endforeach
        </div>
    </div>
</section>
