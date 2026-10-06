{{-- Services: Bento — first service as a large photo tile, the rest as mixed-size tiles that always tile a 4-column grid. --}}
@php
    $services = $company->services->values();
    $count = $services->count();
    // Column span (of 4) per tile; the first tile is the large feature.
    $spans = match (true) {
        $count === 1 => [4],
        $count === 2 => [2, 2],
        $count === 3 => [2, 2, 2],
        default => [2, 1, 1, 2],
    };
    if ($count > 4) {
        foreach (array_chunk(range(4, $count - 1), 3) as $row) {
            array_push($spans, ...match (count($row)) { 3 => [1, 1, 2], 2 => [2, 2], default => [4] });
        }
    }
    $spanClass = [1 => 'lg:col-span-1', 2 => 'sm:col-span-2 lg:col-span-2', 4 => 'sm:col-span-2 lg:col-span-4'];
@endphp
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Solusi yang kami tawarkan', 'subtitle' => $section->subtitle ?: 'Layanan terintegrasi untuk mendukung pertumbuhan bisnis Anda.', 'align' => 'left', 'number' => $index])
            <div class="shrink-0" {!! $ds->reveal(1) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'secondary'])
            </div>
        </div>

        <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($services as $i => $service)
                @if ($i === 0)
                    <article class="group relative isolate flex min-h-[22rem] flex-col justify-end overflow-hidden rounded-brand p-7 text-white sm:col-span-2 sm:p-9 {{ $count >= 3 ? 'lg:row-span-2 lg:min-h-[30rem]' : '' }} {{ $count === 1 ? 'lg:col-span-4' : 'lg:col-span-2' }}" {!! $ds->reveal(0) !!}>
                        <x-site.img :src="$service->url('image')" :alt="$service->title" :icon="$service->icon ?: 'briefcase'" class="absolute inset-0 -z-10 size-full object-cover transition duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/85 via-black/40 to-black/5"></div>
                        <span class="inline-flex size-12 items-center justify-center rounded-full bg-white/15 backdrop-blur">
                            <x-icon :name="$service->icon ?: 'briefcase'" class="size-6" />
                        </span>
                        <h3 class="heading mt-6 text-3xl sm:text-4xl">{{ $service->title }}</h3>
                        @if ($service->description)
                            <p class="mt-3 max-w-lg text-sm leading-relaxed text-white/80 sm:text-base">{{ $service->description }}</p>
                        @endif
                    </article>
                @else
                    @php($wide = $spans[$i] >= 2)
                    <article class="{{ $ds->card('group flex p-7 '.$spanClass[$spans[$i]].($wide ? ' flex-col gap-6 sm:flex-row sm:items-start' : ' flex-col')) }}" {!! $ds->reveal($i) !!}>
                        <div class="flex flex-1 flex-col">
                            <div class="flex items-center justify-between gap-4">
                                <span class="inline-flex size-11 items-center justify-center rounded-brand bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-on-primary">
                                    <x-icon :name="$service->icon ?: 'briefcase'" class="size-5" />
                                </span>
                                <span class="font-mono text-xs text-muted">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <h3 class="heading mt-6 text-h3">{{ $service->title }}</h3>
                            @if ($service->description)
                                <p class="mt-3 text-sm leading-relaxed text-muted">{{ $service->description }}</p>
                            @endif
                        </div>
                        @if ($wide && $service->url('image'))
                            <x-site.img :src="$service->url('image')" :alt="$service->title" class="{{ $ds->img('hidden aspect-[4/3] w-full object-cover sm:block sm:w-2/5') }}" />
                        @endif
                    </article>
                @endif
            @endforeach
        </div>
    </div>
</section>
