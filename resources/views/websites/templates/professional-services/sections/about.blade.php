{{-- Professional services about: long-form profile with a sticky office info card. --}}
<section id="about" class="border-y border-slate-200 bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto grid max-w-7xl gap-12 px-6 lg:grid-cols-12">
        <div class="lg:col-span-8">
            <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-10 bg-secondary"></span> Tentang Kami</p>
            <h2 class="mt-4 font-heading text-3xl leading-tight text-slate-900 md:text-4xl">{{ $section->title ?: 'Integritas, Ketelitian, dan Kepercayaan' }}</h2>
            @if ($section->subtitle)
                <p class="mt-4 text-lg text-slate-600">{{ $section->subtitle }}</p>
            @endif

            <x-site.img :src="$company->gallery->first()?->url('image') ?? $company->url('hero_image')" :alt="$company->name" icon="building" class="mt-10 aspect-[21/9] w-full rounded-brand object-cover" />

            <div class="site-prose mt-10 text-slate-700">{!! $company->about ?: e($company->description) !!}</div>

            @if ($company->vision || $company->missionItems())
                <div class="mt-12 grid gap-6 md:grid-cols-2">
                    @if ($company->vision)
                        <div class="rounded-brand bg-white p-7 ring-1 ring-slate-200">
                            <span class="inline-flex size-10 items-center justify-center rounded-full bg-secondary/15 text-primary"><x-icon name="eye" class="size-5" /></span>
                            <p class="mt-4 text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">Visi</p>
                            <p class="mt-2 font-heading text-lg leading-snug text-slate-900">{{ $company->vision }}</p>
                        </div>
                    @endif
                    @if ($company->missionItems())
                        <div class="rounded-brand bg-white p-7 ring-1 ring-slate-200">
                            <span class="inline-flex size-10 items-center justify-center rounded-full bg-secondary/15 text-primary"><x-icon name="scale" class="size-5" /></span>
                            <p class="mt-4 text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">Misi</p>
                            <ol class="mt-3 space-y-2.5 text-sm text-slate-700">
                                @foreach ($company->missionItems() as $mission)
                                    <li class="flex gap-3"><span class="font-heading text-primary">{{ $loop->iteration }}.</span> <span>{{ $mission }}</span></li>
                                @endforeach
                            </ol>
                        </div>
                    @endif
                </div>
            @endif

            @if ($company->valueItems())
                <div class="mt-12">
                    <p class="text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">Nilai yang kami pegang</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        @foreach ($company->valueItems() as $value)
                            <span class="inline-flex items-center gap-2 rounded-full border border-slate-300 bg-white px-4 py-2 text-sm text-slate-700"><x-icon name="shield" class="size-4 text-primary" /> {{ $value }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($company->history)
                <div class="mt-12 border-l-2 border-secondary pl-6">
                    <p class="text-xs font-semibold tracking-[0.2em] text-slate-500 uppercase">Sejarah singkat</p>
                    <div class="site-prose mt-3 text-slate-700">{!! $company->history !!}</div>
                </div>
            @endif
        </div>

        <aside class="lg:col-span-4">
            <div class="overflow-hidden rounded-brand bg-white shadow-lg shadow-slate-900/5 ring-1 ring-slate-200 lg:sticky lg:top-28">
                <div class="bg-primary p-7 text-on-primary">
                    <p class="text-xs font-semibold tracking-[0.2em] text-on-primary/70 uppercase">Berdiri sejak</p>
                    <p class="mt-1 font-heading text-5xl">{{ $company->established_year ?: '—' }}</p>
                    @if ($company->established_year)
                        <p class="mt-2 text-sm text-on-primary/75">{{ date('Y') - $company->established_year }}+ tahun pengalaman melayani klien</p>
                    @endif
                </div>
                <dl class="divide-y divide-slate-100 px-7">
                    @if ($company->fullAddress())
                        <div class="flex gap-4 py-5">
                            <x-icon name="map-pin" class="mt-0.5 size-5 shrink-0 text-secondary" />
                            <div><dt class="text-xs font-semibold text-slate-500 uppercase">Alamat</dt><dd class="mt-1 text-sm text-slate-800">{{ $company->fullAddress() }}</dd></div>
                        </div>
                    @endif
                    <div class="flex gap-4 py-5">
                        <x-icon name="clock" class="mt-0.5 size-5 shrink-0 text-secondary" />
                        <div><dt class="text-xs font-semibold text-slate-500 uppercase">Jam Kerja</dt><dd class="mt-1 text-sm text-slate-800">{{ $company->working_hours ?: 'Senin – Jumat, 08.00 – 17.00' }}</dd></div>
                    </div>
                    @if ($company->email)
                        <div class="flex gap-4 py-5">
                            <x-icon name="mail" class="mt-0.5 size-5 shrink-0 text-secondary" />
                            <div class="min-w-0"><dt class="text-xs font-semibold text-slate-500 uppercase">Email</dt><dd class="mt-1 text-sm break-all text-slate-800"><a href="mailto:{{ $company->email }}" class="hover:text-primary">{{ $company->email }}</a></dd></div>
                        </div>
                    @endif
                    <div class="grid grid-cols-2 gap-4 py-5 text-center">
                        <div><dd class="font-heading text-2xl text-slate-900">{{ $company->team->count() ?: '—' }}</dd><dt class="text-xs text-slate-500">Tenaga ahli</dt></div>
                        <div><dd class="font-heading text-2xl text-slate-900">{{ $company->services->count() ?: '—' }}</dd><dt class="text-xs text-slate-500">Area praktik</dt></div>
                    </div>
                </dl>
                <div class="p-7 pt-0">
                    <a href="{{ $site->anchor('contact') }}" class="flex items-center justify-center gap-2 rounded-btn bg-primary px-5 py-3 text-sm font-semibold text-on-primary transition hover:opacity-90"><x-icon name="calendar" class="size-4" /> Jadwalkan Konsultasi</a>
                </div>
            </div>
        </aside>
    </div>
</section>
