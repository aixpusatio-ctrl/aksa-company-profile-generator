{{-- Minimal services: plain numbered list. --}}
<section id="services" class="mx-auto max-w-4xl px-6">
    <div class="grid gap-8 border-t border-neutral-200 py-16 md:grid-cols-4 md:py-24">
        <div>
            <p class="text-xs text-neutral-400 tabular-nums">({{ str_pad($index, 2, '0', STR_PAD_LEFT) }})</p>
            <h2 class="mt-1 text-sm font-medium text-neutral-950">Layanan</h2>
        </div>
        <div class="md:col-span-3">
            <p class="font-heading text-2xl leading-snug font-medium tracking-tight text-neutral-950 md:text-3xl">{{ $section->title ?: 'Apa yang kami kerjakan.' }}</p>
            @if ($section->subtitle)<p class="mt-4 text-neutral-500">{{ $section->subtitle }}</p>@endif
            <ol class="mt-10 border-b border-neutral-200">
                @foreach ($company->services as $service)
                    <li class="group grid grid-cols-[2.5rem_1fr] gap-x-4 border-t border-neutral-200 py-6 sm:grid-cols-[3rem_1fr]">
                        <span class="pt-1 text-sm text-neutral-400 tabular-nums">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>
                            <h3 class="font-heading text-xl font-medium tracking-tight text-neutral-950 transition group-hover:translate-x-1">{{ $service->title }}</h3>
                            <p class="mt-2 max-w-xl text-[15px] leading-relaxed text-neutral-500">{{ $service->description }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>
