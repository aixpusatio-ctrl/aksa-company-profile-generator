{{-- Testimonials: Marquee — two rows of quote cards scrolling in opposite directions, paused on hover. --}}
@php
    $items = $company->testimonials->values();
    $fill = function ($list) {
        $out = collect();
        while ($list->isNotEmpty() && $out->count() < 5) {
            $out = $out->concat($list);
        }

        return $out;
    };
    $rowA = $fill($items);
    $rowB = $fill($items->count() > 2 ? $items->reverse()->values() : $items);
@endphp
<section id="testimonials" class="{{ $ds->section($tone, 'overflow-hidden') }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Testimoni', 'title' => $section->title ?: 'Dicintai oleh Klien Kami', 'subtitle' => $section->subtitle, 'number' => $index])
    </div>

    <div class="mt-14 space-y-5 [mask-image:linear-gradient(to_right,transparent,black_10%,black_90%,transparent)]" {!! $ds->reveal(1) !!}>
        @foreach ([[$rowA, 'animate-marquee'], [$rowB, 'animate-marquee-reverse']] as [$row, $anim])
            <div class="group flex overflow-hidden">
                <div class="flex w-max shrink-0 {{ $anim }} group-hover:[animation-play-state:paused] motion-reduce:animate-none">
                    @foreach ([false, true] as $dup)
                        <div class="flex shrink-0 gap-5 pr-5" @if ($dup) aria-hidden="true" @endif>
                            @foreach ($row as $testimonial)
                                <figure class="{{ $ds->card('flex w-[19rem] shrink-0 flex-col p-6 sm:w-[24rem]', false) }}">
                                    <figcaption class="flex items-center gap-3">
                                        <x-site.img :src="$testimonial->url('photo')" :alt="$dup ? '' : $testimonial->customer_name" icon="user" class="size-10 shrink-0 rounded-full object-cover" />
                                        <span class="min-w-0 flex-1">
                                            <span class="block truncate text-sm font-semibold text-ink">{{ $testimonial->customer_name }}</span>
                                            @if ($testimonial->company)<span class="block truncate text-xs text-muted">{{ $testimonial->company }}</span>@endif
                                        </span>
                                        <x-site.stars :rating="$testimonial->rating ?: 5" class="hidden shrink-0 text-ink sm:flex [&_svg]:size-3.5" />
                                    </figcaption>
                                    <blockquote class="mt-4 line-clamp-5 text-[0.95rem] leading-relaxed text-ink/90">“{{ $testimonial->testimonial }}”</blockquote>
                                </figure>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>
