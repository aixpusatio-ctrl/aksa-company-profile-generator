{{-- Team: List — minimal directory rows (name, position, links); hovering a row reveals a small photo. --}}
<section id="team" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="grid gap-10 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <div class="lg:sticky lg:top-28">
                    @include('components.company.partials.heading', ['eyebrow' => 'Tim Kami', 'title' => $section->title ?: 'Tim Inti Kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
                </div>
            </div>
            <ul class="border-b border-line lg:col-span-7" {!! $ds->reveal(1) !!}>
                @foreach ($company->team as $member)
                    <li class="group flex items-center gap-4 border-t border-line py-5 transition sm:gap-6 sm:py-6">
                        <div class="size-14 shrink-0 overflow-hidden rounded-full transition-all duration-500 ease-out lg:w-0 lg:opacity-0 lg:group-hover:w-14 lg:group-hover:opacity-100">
                            <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="size-14 max-w-none object-cover object-top" />
                        </div>
                        <div class="min-w-0 flex-1 sm:grid sm:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)] sm:items-baseline sm:gap-6">
                            <h3 class="heading text-xl leading-tight break-words transition group-hover:text-primary sm:text-2xl">{{ $member->name }}</h3>
                            <p class="mt-1 text-sm text-muted sm:mt-0">{{ $member->position }}</p>
                        </div>
                        <div class="flex shrink-0 gap-1">
                            @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn {{ $member->name }}" class="inline-flex size-11 items-center justify-center rounded-full border border-transparent text-muted transition hover:border-line hover:text-primary"><x-icon name="linkedin" class="size-4" /></a>@endif
                            @if ($member->email)<a href="mailto:{{ $member->email }}" aria-label="Email {{ $member->name }}" class="inline-flex size-11 items-center justify-center rounded-full border border-transparent text-muted transition hover:border-line hover:text-primary"><x-icon name="mail" class="size-4" /></a>@endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
