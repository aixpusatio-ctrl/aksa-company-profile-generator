{{-- Team: Grid — portrait cards with name, role and social links. --}}
<section id="team" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Tim Kami', 'title' => $section->title ?: 'Orang-orang di Balik Kesuksesan Kami', 'subtitle' => $section->subtitle, 'number' => $index])
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group" {!! $ds->reveal($loop->index) !!}>
                    <div class="relative overflow-hidden {{ $ds->img() }}">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/5] w-full object-cover transition duration-700 group-hover:scale-105" />
                        <div class="absolute inset-x-3 bottom-3 flex justify-center gap-2 transition lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100">
                            @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-10 items-center justify-center rounded-full bg-card text-ink shadow-sm transition hover:bg-primary hover:text-on-primary" aria-label="LinkedIn {{ $member->name }}"><x-icon name="linkedin" class="size-4" /></a>@endif
                            @if ($member->email)<a href="mailto:{{ $member->email }}" class="inline-flex size-10 items-center justify-center rounded-full bg-card text-ink shadow-sm transition hover:bg-primary hover:text-on-primary" aria-label="Email {{ $member->name }}"><x-icon name="mail" class="size-4" /></a>@endif
                        </div>
                    </div>
                    <h3 class="heading mt-5 text-lg">{{ $member->name }}</h3>
                    <p class="text-sm text-primary">{{ $member->position }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
