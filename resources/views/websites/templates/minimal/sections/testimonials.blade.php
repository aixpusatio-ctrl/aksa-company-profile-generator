{{-- Minimal testimonials: one quote at a time, text-only pager. --}}
@php($count = $company->testimonials->count())
<section id="testimonials" class="mx-auto max-w-4xl px-6" x-data="{ i: 0, n: {{ $count }} }">
    <div class="grid gap-8 border-t border-neutral-200 py-16 md:grid-cols-4 md:py-24">
        <div>
            <p class="text-xs text-neutral-400 tabular-nums">({{ str_pad($index, 2, '0', STR_PAD_LEFT) }})</p>
            <h2 class="mt-1 text-sm font-medium text-neutral-950">{{ $section->title ?: 'Kata klien' }}</h2>
            @if ($section->subtitle)<p class="mt-2 text-sm text-neutral-500">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="md:col-span-3">
            <div class="grid">
                @foreach ($company->testimonials as $testimonial)
                    <figure x-show="i === {{ $loop->index }}" @if (! $loop->first) x-cloak @endif x-transition.opacity.duration.400ms class="col-start-1 row-start-1">
                        <blockquote class="font-heading text-2xl leading-snug font-medium tracking-tight text-neutral-950 md:text-[2rem]">“{{ $testimonial->testimonial }}”</blockquote>
                        <figcaption class="mt-8 text-sm">
                            <span class="text-neutral-950">{{ $testimonial->customer_name }}</span>
                            @if ($testimonial->company)<span class="text-neutral-400"> — {{ $testimonial->company }}</span>@endif
                            <span class="ml-2 text-neutral-400 tabular-nums">{{ str_repeat('★', (int) $testimonial->rating) }}</span>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
            @if ($count > 1)
                <div class="mt-10 flex items-center gap-6 text-sm">
                    <button type="button" @click="i = (i - 1 + n) % n" class="text-neutral-500 hover:text-neutral-950">← Sebelumnya</button>
                    <span class="text-neutral-400 tabular-nums"><span x-text="String(i + 1).padStart(2, '0')">01</span> / {{ str_pad($count, 2, '0', STR_PAD_LEFT) }}</span>
                    <button type="button" @click="i = (i + 1) % n" class="text-neutral-500 hover:text-neutral-950">Berikutnya →</button>
                </div>
            @endif
        </div>
    </div>
</section>
