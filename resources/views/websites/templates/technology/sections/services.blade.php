{{-- Technology services: bento grid of glass tiles with glow on hover. --}}
@php
    // Repeating bento pattern on a 4-column grid (6 tiles fill 3 rows exactly).
    $spans = ['lg:col-span-2 lg:row-span-2', 'lg:col-span-2', '', '', 'lg:col-span-2', 'lg:col-span-2'];
@endphp
<section id="services" class="relative py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="grid gap-6 lg:grid-cols-2 lg:items-end">
            <div>
                <p class="font-mono text-sm text-primary">// layanan</p>
                <h2 class="mt-4 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Stack lengkap untuk skala bisnis Anda' }}</h2>
            </div>
            <p class="max-w-lg text-slate-400 lg:justify-self-end">{{ $section->subtitle ?: 'Dari strategi hingga operasional, kami merancang, membangun, dan menjalankan sistem yang andal.' }}</p>
        </div>

        <div class="mt-14 grid grid-flow-dense auto-rows-[minmax(14rem,auto)] gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->services as $service)
                @php($big = $loop->index % 6 === 0)
                <article class="group relative flex flex-col overflow-hidden rounded-brand border border-white/10 bg-white/[0.03] p-6 transition duration-300 hover:border-primary/40 hover:bg-white/[0.05] {{ $spans[$loop->index % 6] }} {{ $big ? 'sm:col-span-2' : '' }}">
                    <div class="pointer-events-none absolute -top-24 -right-24 size-56 rounded-full bg-primary/0 blur-3xl transition duration-500 group-hover:bg-primary/25"></div>
                    @if ($big && $service->url('image'))
                        <img src="{{ $service->url('image') }}" alt="" loading="lazy" class="absolute inset-0 size-full object-cover opacity-25 mix-blend-luminosity transition duration-700 [mask-image:linear-gradient(to_bottom,black,transparent_85%)] group-hover:scale-105 group-hover:opacity-35">
                    @endif
                    @if ($big)
                        <div class="absolute inset-0 bg-[radial-gradient(rgb(255_255_255/0.08)_1px,transparent_1px)] bg-[size:18px_18px] [mask-image:linear-gradient(to_top,black,transparent_70%)]"></div>
                    @endif
                    <div class="relative flex items-start justify-between">
                        <span class="inline-flex size-12 items-center justify-center rounded-xl border border-white/10 bg-slate-900 text-primary shadow-[0_0_30px_-10px_var(--brand-primary)] transition group-hover:shadow-[0_0_30px_-4px_var(--brand-primary)]">
                            <x-icon :name="$service->icon ?: 'briefcase'" class="size-6" />
                        </span>
                        <span class="font-mono text-xs text-slate-600">0x{{ str_pad(dechex($loop->iteration), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="relative mt-auto pt-10">
                        <h3 class="font-heading font-semibold text-white {{ $big ? 'text-2xl sm:text-3xl' : 'text-lg' }}">{{ $service->title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-400 {{ $big ? 'max-w-md sm:text-base' : '' }}">{{ $service->description }}</p>
                        @if ($big)
                            <a href="{{ $site->anchor('contact') }}" class="mt-6 inline-flex items-center gap-2 font-mono text-sm text-primary">diskusikan <x-icon name="arrow-right" class="size-4 transition group-hover:translate-x-1" /></a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
