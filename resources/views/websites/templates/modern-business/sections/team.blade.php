{{-- Modern Business team: portrait cards with floating white name card. --}}
<section id="team" class="bg-slate-50 py-20 lg:py-32">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="mx-auto max-w-2xl text-center">
            <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-4 py-1.5 text-xs font-semibold text-primary"><x-icon name="users" class="size-3.5" /> Tim Kami</span>
            <h2 class="mt-5 font-heading text-3xl font-semibold tracking-tight text-slate-900 sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Orang-Orang di Balik Kesuksesan' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-lg text-slate-500">{{ $section->subtitle }}</p>@endif
        </div>
        <div class="mt-16 grid gap-x-6 gap-y-14 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($company->team as $member)
                <article class="group relative">
                    <div class="overflow-hidden rounded-brand bg-linear-to-br from-primary/20 to-secondary/20">
                        <x-site.img :src="$member->url('photo')" :alt="$member->name" icon="user" class="aspect-[4/5] w-full object-cover transition duration-700 group-hover:scale-105" />
                    </div>
                    <div class="relative mx-4 -mt-16 rounded-2xl bg-white p-5 text-center shadow-xl shadow-slate-900/10 transition duration-300 group-hover:-translate-y-2">
                        <h3 class="font-heading text-lg font-semibold text-slate-900">{{ $member->name }}</h3>
                        <p class="mt-0.5 text-sm font-medium text-primary">{{ $member->position }}</p>
                        @if ($member->linkedin || $member->email)
                            <div class="mt-3 flex justify-center gap-2">
                                @if ($member->linkedin)
                                    <a href="{{ $member->linkedin }}" target="_blank" rel="noopener noreferrer" class="inline-flex size-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-primary hover:text-on-primary" aria-label="LinkedIn {{ $member->name }}"><x-icon name="linkedin" class="size-3.5" /></a>
                                @endif
                                @if ($member->email)
                                    <a href="mailto:{{ $member->email }}" class="inline-flex size-8 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-primary hover:text-on-primary" aria-label="Email {{ $member->name }}"><x-icon name="mail" class="size-3.5" /></a>
                                @endif
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
