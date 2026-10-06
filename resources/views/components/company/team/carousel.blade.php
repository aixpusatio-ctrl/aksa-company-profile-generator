{{-- Team: Carousel — horizontal scroll-snap rail of member cards with prev/next arrows. --}}
@php
    // Rail spans the full section width; its inline padding lines the first card up with the container.
    $railPad = match ($ds->get('container')) {
        'wide' => '[--rail:1.25rem] sm:[--rail:2rem] lg:[--rail:3rem] min-[88rem]:[--rail:calc((100%-88rem)/2+3rem)]',
        'narrow' => '[--rail:1.25rem] sm:[--rail:2rem] min-[64rem]:[--rail:calc((100%-64rem)/2+2rem)]',
        'full' => '[--rail:1.25rem] sm:[--rail:2rem] lg:[--rail:3.5rem]',
        default => '[--rail:1.25rem] sm:[--rail:2rem] min-[80rem]:[--rail:calc((100%-80rem)/2+2rem)]',
    };
@endphp
<section id="team" class="{{ $ds->section($tone, 'overflow-hidden') }}"
    x-data="{ atStart: true, atEnd: false, update() { const r = this.$refs.rail; this.atStart = r.scrollLeft < 8; this.atEnd = r.scrollLeft + r.clientWidth >= r.scrollWidth - 8 }, go(dir) { const r = this.$refs.rail; r.scrollBy({ left: dir * r.clientWidth * 0.8, behavior: 'smooth' }) } }"
    x-init="$nextTick(() => update())">
    <div class="{{ $ds->container() }}">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            @include('components.company.partials.heading', ['eyebrow' => 'Tim Kami', 'title' => $section->title ?: 'Bertemu dengan Tim Kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            @if ($company->team->count() > 1)
                <div class="flex shrink-0 gap-2" x-show="!(atStart && atEnd)" {!! $ds->reveal(1) !!}>
                    <button type="button" @click="go(-1)" :disabled="atStart" aria-label="Anggota tim sebelumnya" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary disabled:pointer-events-none disabled:opacity-30"><x-icon name="arrow-left" class="size-5" /></button>
                    <button type="button" @click="go(1)" :disabled="atEnd" aria-label="Anggota tim berikutnya" class="inline-flex size-12 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:bg-primary hover:text-on-primary disabled:pointer-events-none disabled:opacity-30"><x-icon name="arrow-right" class="size-5" /></button>
                </div>
            @endif
        </div>

    </div>

        <div x-ref="rail" @scroll.debounce.60ms="update()" tabindex="0" role="region" aria-label="Anggota tim"
            class="{{ $railPad }} mt-12 flex snap-x snap-mandatory scroll-px-[var(--rail)] gap-5 overflow-x-auto px-[var(--rail)] pb-4 [scrollbar-width:none] focus:outline-none lg:gap-6 [&::-webkit-scrollbar]:hidden">
            @foreach ($company->team as $member)
                <article class="{{ $ds->card('group w-[74%] shrink-0 snap-start overflow-hidden sm:w-[44%] lg:w-[min(23%,18.5rem)]') }}" {!! $ds->reveal(min($loop->index, 4)) !!}>
                    <div class="overflow-hidden">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/5] w-full object-cover object-top transition duration-700 group-hover:scale-105" />
                    </div>
                    <div class="p-5">
                        <h3 class="heading text-lg leading-snug break-words">{{ $member->name }}</h3>
                        <p class="mt-1 text-sm text-primary">{{ $member->position }}</p>
                        @if ($member->bio)<p class="mt-3 line-clamp-2 text-sm leading-relaxed text-muted">{{ $member->bio }}</p>@endif
                        @if ($member->linkedin || $member->email)
                            <div class="mt-4 -ml-2 flex border-t border-line pt-3">
                                @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn {{ $member->name }}" class="inline-flex size-11 items-center justify-center rounded-full text-muted transition hover:bg-primary/10 hover:text-primary"><x-icon name="linkedin" class="size-4" /></a>@endif
                                @if ($member->email)<a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}" class="inline-flex size-11 items-center justify-center rounded-full text-muted transition hover:bg-primary/10 hover:text-primary"><x-icon name="mail" class="size-4" /></a>@endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
</section>
