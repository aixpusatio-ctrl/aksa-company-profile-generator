{{-- Consulting team: minimalist portraits, grayscale to color on hover. --}}
<section id="team" class="py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-end">
            <div class="lg:col-span-7">
                <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Partner & Konsultan</p>
                <h2 class="mt-6 font-heading text-4xl leading-tight text-stone-900 md:text-5xl">{{ $section->title ?: 'Orang-orang di balik setiap rekomendasi' }}</h2>
            </div>
            @if ($section->subtitle)<p class="text-stone-500 lg:col-span-4 lg:col-start-9">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-16 grid gap-x-8 gap-y-14 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group {{ $loop->even ? 'lg:mt-16' : '' }}">
                    <div class="overflow-hidden">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[3/4] w-full object-cover grayscale transition duration-700 group-hover:scale-[1.03] group-hover:grayscale-0" />
                    </div>
                    <div class="mt-5 flex items-start justify-between gap-3 border-t border-stone-200 pt-4">
                        <div>
                            <h3 class="font-heading text-xl text-stone-900">{{ $member->name }}</h3>
                            <p class="mt-1 text-xs tracking-[0.15em] text-stone-400 uppercase">{{ $member->position }}</p>
                        </div>
                        <div class="flex gap-2 pt-1 text-stone-400">
                            @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="hover:text-primary" aria-label="LinkedIn"><x-icon name="linkedin" class="size-4" /></a>@endif
                            @if ($member->email)<a href="mailto:{{ $member->email }}" class="hover:text-primary" aria-label="Email"><x-icon name="mail" class="size-4" /></a>@endif
                        </div>
                    </div>
                    @if ($member->bio)<p class="mt-3 text-sm leading-relaxed text-stone-500">{{ $member->bio }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
