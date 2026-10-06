{{-- Minimal team: names + roles list, photo shown on hover. --}}
<section id="team" class="mx-auto max-w-4xl px-6">
    <div class="grid gap-8 border-t border-neutral-200 py-16 md:grid-cols-4 md:py-24">
        <div>
            <p class="text-xs text-neutral-400 tabular-nums">({{ str_pad($index, 2, '0', STR_PAD_LEFT) }})</p>
            <h2 class="mt-1 text-sm font-medium text-neutral-950">Tim</h2>
        </div>
        <div class="md:col-span-3">
            <p class="font-heading text-2xl leading-snug font-medium tracking-tight text-neutral-950 md:text-3xl">{{ $section->title ?: 'Orang-orang di balik pekerjaan kami.' }}</p>
            @if ($section->subtitle)<p class="mt-4 text-neutral-500">{{ $section->subtitle }}</p>@endif
            <ul class="mt-10 border-b border-neutral-200">
                @foreach ($company->team as $member)
                    <li x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false" class="relative grid gap-1 border-t border-neutral-200 py-5 sm:grid-cols-12 sm:items-baseline sm:gap-4">
                        <span class="font-heading text-lg font-medium tracking-tight text-neutral-950 sm:col-span-5">{{ $member->name }}</span>
                        <span class="text-sm text-neutral-500 sm:col-span-5">{{ $member->position }}</span>
                        <span class="flex gap-3 text-sm text-neutral-400 sm:col-span-2 sm:justify-end">
                            @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="hover:text-neutral-950">in</a>@endif
                            @if ($member->email)<a href="mailto:{{ $member->email }}" class="hover:text-neutral-950">@</a>@endif
                        </span>
                        <div x-cloak x-show="hover" x-transition.opacity.duration.200ms class="pointer-events-none absolute top-1/2 right-24 z-10 hidden w-36 -translate-y-1/2 lg:block">
                            <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/5] w-full object-cover grayscale" />
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
