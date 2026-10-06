{{-- Corporate team: 4-column portrait cards with social links. --}}
<section id="team" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold tracking-widest text-primary uppercase">Tim Kami</p>
            <h2 class="mt-3 font-heading text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $section->title ?: 'Dipimpin oleh Para Profesional' }}</h2>
            @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-14 grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group text-center">
                    <div class="relative overflow-hidden rounded-brand">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/5] w-full object-cover transition duration-500 group-hover:scale-105" />
                        <div class="absolute inset-x-0 bottom-0 flex justify-center gap-2 bg-gradient-to-t from-black/60 to-transparent p-4 opacity-0 transition group-hover:opacity-100">
                            @if ($member->linkedin)
                                <a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-9 items-center justify-center rounded-full bg-white text-slate-900" aria-label="LinkedIn"><x-icon name="linkedin" class="size-4" /></a>
                            @endif
                            @if ($member->email)
                                <a href="mailto:{{ $member->email }}" class="inline-flex size-9 items-center justify-center rounded-full bg-white text-slate-900" aria-label="Email"><x-icon name="mail" class="size-4" /></a>
                            @endif
                        </div>
                    </div>
                    <h3 class="mt-5 font-heading text-lg font-bold text-slate-900">{{ $member->name }}</h3>
                    <p class="text-sm font-medium text-primary">{{ $member->position }}</p>
                    @if ($member->bio)
                        <p class="mt-2 text-sm text-slate-500">{{ $member->bio }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
