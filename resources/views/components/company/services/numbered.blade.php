{{-- Services: Numbered List — editorial rows (01, 02 …) with title, description and arrow; hovered row highlights. --}}
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Apa yang kami kerjakan', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <div class="shrink-0" {!! $ds->reveal(1) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Diskusikan Proyek', 'kind' => 'secondary'])
            </div>
        </div>

        <ol class="mt-14 border-t border-line">
            @foreach ($company->services as $service)
                <li class="group relative border-b border-line" {!! $ds->reveal($loop->index) !!}>
                    <a href="{{ $site->anchor('contact') }}" class="relative grid grid-cols-[auto_1fr_auto] items-start gap-x-5 gap-y-3 py-7 sm:gap-x-8 sm:py-9 lg:grid-cols-12 lg:items-center">
                        <span class="pointer-events-none absolute inset-y-0 -inset-x-5 origin-left scale-x-0 bg-primary/5 transition-transform duration-500 ease-out group-hover:scale-x-100 sm:-inset-x-8"></span>
                        <span class="relative font-mono text-sm text-muted transition group-hover:text-primary lg:col-span-1">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="heading relative text-2xl transition group-hover:translate-x-2 sm:text-3xl lg:col-span-5 lg:text-4xl">{{ $service->title }}</h3>
                        <span class="relative col-start-3 row-start-1 inline-flex size-11 items-center justify-center rounded-full border border-line text-ink transition group-hover:border-primary group-hover:bg-primary group-hover:text-on-primary lg:order-last lg:col-span-1 lg:col-start-auto lg:row-start-auto lg:justify-self-end">
                            <x-icon name="arrow-up-right" class="size-4 transition group-hover:rotate-45" />
                        </span>
                        @if ($service->description)
                            <p class="relative col-span-3 text-sm leading-relaxed text-muted sm:col-span-2 sm:col-start-2 lg:col-span-5 lg:col-start-auto">{{ $service->description }}</p>
                        @endif
                    </a>
                </li>
            @endforeach
        </ol>
    </div>
</section>
