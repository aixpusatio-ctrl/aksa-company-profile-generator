{{-- Team: Portrait — tall editorial portraits, three per row with a staggered middle column; name in heading font beneath. --}}
<section id="team" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Tim Kami', 'title' => $section->title ?: 'Wajah di Balik Karya Kami', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-16 flex flex-wrap justify-center gap-x-8 gap-y-14 lg:gap-x-12 lg:gap-y-20">
            @foreach ($company->team as $member)
                <article class="group w-full sm:w-[calc(50%-1rem)] lg:w-[calc(33.333%-2rem)] {{ $loop->index % 3 === 1 ? 'lg:mt-16' : '' }}" {!! $ds->reveal($loop->index % 3) !!}>
                    <div class="overflow-hidden {{ $ds->img() }}">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[3/4] w-full object-cover object-top grayscale-[35%] transition duration-[1200ms] group-hover:scale-[1.04] group-hover:grayscale-0" />
                    </div>
                    <h3 class="heading mt-6 text-2xl break-words sm:text-[1.7rem]">{{ $member->name }}</h3>
                    <p class="mt-1.5 text-xs tracking-[0.18em] text-muted uppercase">{{ $member->position }}</p>
                    @if ($member->bio)<p class="mt-4 line-clamp-3 border-t border-line pt-4 text-sm leading-relaxed text-muted">{{ $member->bio }}</p>@endif
                    @if ($member->linkedin || $member->email)
                        <div class="mt-3 -ml-3 flex">
                            @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn {{ $member->name }}" class="inline-flex size-11 items-center justify-center rounded-full text-muted transition hover:bg-primary/10 hover:text-primary"><x-icon name="linkedin" class="size-4" /></a>@endif
                            @if ($member->email)<a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}" class="inline-flex size-11 items-center justify-center rounded-full text-muted transition hover:bg-primary/10 hover:text-primary"><x-icon name="mail" class="size-4" /></a>@endif
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
