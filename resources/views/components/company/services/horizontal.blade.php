{{-- Services: Horizontal Rail — scroll-snap rail of tall image cards with number & title; heading row with prev/next arrows. --}}
<section id="services" class="{{ $ds->section($tone, 'overflow-hidden') }}"
    x-data="{
        atStart: true, atEnd: false,
        update() { const r = this.$refs.rail; this.atStart = r.scrollLeft < 8; this.atEnd = r.scrollLeft + r.clientWidth >= r.scrollWidth - 8; },
        go(dir) { const r = this.$refs.rail; const card = r.querySelector('li'); r.scrollBy({ left: dir * ((card ? card.offsetWidth : 320) + 20), behavior: 'smooth' }); }
    }" x-init="$nextTick(() => update())">
    <div class="{{ $ds->container() }}">
        <div class="flex items-end justify-between gap-8">
            @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Keahlian kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <div class="hidden shrink-0 gap-2 sm:flex" {!! $ds->reveal(1) !!}>
                <button type="button" @click="go(-1)" :disabled="atStart" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary disabled:pointer-events-none disabled:opacity-35" aria-label="Layanan sebelumnya">
                    <x-icon name="arrow-left" class="size-5" />
                </button>
                <button type="button" @click="go(1)" :disabled="atEnd" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary disabled:pointer-events-none disabled:opacity-35" aria-label="Layanan berikutnya">
                    <x-icon name="arrow-right" class="size-5" />
                </button>
            </div>
        </div>
    </div>

    <div class="{{ $ds->container() }} mt-12" {!! $ds->reveal(2) !!}>
        <ul x-ref="rail" @scroll.debounce.50ms="update()" class="mr-[calc(50%-50vw)] -ml-5 flex snap-x snap-mandatory scroll-px-5 gap-5 overflow-x-auto px-5 pb-6 [scrollbar-width:none] sm:-ml-8 sm:scroll-px-8 sm:px-8 [&::-webkit-scrollbar]:hidden" tabindex="0" aria-label="Daftar layanan">
            @foreach ($company->services as $service)
                <li class="group relative w-[78%] shrink-0 snap-start sm:w-[22rem] lg:w-[24rem]">
                    <article class="relative isolate flex aspect-[3/4] flex-col justify-between overflow-hidden rounded-brand p-6 text-white sm:p-7">
                        <x-site.img :src="$service->url('image')" :alt="$service->title" :icon="$service->icon ?: 'briefcase'" class="absolute inset-0 -z-10 size-full object-cover transition duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/90 via-black/35 to-black/10"></div>
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-sm text-white/80">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }} / {{ str_pad($loop->count, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="inline-flex size-10 items-center justify-center rounded-full bg-white/15 backdrop-blur">
                                <x-icon :name="$service->icon ?: 'briefcase'" class="size-5" />
                            </span>
                        </div>
                        <div>
                            <h3 class="heading text-2xl sm:text-3xl">{{ $service->title }}</h3>
                            @if ($service->description)
                                <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-white/75">{{ $service->description }}</p>
                            @endif
                        </div>
                    </article>
                </li>
            @endforeach
        </ul>

        <div class="mt-4 flex items-center justify-between gap-4">
            <div class="flex gap-2 sm:hidden">
                <button type="button" @click="go(-1)" :disabled="atStart" class="inline-flex size-11 items-center justify-center rounded-full border border-line text-ink disabled:opacity-35" aria-label="Layanan sebelumnya"><x-icon name="arrow-left" class="size-5" /></button>
                <button type="button" @click="go(1)" :disabled="atEnd" class="inline-flex size-11 items-center justify-center rounded-full border border-line text-ink disabled:opacity-35" aria-label="Layanan berikutnya"><x-icon name="arrow-right" class="size-5" /></button>
            </div>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Diskusikan Kebutuhan Anda', 'kind' => 'link', 'class' => 'ml-auto'])
        </div>
    </div>
</section>
