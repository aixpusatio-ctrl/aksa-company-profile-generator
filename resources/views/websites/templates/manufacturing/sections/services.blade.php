{{-- Manufacturing services: numbered production process steps (01 → 04 ...). --}}
<section id="services" class="relative overflow-hidden bg-secondary py-20 text-on-secondary lg:py-28">
    <div class="absolute inset-0 bg-[linear-gradient(to_right,rgb(255_255_255/0.04)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.04)_1px,transparent_1px)] bg-[size:48px_48px]"></div>
    <div class="relative mx-auto max-w-7xl px-6">
        <div class="grid gap-6 lg:grid-cols-2 lg:items-end">
            <div>
                <p class="font-mono text-xs font-semibold tracking-widest text-on-secondary/60 uppercase">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }} — Alur Produksi</p>
                <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight uppercase md:text-4xl">{{ $section->title ?: 'Proses Kerja Terukur' }}</h2>
            </div>
            <p class="text-on-secondary/70 lg:text-right">{{ $section->subtitle ?: 'Setiap pesanan melewati tahapan yang terdokumentasi — dari spesifikasi hingga pengiriman.' }}</p>
        </div>

        @php($n = $company->services->count())
        <ol class="mt-14 grid border-t border-l border-white/10 sm:grid-cols-2 {{ $n % 3 === 0 && $n % 4 !== 0 ? 'lg:grid-cols-3' : 'lg:grid-cols-4' }}">
            @foreach ($company->services as $service)
                <li class="group relative border-r border-b border-white/10 p-7 transition hover:bg-white/[0.04]">
                    <div class="flex items-center justify-between">
                        <span class="font-mono text-5xl font-bold text-primary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="inline-flex size-11 items-center justify-center border border-white/15 text-on-secondary/80 transition group-hover:border-primary group-hover:bg-primary group-hover:text-on-primary">
                            <x-icon :name="$service->icon ?: 'briefcase'" class="size-5" />
                        </span>
                    </div>
                    <div class="mt-6 flex items-center gap-2">
                        <span class="h-0.5 w-6 bg-primary"></span>
                        @unless ($loop->last)<span class="h-px flex-1 bg-white/15"></span><x-icon name="chevron-right" class="size-4 text-on-secondary/40" />@endunless
                    </div>
                    <h3 class="mt-6 font-heading text-lg font-bold">{{ $service->title }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-on-secondary/65">{{ $service->description }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
