{{-- Construction team: grayscale portraits with heavy name plate. --}}
<section id="team" class="bg-white py-20 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="grid gap-8 lg:grid-cols-2 lg:items-end">
            <div>
                <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase"><span class="h-1 w-10 bg-primary"></span> Tim Ahli</p>
                <h2 class="mt-5 font-heading text-4xl leading-[0.95] font-bold tracking-tight text-stone-950 uppercase sm:text-5xl lg:text-6xl">{{ $section->title ?: 'Dipimpin Para Ahli Lapangan' }}</h2>
            </div>
            @if ($section->subtitle)<p class="max-w-lg text-stone-500 lg:justify-self-end">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group">
                    <div class="relative overflow-hidden bg-stone-200">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/5] w-full object-cover grayscale transition duration-500 group-hover:scale-105 group-hover:grayscale-0" />
                        <div class="absolute top-0 right-0 flex flex-col">
                            @if ($member->linkedin)
                                <a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-10 translate-x-full items-center justify-center bg-primary text-on-primary transition group-hover:translate-x-0" aria-label="LinkedIn {{ $member->name }}"><x-icon name="linkedin" class="size-4" /></a>
                            @endif
                            @if ($member->email)
                                <a href="mailto:{{ $member->email }}" class="inline-flex size-10 translate-x-full items-center justify-center bg-stone-950 text-white transition delay-75 group-hover:translate-x-0" aria-label="Email {{ $member->name }}"><x-icon name="mail" class="size-4" /></a>
                            @endif
                        </div>
                    </div>
                    <div class="border-l-4 border-primary bg-stone-950 px-5 py-4">
                        <h3 class="font-heading text-lg font-bold tracking-wide text-white uppercase">{{ $member->name }}</h3>
                        <p class="mt-0.5 text-xs font-semibold tracking-[0.15em] text-primary uppercase">{{ $member->position }}</p>
                    </div>
                    @if ($member->bio)<p class="mt-4 text-sm leading-relaxed text-stone-500">{{ $member->bio }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
