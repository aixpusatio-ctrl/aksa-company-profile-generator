{{-- Testimonials: Split — large image with rating summary left, switchable stacked quotes right. --}}
@php
    $items = $company->testimonials->values();
    $avg = $items->count() ? round((float) ($items->avg('rating') ?: 5), 1) : 5;
    $image = $items->map(fn ($t) => $t->url('photo'))->filter()->first() ?? $company->gallery->first()?->url('image');
@endphp
<section id="testimonials" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid items-center gap-12 lg:grid-cols-12 lg:gap-16" x-data="{ i: 0 }">
        <div class="relative lg:col-span-5" {!! $ds->reveal(0, 'left') !!}>
            <x-site.img :src="$image" :alt="$company->name" icon="users" class="{{ $ds->img('aspect-[4/5] w-full object-cover') }} max-h-[34rem] sm:max-h-none" />
            <div class="absolute right-4 bottom-4 left-4 flex items-center gap-5 rounded-brand bg-card p-5 shadow-2xl ring-1 ring-line sm:right-auto sm:bottom-6 sm:left-6 sm:p-6">
                <p class="heading text-5xl text-ink">{{ number_format($avg, 1, ',', '.') }}</p>
                <div>
                    <x-site.stars :rating="(int) round($avg)" class="text-ink" />
                    <p class="mt-1 text-sm text-muted">dari {{ $items->count() }} ulasan klien</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7">
            @include('components.company.partials.heading', ['eyebrow' => 'Testimoni', 'title' => $section->title ?: 'Kepercayaan yang Kami Jaga', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])

            <div class="mt-10 grid" {!! $ds->reveal(1) !!} aria-live="polite">
                @foreach ($items as $testimonial)
                    <blockquote class="[grid-area:1/1] border-l-2 border-primary pl-6 transition duration-500 sm:pl-8"
                        @unless ($loop->first) x-cloak @endunless
                        :class="i === {{ $loop->index }} ? 'opacity-100 translate-x-0' : 'pointer-events-none opacity-0 translate-x-4'"
                        :aria-hidden="i !== {{ $loop->index }}">
                        <p class="heading text-xl !leading-snug text-ink sm:text-2xl">“{{ $testimonial->testimonial }}”</p>
                    </blockquote>
                @endforeach
            </div>

            <div class="mt-10 divide-y divide-line border-y border-line">
                @foreach ($items as $testimonial)
                    <button type="button" @click="i = {{ $loop->index }}" class="flex w-full items-center gap-4 py-4 text-left transition" :class="i === {{ $loop->index }} ? 'opacity-100' : 'opacity-55 hover:opacity-90'" :aria-pressed="i === {{ $loop->index }}" {!! $ds->reveal($loop->index + 2) !!}>
                        <x-site.img :src="$testimonial->url('photo')" :alt="$testimonial->customer_name" icon="user" class="size-11 shrink-0 rounded-full object-cover" />
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-semibold text-ink">{{ $testimonial->customer_name }}</span>
                            @if ($testimonial->company)<span class="block truncate text-sm text-muted">{{ $testimonial->company }}</span>@endif
                        </span>
                        <span class="h-0.5 shrink-0 rounded-full bg-primary transition-all duration-500" :class="i === {{ $loop->index }} ? 'w-10' : 'w-0'"></span>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>
