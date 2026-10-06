{{-- Services: Process Timeline — services as numbered sequential steps joined by a line (horizontal on desktop, vertical on mobile). --}}
@php
    $count = $company->services->count();
    $cols = $count <= 5 ? max($count, 1) : ($count % 3 === 0 && $count % 4 !== 0 ? 3 : 4);
    $gridClass = [
        1 => 'lg:grid-cols-1',
        2 => 'lg:grid-cols-2 lg:[&>li:nth-child(2n)>.step-line]:hidden',
        3 => 'lg:grid-cols-3 lg:[&>li:nth-child(3n)>.step-line]:hidden',
        4 => 'lg:grid-cols-4 lg:[&>li:nth-child(4n)>.step-line]:hidden',
        5 => 'lg:grid-cols-5 lg:[&>li:nth-child(5n)>.step-line]:hidden',
    ][$cols];
@endphp
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Layanan & Proses', 'title' => $section->title ?: 'Bagaimana kami bekerja', 'subtitle' => $section->subtitle ?: 'Langkah terstruktur dari kebutuhan awal hingga hasil yang terukur.', 'number' => $index])

        <ol class="mt-16 grid gap-x-8 gap-y-0 lg:gap-y-16 {{ $gridClass }} [&>li:last-child>.step-line]:hidden">
            @foreach ($company->services as $service)
                <li class="group relative pb-12 pl-20 lg:pb-0 lg:pl-0" {!! $ds->reveal($loop->index) !!}>
                    {{-- connector: vertical on mobile, horizontal on desktop --}}
                    <span class="step-line absolute top-14 bottom-0 left-7 w-px bg-line lg:top-7 lg:right-[-2rem] lg:bottom-auto lg:left-16 lg:h-px lg:w-auto" aria-hidden="true"></span>
                    <span class="absolute top-0 left-0 inline-flex size-14 items-center justify-center rounded-full border border-line bg-surface font-mono text-sm font-semibold text-primary transition group-hover:border-primary group-hover:bg-primary group-hover:text-on-primary lg:relative">
                        {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </span>
                    <div class="lg:mt-8 lg:pr-4">
                        <div class="flex items-center gap-2 text-primary">
                            <x-icon :name="$service->icon ?: 'briefcase'" class="size-5" />
                            <span class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Langkah {{ $loop->iteration }}</span>
                        </div>
                        <h3 class="heading mt-3 text-h3">{{ $service->title }}</h3>
                        @if ($service->description)
                            <p class="mt-3 text-sm leading-relaxed text-muted">{{ $service->description }}</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ol>
    </div>
</section>
