{{-- Services: Minimal List — quiet two-column rows (title left, description right) separated by hairlines; no icons. --}}
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-28">
                @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Layanan', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
                <div class="mt-8" {!! $ds->reveal(1) !!}>
                    @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Mulai Percakapan', 'kind' => 'link'])
                </div>
            </div>
        </div>

        <dl class="border-t border-ink/80 lg:col-span-8">
            @foreach ($company->services as $service)
                <div class="group grid gap-3 border-b border-line py-7 sm:grid-cols-2 sm:gap-10 sm:py-9" {!! $ds->reveal($loop->index) !!}>
                    <dt class="heading text-xl transition-colors group-hover:text-primary sm:text-2xl">{{ $service->title }}</dt>
                    <dd class="text-sm leading-relaxed text-muted sm:text-[0.95rem]">{{ $service->description }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
