{{-- Executive team: elegant framed portraits on ivory. --}}
<section id="team" class="bg-stone-50 py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="flex items-center justify-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase"><span class="h-px w-10 bg-primary"></span> Kepemimpinan <span class="h-px w-10 bg-primary"></span></p>
            <h2 class="mt-6 font-heading text-4xl leading-[1.1] font-medium text-secondary md:text-5xl">{{ $section->title ?: 'Dewan & Para Mitra' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-slate-500">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-20 grid gap-x-10 gap-y-16 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group text-center">
                    <div class="relative p-3">
                        <div class="absolute inset-0 border border-primary/50 transition duration-500 group-hover:inset-1"></div>
                        <div class="overflow-hidden">
                            <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[3/4] w-full object-cover grayscale transition duration-700 group-hover:scale-105 group-hover:grayscale-0" />
                        </div>
                    </div>
                    <h3 class="mt-7 font-heading text-2xl font-medium text-secondary">{{ $member->name }}</h3>
                    <p class="mt-2 text-[10px] tracking-[0.3em] text-primary uppercase">{{ $member->position }}</p>
                    @if ($member->bio)
                        <p class="mx-auto mt-4 max-w-xs text-sm leading-relaxed text-slate-500">{{ $member->bio }}</p>
                    @endif
                    @if ($member->linkedin || $member->email)
                        <div class="mt-5 flex justify-center gap-2">
                            @if ($member->linkedin)
                                <a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-8 items-center justify-center border border-slate-300 text-slate-500 transition hover:border-primary hover:text-primary" aria-label="LinkedIn"><x-icon name="linkedin" class="size-3.5" /></a>
                            @endif
                            @if ($member->email)
                                <a href="mailto:{{ $member->email }}" class="inline-flex size-8 items-center justify-center border border-slate-300 text-slate-500 transition hover:border-primary hover:text-primary" aria-label="Email"><x-icon name="mail" class="size-3.5" /></a>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
