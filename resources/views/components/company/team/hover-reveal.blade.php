{{-- Team: Hover reveal — photo cards; hovering (or keyboard focus) slides up a brand panel with bio and links; on touch screens the info sits below the photo. --}}
<section id="team" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Tim Kami', 'title' => $section->title ?: 'Tim di Balik Setiap Karya', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-4 lg:gap-y-6">
            @foreach ($company->team as $member)
                <article class="group" {!! $ds->reveal($loop->index % 4) !!}>
                    <div class="relative overflow-hidden {{ $ds->img() }}">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/5] w-full object-cover object-top transition duration-700 group-hover:scale-105 lg:aspect-[3/4]" />

                        {{-- Desktop: resting caption --}}
                        <div class="pointer-events-none absolute inset-x-0 bottom-0 hidden bg-gradient-to-t from-black/75 to-transparent p-5 pt-16 text-white transition duration-300 group-focus-within:opacity-0 group-hover:opacity-0 lg:block" aria-hidden="true">
                            <p class="heading text-lg leading-tight">{{ $member->name }}</p>
                            <p class="mt-0.5 text-sm text-white/80">{{ $member->position }}</p>
                        </div>

                        {{-- Desktop: reveal panel --}}
                        <div class="tone-primary absolute inset-0 hidden translate-y-full flex-col justify-end bg-primary/95 p-6 text-on-primary backdrop-blur-sm transition duration-500 ease-out group-focus-within:translate-y-0 group-hover:translate-y-0 lg:flex">
                            <h3 class="heading text-xl leading-tight break-words">{{ $member->name }}</h3>
                            <p class="mt-1 text-sm text-on-primary/80">{{ $member->position }}</p>
                            @if ($member->bio)<p class="mt-4 line-clamp-6 border-t border-line pt-4 text-sm leading-relaxed text-on-primary/90">{{ $member->bio }}</p>@endif
                            @if ($member->linkedin || $member->email)
                                <div class="mt-5 flex gap-2">
                                    @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn {{ $member->name }}" class="inline-flex size-10 items-center justify-center rounded-full border border-on-primary/40 transition hover:bg-on-primary hover:text-primary"><x-icon name="linkedin" class="size-4" /></a>@endif
                                    @if ($member->email)<a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}" class="inline-flex size-10 items-center justify-center rounded-full border border-on-primary/40 transition hover:bg-on-primary hover:text-primary"><x-icon name="mail" class="size-4" /></a>@endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Mobile / tablet: info below the photo --}}
                    <div class="pt-5 lg:hidden">
                        <h3 class="heading text-xl leading-tight break-words">{{ $member->name }}</h3>
                        <p class="mt-1 text-sm text-primary">{{ $member->position }}</p>
                        @if ($member->bio)<p class="mt-3 line-clamp-4 text-sm leading-relaxed text-muted">{{ $member->bio }}</p>@endif
                        @if ($member->linkedin || $member->email)
                            <div class="mt-4 flex gap-2">
                                @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn {{ $member->name }}" class="inline-flex size-11 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:text-primary"><x-icon name="linkedin" class="size-4" /></a>@endif
                                @if ($member->email)<a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}" class="inline-flex size-11 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:text-primary"><x-icon name="mail" class="size-4" /></a>@endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
