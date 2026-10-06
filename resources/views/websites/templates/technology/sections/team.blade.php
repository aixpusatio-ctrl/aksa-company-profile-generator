{{-- Technology team: monochrome portraits that light up on hover, mono roles. --}}
<section id="team" class="py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="max-w-2xl">
            <p class="font-mono text-sm text-primary">// tim</p>
            <h2 class="mt-4 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Engineer, desainer, dan pemecah masalah' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-slate-400">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group rounded-brand border border-white/10 bg-white/[0.02] p-3 transition hover:border-primary/40">
                    <div class="relative overflow-hidden rounded-[calc(var(--brand-radius)-6px)]">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-square w-full object-cover grayscale transition duration-500 group-hover:grayscale-0" />
                        <div class="absolute inset-0 bg-[linear-gradient(transparent_50%,rgb(0_0_0/0.25)_50%)] bg-[size:100%_4px] opacity-40 transition group-hover:opacity-0"></div>
                    </div>
                    <div class="flex items-start justify-between gap-3 px-2 pt-4 pb-2">
                        <div class="min-w-0">
                            <h3 class="font-heading text-base font-semibold text-white">{{ $member->name }}</h3>
                            <p class="mt-1 font-mono text-xs text-primary">{{ $member->position }}</p>
                        </div>
                        <div class="flex shrink-0 gap-1">
                            @if ($member->linkedin)
                                <a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-8 items-center justify-center rounded-lg border border-white/10 text-slate-400 transition hover:text-white" aria-label="LinkedIn {{ $member->name }}"><x-icon name="linkedin" class="size-3.5" /></a>
                            @endif
                            @if ($member->email)
                                <a href="mailto:{{ $member->email }}" class="inline-flex size-8 items-center justify-center rounded-lg border border-white/10 text-slate-400 transition hover:text-white" aria-label="Email {{ $member->name }}"><x-icon name="mail" class="size-3.5" /></a>
                            @endif
                        </div>
                    </div>
                    @if ($member->bio)<p class="px-2 pb-2 text-xs leading-relaxed text-slate-500">{{ $member->bio }}</p>@endif
                </article>
            @endforeach
        </div>
    </div>
</section>
