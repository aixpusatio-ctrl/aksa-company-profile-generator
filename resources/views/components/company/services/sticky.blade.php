{{-- Services: Sticky Scroll — sticky left column (heading, intro, CTA) while large numbered service blocks scroll by on the right. --}}
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-5">
            <div class="lg:sticky lg:top-28">
                @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Kapabilitas inti kami', 'subtitle' => $section->subtitle ?: 'Setiap layanan dirancang untuk memberi dampak nyata dan terukur bagi bisnis Anda.', 'align' => 'left', 'number' => $index])
                @if ($company->services->count() > 1)
                    <ul class="mt-10 hidden space-y-2 lg:block" {!! $ds->reveal(1) !!}>
                        @foreach ($company->services as $service)
                            <li class="flex items-center gap-3 text-sm text-muted">
                                <span class="font-mono text-xs text-primary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="h-px w-5 bg-line"></span>
                                <span class="truncate">{{ $service->title }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <div class="mt-10" {!! $ds->reveal(2) !!}>
                    @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Jadwalkan Diskusi', 'kind' => 'primary'])
                </div>
            </div>
        </div>

        <div class="space-y-5 lg:col-span-7">
            @foreach ($company->services as $service)
                <article class="{{ $ds->card('group relative overflow-hidden p-7 sm:p-10') }}" {!! $ds->reveal(0) !!}>
                    <span class="pointer-events-none absolute -top-4 right-4 heading text-[7rem] leading-none text-ink/[0.05] select-none sm:text-[9rem]" aria-hidden="true">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="relative">
                        <span class="inline-flex size-14 items-center justify-center rounded-brand bg-primary text-on-primary shadow-lg shadow-primary/25">
                            <x-icon :name="$service->icon ?: 'briefcase'" class="size-7" />
                        </span>
                        <p class="mt-8 font-mono text-xs tracking-wide text-primary">Layanan {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                        <h3 class="heading mt-2 text-[clamp(1.6rem,3vw,2.25rem)]">{{ $service->title }}</h3>
                        @if ($service->description)
                            <p class="mt-4 max-w-xl leading-relaxed text-muted">{{ $service->description }}</p>
                        @endif
                        @if ($service->url('image'))
                            <x-site.img :src="$service->url('image')" :alt="$service->title" class="{{ $ds->get('image') === 'arch' ? 'mt-8 aspect-[16/9] w-full rounded-brand object-cover' : $ds->img('mt-8 aspect-[16/9] w-full object-cover') }}" />
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
