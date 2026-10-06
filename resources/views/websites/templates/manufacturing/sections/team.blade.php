{{-- Manufacturing team: compact ID-badge style cards. --}}
<section id="team" class="border-t border-slate-200 bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="max-w-2xl">
            <p class="font-mono text-xs font-semibold tracking-widest text-primary uppercase">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }} — Manajemen</p>
            <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight text-slate-900 uppercase md:text-4xl">{{ $section->title ?: 'Tim di Balik Produksi' }}</h2>
            @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group flex flex-col overflow-hidden rounded-brand border border-slate-200 bg-white">
                    <div class="relative overflow-hidden">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-square w-full object-cover grayscale-[30%] transition duration-500 group-hover:scale-105 group-hover:grayscale-0" />
                        <span class="absolute bottom-0 left-0 bg-primary px-3 py-1 font-mono text-[11px] font-semibold text-on-primary">ID-{{ str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="font-heading text-lg font-bold text-slate-900">{{ $member->name }}</h3>
                        <p class="font-mono text-xs tracking-wide text-primary uppercase">{{ $member->position }}</p>
                        @if ($member->bio)<p class="mt-3 line-clamp-3 text-sm text-slate-500">{{ $member->bio }}</p>@endif
                        @if ($member->linkedin || $member->email)
                            <div class="mt-auto flex gap-2 pt-4">
                                @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-8 items-center justify-center border border-slate-200 text-slate-600 hover:border-primary hover:text-primary" aria-label="LinkedIn"><x-icon name="linkedin" class="size-4" /></a>@endif
                                @if ($member->email)<a href="mailto:{{ $member->email }}" class="inline-flex size-8 items-center justify-center border border-slate-200 text-slate-600 hover:border-primary hover:text-primary" aria-label="Email"><x-icon name="mail" class="size-4" /></a>@endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
