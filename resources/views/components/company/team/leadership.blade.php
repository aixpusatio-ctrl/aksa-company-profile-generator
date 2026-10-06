{{-- Team: Leadership — first two members as large leadership feature cards with bio, the rest in a compact grid. --}}
@php
    $leaders = $company->team->take(2);
    $others = $company->team->slice(2);
@endphp
<section id="team" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Kepemimpinan', 'title' => $section->title ?: 'Pimpinan & Tim Ahli', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 grid gap-6 {{ $leaders->count() > 1 ? 'xl:grid-cols-2' : '' }}">
            @foreach ($leaders as $member)
                <article class="{{ $ds->card('group grid overflow-hidden sm:grid-cols-5', false) }}" {!! $ds->reveal($loop->index) !!}>
                    <div class="relative overflow-hidden sm:col-span-2">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/3] h-full w-full object-cover object-top transition duration-[1200ms] group-hover:scale-105 sm:aspect-auto sm:min-h-80" />
                    </div>
                    <div class="flex flex-col p-7 sm:col-span-3 sm:p-9">
                        <p class="inline-flex items-center gap-2 text-xs font-semibold tracking-[0.18em] text-primary uppercase"><span class="h-px w-6 bg-primary"></span>{{ $member->position }}</p>
                        <h3 class="heading mt-3 text-2xl break-words sm:text-3xl">{{ $member->name }}</h3>
                        @if ($member->bio)<p class="mt-4 line-clamp-6 leading-relaxed text-muted">{{ $member->bio }}</p>@endif
                        @if ($member->linkedin || $member->email)
                            <div class="mt-auto pt-6"><div class="flex flex-wrap gap-x-6 gap-y-2 border-t border-line pt-4">
                                @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-ink transition hover:text-primary"><x-icon name="linkedin" class="size-4" /> LinkedIn</a>@endif
                                @if ($member->email)<a href="mailto:{{ $member->email }}" class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-ink transition hover:text-primary"><x-icon name="mail" class="size-4 shrink-0" /> Email</a>@endif
                            </div></div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        @if ($others->isNotEmpty())
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($others as $member)
                    <article class="{{ $ds->card('flex items-center gap-4 p-4') }}" {!! $ds->reveal($loop->index % 4) !!}>
                        <div class="shrink-0 overflow-hidden rounded-full">
                            <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="size-16 object-cover object-top" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="heading text-base leading-snug break-words">{{ $member->name }}</h3>
                            <p class="mt-0.5 text-xs text-muted">{{ $member->position }}</p>
                            @if ($member->linkedin || $member->email)
                                <div class="mt-1 -ml-2 flex">
                                    @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn {{ $member->name }}" class="inline-flex size-9 items-center justify-center rounded-full text-muted transition hover:text-primary"><x-icon name="linkedin" class="size-4" /></a>@endif
                                    @if ($member->email)<a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}" class="inline-flex size-9 items-center justify-center rounded-full text-muted transition hover:text-primary"><x-icon name="mail" class="size-4" /></a>@endif
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>
