{{-- Services: Icon Grid — compact centered grid, icons in tinted circles, no card borders. --}}
@php($count = $company->services->count())
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Layanan kami', 'subtitle' => $section->subtitle ?: 'Solusi menyeluruh yang dirancang untuk kebutuhan Anda.', 'align' => 'center', 'number' => $index])

        <div class="mx-auto mt-16 grid max-w-6xl grid-cols-1 gap-x-10 gap-y-14 sm:grid-cols-2 {{ $count % 4 === 0 || $count > 9 ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">
            @foreach ($company->services as $service)
                <div class="group flex flex-col items-center px-2 text-center" {!! $ds->reveal($loop->index) !!}>
                    <span class="relative inline-flex size-[4.5rem] items-center justify-center rounded-full bg-primary/10 text-primary ring-8 ring-primary/5 transition duration-300 group-hover:-translate-y-1 group-hover:bg-primary group-hover:text-on-primary">
                        <x-icon :name="$service->icon ?: 'briefcase'" class="size-7" />
                    </span>
                    <h3 class="heading mt-7 text-h3">{{ $service->title }}</h3>
                    @if ($service->description)
                        <p class="mt-3 max-w-xs text-sm leading-relaxed text-muted">{{ $service->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-16 text-center" {!! $ds->reveal(1) !!}>
            @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Konsultasikan Kebutuhan Anda', 'kind' => 'primary'])
        </div>
    </div>
</section>
