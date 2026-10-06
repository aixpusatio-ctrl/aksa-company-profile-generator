{{-- Construction services: dark numbered rows with images that reveal on hover. --}}
<section id="services" class="relative bg-stone-950 py-20 text-white lg:py-32">
    <div class="absolute top-0 right-0 h-full w-1/3 bg-[repeating-linear-gradient(90deg,rgb(255_255_255/0.025)_0_1px,transparent_1px_80px)]"></div>
    <div class="relative mx-auto max-w-7xl px-5 sm:px-6">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-end">
            <div>
                <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase"><span class="h-1 w-10 bg-primary"></span> Layanan</p>
                <h2 class="mt-5 font-heading text-4xl leading-[0.95] font-bold tracking-tight uppercase sm:text-5xl lg:text-6xl">{{ $section->title ?: 'Apa yang Kami Bangun' }}</h2>
            </div>
            <p class="max-w-lg text-stone-400 lg:justify-self-end">{{ $section->subtitle ?: 'Layanan konstruksi menyeluruh, dari perencanaan hingga serah terima, dikerjakan oleh tim bersertifikat.' }}</p>
        </div>

        <div class="mt-14 border-t border-white/15">
            @foreach ($company->services as $service)
                <article class="group relative grid items-center gap-6 border-b border-white/15 py-8 transition-colors hover:bg-white/[0.03] md:grid-cols-12 md:py-10">
                    <span class="absolute inset-y-0 left-0 w-1 origin-top scale-y-0 bg-primary transition-transform duration-300 group-hover:scale-y-100"></span>
                    <div class="font-heading text-5xl leading-none font-bold text-primary md:col-span-2 md:pl-6 lg:text-7xl">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="md:col-span-6">
                        <div class="flex items-center gap-3">
                            <x-icon :name="$service->icon ?: 'briefcase'" class="size-6 text-stone-500 transition group-hover:text-primary" />
                            <h3 class="font-heading text-2xl font-bold tracking-wide uppercase sm:text-3xl">{{ $service->title }}</h3>
                        </div>
                        <p class="mt-3 max-w-xl leading-relaxed text-stone-400">{{ $service->description }}</p>
                    </div>
                    <div class="md:col-span-3">
                        <div class="overflow-hidden">
                            <x-site.img :src="$service->url('image')" :alt="$service->title" icon="wrench" class="aspect-[16/10] w-full object-cover grayscale transition duration-500 group-hover:scale-105 group-hover:grayscale-0" />
                        </div>
                    </div>
                    <div class="hidden justify-end md:col-span-1 md:flex">
                        <a href="{{ $site->anchor('contact') }}" class="inline-flex size-12 items-center justify-center border-2 border-white/20 text-white transition group-hover:border-primary group-hover:bg-primary group-hover:text-on-primary" aria-label="Konsultasi {{ $service->title }}"><x-icon name="arrow-up-right" class="size-5" /></a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
