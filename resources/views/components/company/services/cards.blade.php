{{-- Services: Cards — 3-column cards with icon, title, description. --}}
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Solusi yang Kami Tawarkan', 'subtitle' => $section->subtitle ?: 'Layanan terintegrasi untuk mendukung pertumbuhan bisnis Anda.', 'number' => $index])
        <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($company->services as $service)
                <article class="{{ $ds->card('group p-7 sm:p-8') }}" {!! $ds->reveal($loop->index) !!}>
                    <span class="inline-flex size-12 items-center justify-center rounded-brand bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-on-primary">
                        <x-icon :name="$service->icon ?: 'briefcase'" class="size-6" />
                    </span>
                    <h3 class="heading mt-6 text-h3">{{ $service->title }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-muted">{{ $service->description }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
