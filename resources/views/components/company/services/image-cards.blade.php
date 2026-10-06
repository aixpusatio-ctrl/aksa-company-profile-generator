{{-- Services: Image Cards — photo-topped cards with gradient title overlay and description below. --}}
@php($count = $company->services->count())
<section id="services" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Layanan', 'title' => $section->title ?: 'Layanan unggulan kami', 'subtitle' => $section->subtitle ?: 'Didukung tim berpengalaman dan standar kerja yang teruji.', 'number' => $index])

        <div class="mt-14 grid gap-6 sm:grid-cols-2 {{ $count === 4 || $count >= 7 && $count % 4 === 0 ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }}">
            @foreach ($company->services as $service)
                <article class="{{ $ds->card('group flex flex-col overflow-hidden') }}" {!! $ds->reveal($loop->index) !!}>
                    <div class="relative isolate aspect-[4/3] overflow-hidden">
                        <x-site.img :src="$service->url('image')" :alt="$service->title" :icon="$service->icon ?: 'briefcase'" class="absolute inset-0 -z-10 size-full object-cover transition duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-4 p-5 sm:p-6">
                            <h3 class="heading text-xl text-white sm:text-2xl">{{ $service->title }}</h3>
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-white backdrop-blur transition group-hover:bg-primary group-hover:text-on-primary">
                                <x-icon :name="$service->icon ?: 'briefcase'" class="size-5" />
                            </span>
                        </div>
                    </div>
                    @if ($service->description)
                        <p class="flex-1 p-5 text-sm leading-relaxed text-muted sm:p-6">{{ $service->description }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
