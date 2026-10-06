{{-- Corporate services: centered heading, 3-column bordered cards with icon. --}}
<section id="services" class="bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold tracking-widest text-primary uppercase">Layanan</p>
            <h2 class="mt-3 font-heading text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $section->title ?: 'Solusi Lengkap untuk Bisnis Anda' }}</h2>
            <p class="mt-4 text-slate-600">{{ $section->subtitle ?: 'Kami menghadirkan layanan terintegrasi yang dirancang untuk mendukung pertumbuhan perusahaan Anda.' }}</p>
        </div>
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->services as $service)
                <article class="group relative overflow-hidden rounded-brand border border-slate-200 bg-white p-8 transition hover:-translate-y-1 hover:border-primary/30 hover:shadow-xl hover:shadow-slate-900/5">
                    <span class="inline-flex size-14 items-center justify-center rounded-brand bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-on-primary">
                        <x-icon :name="$service->icon ?: 'briefcase'" class="size-7" />
                    </span>
                    <h3 class="mt-6 font-heading text-xl font-bold text-slate-900">{{ $service->title }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $service->description }}</p>
                    <span class="absolute right-6 bottom-6 text-5xl font-extrabold text-slate-100 transition group-hover:text-primary/10">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                </article>
            @endforeach
        </div>
    </div>
</section>
