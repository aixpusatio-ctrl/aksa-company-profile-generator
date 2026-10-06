{{-- Manufacturing about: company profile with fact sheet, vision/mission, and certification badges. --}}
<section id="about" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-14 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <div class="relative">
                    <x-site.img :src="$company->gallery->first()?->url('image') ?? $company->url('hero_image')" :alt="$company->name" icon="building" class="aspect-[4/5] w-full rounded-brand object-cover" />
                    <div class="absolute -right-3 -bottom-6 left-10 grid grid-cols-2 bg-secondary text-on-secondary sm:-right-6">
                        <div class="border-r border-white/10 p-5">
                            <p class="font-mono text-[10px] tracking-widest uppercase opacity-60">Berdiri</p>
                            <p class="mt-1 font-heading text-3xl font-bold">{{ $company->established_year ?: '—' }}</p>
                        </div>
                        <div class="p-5">
                            <p class="font-mono text-[10px] tracking-widest uppercase opacity-60">Lokasi</p>
                            <p class="mt-1 font-heading text-xl font-bold">{{ $company->city ?: 'Indonesia' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lg:col-span-7 lg:pl-6">
                <p class="font-mono text-xs font-semibold tracking-widest text-primary uppercase">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }} — Profil Perusahaan</p>
                <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight text-slate-900 uppercase md:text-4xl">{{ $section->title ?: 'Tentang '.$company->name }}</h2>
                @if ($section->subtitle)
                    <p class="mt-4 text-lg text-slate-600">{{ $section->subtitle }}</p>
                @endif
                <div class="site-prose mt-6 text-slate-600">{!! $company->about ?: e($company->description) !!}</div>

                @if ($company->vision || $company->missionItems())
                    <div class="mt-10 grid gap-px border border-slate-200 bg-slate-200 md:grid-cols-2">
                        @if ($company->vision)
                            <div class="bg-white p-6">
                                <p class="flex items-center gap-2 font-mono text-xs font-semibold tracking-widest text-primary uppercase"><x-icon name="eye" class="size-4" /> Visi</p>
                                <p class="mt-3 font-heading font-semibold text-slate-900">{{ $company->vision }}</p>
                            </div>
                        @endif
                        @if ($company->missionItems())
                            <div class="bg-white p-6">
                                <p class="flex items-center gap-2 font-mono text-xs font-semibold tracking-widest text-primary uppercase"><x-icon name="rocket" class="size-4" /> Misi</p>
                                <ul class="mt-3 space-y-2 text-sm">
                                    @foreach ($company->missionItems() as $mission)
                                        <li class="flex gap-2"><span class="mt-1.5 size-1.5 shrink-0 bg-primary"></span> {{ $mission }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Certifications / quality badges --}}
        <div class="mt-20 border-y border-slate-200 py-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center">
                <p class="shrink-0 font-mono text-xs font-semibold tracking-widest text-slate-500 uppercase lg:w-48">Sertifikasi &amp;<br class="hidden lg:block"> Standar Mutu</p>
                <div class="grid flex-1 grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    @foreach ([
                        ['award', 'ISO 9001', 'Manajemen Mutu'],
                        ['leaf', 'ISO 14001', 'Lingkungan'],
                        ['shield', 'ISO 45001', 'K3'],
                        ['check-circle', 'SNI', 'Standar Nasional'],
                        ['scale', 'TKDN', 'Konten Lokal'],
                        ['sparkles', 'Halal', 'Bersertifikat'],
                    ] as [$icon, $code, $label])
                        <div class="flex items-center gap-3 border border-slate-200 px-3 py-3">
                            <span class="inline-flex size-9 shrink-0 items-center justify-center bg-primary/10 text-primary"><x-icon :name="$icon" class="size-5" /></span>
                            <div class="min-w-0">
                                <p class="font-heading text-sm font-bold text-slate-900">{{ $code }}</p>
                                <p class="truncate text-[11px] text-slate-500">{{ $label }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @if ($company->valueItems())
                <div class="mt-6 flex flex-wrap gap-2">
                    @foreach ($company->valueItems() as $value)
                        <span class="inline-flex items-center gap-1.5 bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-700"><x-icon name="check" class="size-3.5 text-primary" /> {{ $value }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
