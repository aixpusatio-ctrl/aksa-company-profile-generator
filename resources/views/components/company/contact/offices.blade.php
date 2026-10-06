{{-- Contact: Offices — head office card plus service areas derived from project locations, beside a form. --}}
@php
    $tel = $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null;
    $areas = $company->projects
        ->filter(fn ($p) => filled($p->location))
        ->groupBy(fn ($p) => trim($p->location))
        ->map->count()
        ->reject(fn ($count, $location) => $company->city && strcasecmp($location, $company->city) === 0)
        ->take(6);
@endphp
<section id="contact" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Kontak', 'title' => $section->title ?: 'Kantor & Area Layanan', 'subtitle' => $section->subtitle ?: 'Temui kami di kantor atau kirimkan pesan — tim kami akan merespons dalam 1x24 jam kerja.', 'number' => $index])

        <div class="mt-14 grid gap-6 lg:grid-cols-12 lg:gap-8">
            <div class="space-y-6 lg:col-span-5">
                <article class="overflow-hidden rounded-brand border border-line bg-card" {!! $ds->reveal(0) !!}>
                    @if ($company->mapEmbedUrl())
                        @include('websites.partials.map', ['mapClass' => 'h-48 w-full border-0'])
                    @endif
                    <div class="p-6 sm:p-8">
                        <span class="inline-flex items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary"><x-icon name="building" class="size-3.5" /> Kantor Pusat</span>
                        <h3 class="heading mt-4 text-2xl">{{ $company->city ?: $company->name }}</h3>
                        @if ($company->fullAddress())<p class="mt-2 leading-relaxed text-muted">{{ $company->fullAddress() }}</p>@endif
                        <dl class="mt-6 space-y-3 border-t border-line pt-6 text-sm">
                            @if ($company->phone)
                                <div class="flex items-center gap-3"><dt><x-icon name="phone" class="size-4 text-primary" /><span class="sr-only">Telepon</span></dt><dd><a href="{{ $tel }}" class="text-ink hover:text-primary">{{ $company->phone }}</a></dd></div>
                            @endif
                            @if ($company->email)
                                <div class="flex items-center gap-3"><dt><x-icon name="mail" class="size-4 text-primary" /><span class="sr-only">Email</span></dt><dd class="min-w-0"><a href="mailto:{{ $company->email }}" class="break-all text-ink hover:text-primary">{{ $company->email }}</a></dd></div>
                            @endif
                            @if ($company->working_hours)
                                <div class="flex items-center gap-3"><dt><x-icon name="clock" class="size-4 text-primary" /><span class="sr-only">Jam kerja</span></dt><dd class="text-ink">{{ $company->working_hours }}</dd></div>
                            @endif
                        </dl>
                        @if ($company->google_maps_url)
                            <a href="{{ $company->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="{{ $ds->btn('link', 'mt-6') }}">Petunjuk arah <x-icon name="arrow-up-right" class="size-4" /></a>
                        @endif
                    </div>
                </article>

                @if ($areas->isNotEmpty())
                    <div {!! $ds->reveal(1) !!}>
                        <p class="text-xs font-semibold tracking-[0.18em] text-muted uppercase">Area layanan / Proyek</p>
                        <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                            @foreach ($areas as $location => $count)
                                <li class="flex items-center gap-3 rounded-brand border border-line px-4 py-3">
                                    <x-icon name="map-pin" class="size-4 shrink-0 text-primary" />
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-medium text-ink">{{ $location }}</span>
                                        <span class="block text-xs text-muted">{{ $count }} proyek</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <div class="lg:col-span-7" {!! $ds->reveal(2) !!}>
                <div class="{{ $ds->card('p-6 sm:p-10 lg:sticky lg:top-24', false) }}">
                    <h3 class="heading text-h3">Kirim Pesan</h3>
                    <p class="mt-2 text-sm text-muted">Sampaikan kebutuhan Anda, tim kami akan segera menghubungi.</p>
                    <div class="mt-8">
                        @include('components.company.partials.form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
