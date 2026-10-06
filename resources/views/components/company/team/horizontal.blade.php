{{-- Team: Horizontal — two-column horizontal cards: photo left, name, role, bio and contact links right. --}}
<section id="team" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Tim Kami', 'title' => $section->title ?: 'Kenali Tim Kami', 'subtitle' => $section->subtitle, 'number' => $index])

        <div class="mt-14 grid gap-6 lg:grid-cols-2">
            @foreach ($company->team as $member)
                <article class="{{ $ds->card('group flex flex-col overflow-hidden sm:flex-row') }}" {!! $ds->reveal($loop->index % 2) !!}>
                    <div class="relative shrink-0 overflow-hidden sm:w-[42%] lg:w-[40%]">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/3] h-full w-full object-cover object-top transition duration-700 group-hover:scale-105 sm:aspect-auto sm:min-h-64" />
                    </div>
                    <div class="flex min-w-0 flex-1 flex-col p-6 sm:p-7">
                        <p class="text-xs font-semibold tracking-[0.16em] text-primary uppercase">{{ $member->position }}</p>
                        <h3 class="heading mt-2 text-h3 break-words">{{ $member->name }}</h3>
                        @if ($member->bio)<p class="mt-3 line-clamp-4 text-sm leading-relaxed text-muted">{{ $member->bio }}</p>@endif
                        @if ($member->linkedin || $member->email)
                            <div class="mt-auto flex flex-wrap gap-2 pt-6">
                                @if ($member->linkedin)
                                    <a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn {{ $member->name }}" class="inline-flex min-h-11 items-center gap-2 rounded-btn border border-line px-3.5 text-sm font-medium text-ink transition hover:border-primary hover:text-primary"><x-icon name="linkedin" class="size-4" /> LinkedIn</a>
                                @endif
                                @if ($member->email)
                                    <a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}" class="inline-flex min-h-11 items-center gap-2 rounded-btn border border-line px-3.5 text-sm font-medium text-ink transition hover:border-primary hover:text-primary"><x-icon name="mail" class="size-4" /> Email</a>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
