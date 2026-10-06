{{-- Consulting services: numbered editorial list (I, II, III) with hover reveal. --}}
@php($roman = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'])
<section id="services" class="py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Layanan</p>
                <h2 class="mt-6 font-heading text-4xl leading-tight text-stone-900 md:text-5xl">{{ $section->title ?: 'Bidang keahlian kami' }}</h2>
            </div>
            <p class="text-stone-500 lg:col-span-4 lg:col-start-9">{{ $section->subtitle ?: 'Pendampingan menyeluruh — dari diagnosis hingga implementasi — dirancang khusus untuk konteks organisasi Anda.' }}</p>
        </div>

        <ol class="mt-16 border-t border-stone-300">
            @foreach ($company->services as $service)
                <li x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false" class="group border-b border-stone-300">
                    <button type="button" @click="open = !open" class="flex w-full items-center gap-4 py-8 text-left md:gap-6 md:py-10">
                        <span class="w-10 shrink-0 font-heading text-lg text-stone-400 italic transition group-hover:text-primary md:w-20">{{ $roman[$loop->index] ?? $loop->iteration }}.</span>
                        <span class="flex-1 font-heading text-2xl text-stone-900 transition group-hover:translate-x-2 md:text-4xl">{{ $service->title }}</span>
                        <span class="shrink-0">
                            <span class="inline-flex size-10 items-center justify-center rounded-full border border-stone-300 text-stone-500 transition group-hover:border-primary group-hover:bg-primary group-hover:text-on-primary">
                                <x-icon name="arrow-right" class="size-4 -rotate-45 transition group-hover:rotate-0" />
                            </span>
                        </span>
                    </button>
                    <div x-cloak x-show="open" x-collapse>
                        <div class="grid grid-cols-12 gap-4 pb-10">
                            <div class="col-span-12 flex gap-5 md:col-span-6 md:col-start-2">
                                <x-icon :name="$service->icon ?: 'briefcase'" class="mt-1 size-5 shrink-0 text-primary" />
                                <p class="leading-relaxed text-stone-600">{{ $service->description }}</p>
                            </div>
                            @if ($service->url('image'))
                                <div class="col-span-12 hidden md:col-span-3 md:col-start-10 md:block">
                                    <x-site.img :src="$service->url('image')" :alt="$service->title" class="aspect-[4/3] w-full object-cover grayscale" />
                                </div>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
