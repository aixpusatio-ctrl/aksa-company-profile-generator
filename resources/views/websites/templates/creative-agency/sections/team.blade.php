{{-- Creative team: playful tilted portrait cards. --}}
<section id="team" class="py-20 lg:py-28">
    <div class="mx-auto max-w-[90rem] px-6 sm:px-10">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <h2 class="font-heading text-5xl leading-[0.9] font-extrabold tracking-tighter text-neutral-950 md:text-7xl lg:text-8xl">{{ $section->title ?: 'Para pembuat onar' }}<span class="text-primary">.</span></h2>
            <p class="max-w-sm text-neutral-600">{{ $section->subtitle ?: 'Desainer, penulis, strategist, dan developer yang tidak bisa berhenti berkarya.' }}</p>
        </div>
        <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group rounded-brand bg-white p-3 transition duration-500 hover:z-10 hover:shadow-2xl {{ $loop->odd ? 'hover:-rotate-3' : 'hover:rotate-3' }}">
                    <div class="relative overflow-hidden rounded-[calc(var(--brand-radius)*0.75)]">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/5] w-full object-cover transition duration-700 group-hover:scale-105" />
                        <span class="absolute top-3 right-3 rotate-6 rounded-full bg-primary px-3 py-1 text-xs font-extrabold text-on-primary opacity-0 transition group-hover:opacity-100">Hi! 👋</span>
                    </div>
                    <div class="flex items-end justify-between gap-3 px-2 pt-4 pb-2">
                        <div class="min-w-0">
                            <h3 class="font-heading text-xl font-extrabold tracking-tight text-neutral-950">{{ $member->name }}</h3>
                            <p class="text-sm text-neutral-500">{{ $member->position }}</p>
                        </div>
                        <div class="flex shrink-0 gap-1.5">
                            @if ($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-9 items-center justify-center rounded-full bg-neutral-100 text-neutral-700 transition hover:bg-primary hover:text-on-primary" aria-label="LinkedIn"><x-icon name="linkedin" class="size-4" /></a>@endif
                            @if ($member->email)<a href="mailto:{{ $member->email }}" class="inline-flex size-9 items-center justify-center rounded-full bg-neutral-100 text-neutral-700 transition hover:bg-primary hover:text-on-primary" aria-label="Email"><x-icon name="mail" class="size-4" /></a>@endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
