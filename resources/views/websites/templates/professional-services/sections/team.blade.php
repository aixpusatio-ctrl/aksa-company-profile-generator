{{-- Professional services team: horizontal profile cards with bio and contact links. --}}
<section id="team" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
            <div class="max-w-2xl">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-10 bg-secondary"></span> Profesional Kami</p>
                <h2 class="mt-4 font-heading text-3xl leading-tight text-slate-900 md:text-4xl">{{ $section->title ?: 'Ditangani oleh Para Ahli' }}</h2>
                @if ($section->subtitle)<p class="mt-4 text-slate-600">{{ $section->subtitle }}</p>@endif
            </div>
            <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary">Bicara dengan tim kami <x-icon name="arrow-right" class="size-4" /></a>
        </div>

        <div class="mt-12 grid gap-6 lg:grid-cols-2">
            @foreach ($company->team as $member)
                <article class="group flex flex-col overflow-hidden rounded-brand border border-slate-200 bg-white transition hover:border-primary/30 hover:shadow-lg hover:shadow-slate-900/5 sm:flex-row">
                    <div class="relative sm:w-48 sm:shrink-0">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/3] h-full w-full object-cover sm:aspect-auto sm:min-h-64" />
                        <span class="absolute bottom-0 left-0 h-1 w-full bg-secondary sm:h-full sm:w-1"></span>
                    </div>
                    <div class="flex flex-1 flex-col p-6 sm:p-7">
                        <p class="text-xs font-semibold tracking-wide text-primary uppercase">{{ $member->position }}</p>
                        <h3 class="mt-1.5 font-heading text-xl text-slate-900">{{ $member->name }}</h3>
                        @if ($member->bio)
                            <p class="mt-3 line-clamp-4 text-sm leading-relaxed text-slate-600">{{ $member->bio }}</p>
                        @endif
                        <div class="mt-auto flex flex-wrap items-center gap-2 pt-5">
                            @if ($member->linkedin)
                                <a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:border-primary hover:text-primary"><x-icon name="linkedin" class="size-3.5" /> LinkedIn</a>
                            @endif
                            @if ($member->email)
                                <a href="mailto:{{ $member->email }}" class="inline-flex items-center gap-1.5 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:border-primary hover:text-primary"><x-icon name="mail" class="size-3.5" /> Email</a>
                            @endif
                            <a href="{{ $site->anchor('contact') }}" class="ml-auto inline-flex items-center gap-1 text-xs font-semibold text-primary opacity-80 group-hover:opacity-100">Buat janji <x-icon name="arrow-right" class="size-3.5" /></a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
