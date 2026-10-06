{{-- Professional services hero: soft tinted band, headline + assurance checklist, booking card. --}}
<section id="hero" class="relative overflow-hidden bg-secondary/10">
    <div class="absolute inset-y-0 right-0 hidden w-1/3 bg-[linear-gradient(90deg,transparent,rgba(255,255,255,.6))] lg:block"></div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-6 py-16 lg:grid-cols-12 lg:py-24">
        <div class="lg:col-span-7">
            <p class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase">
                <span class="h-px w-10 bg-secondary"></span>
                {{ $company->established_year ? 'Melayani sejak '.$company->established_year : 'Layanan profesional terpercaya' }}
            </p>
            <h1 class="mt-6 font-heading text-4xl leading-[1.15] text-slate-900 md:text-5xl lg:text-[3.25rem]">
                {{ $section->title ?: ($company->tagline ?: $company->name) }}
            </h1>
            <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-600">
                {{ $section->subtitle ?: $company->description }}
            </p>

            <ul class="mt-8 grid max-w-xl gap-3 sm:grid-cols-2">
                @foreach (['Konsultasi awal bersifat rahasia', 'Ditangani tenaga ahli bersertifikat', 'Biaya transparan sejak awal', 'Pendampingan hingga tuntas'] as $assurance)
                    <li class="flex items-start gap-3 text-sm font-medium text-slate-800">
                        <span class="mt-0.5 inline-flex size-5 shrink-0 items-center justify-center rounded-full bg-primary text-on-primary"><x-icon name="check" class="size-3" stroke="2.5" /></span>
                        {{ $assurance }}
                    </li>
                @endforeach
            </ul>

            <div class="mt-10 flex flex-wrap items-center gap-4">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-primary px-7 py-3.5 text-sm font-semibold text-on-primary transition hover:opacity-90">
                    Jadwalkan Konsultasi <x-icon name="arrow-right" class="size-4" />
                </a>
                <a href="{{ $site->anchor('services') }}" class="inline-flex items-center gap-2 px-2 py-3.5 text-sm font-semibold text-primary underline decoration-secondary decoration-2 underline-offset-8 hover:decoration-primary">
                    Lihat area praktik
                </a>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="relative">
                <div class="absolute -top-3 -right-3 h-full w-full rounded-brand border border-secondary/60"></div>
                <div class="relative overflow-hidden rounded-brand bg-white shadow-xl shadow-slate-900/10">
                    <div class="relative">
                        <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="aspect-[16/9] w-full object-cover" />
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent px-6 pt-10 pb-4">
                            <p class="text-xs font-semibold tracking-[0.2em] text-white/80 uppercase">Janji temu</p>
                            <p class="font-heading text-xl text-white">Konsultasi dengan ahli kami</p>
                        </div>
                    </div>
                    <div class="divide-y divide-slate-100 px-6">
                        <div class="flex items-center gap-4 py-4">
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"><x-icon name="clock" class="size-5" /></span>
                            <div>
                                <p class="text-xs text-slate-500">Jam layanan</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $company->working_hours ?: 'Senin – Jumat, 08.00 – 17.00' }}</p>
                            </div>
                        </div>
                        @if ($company->phone)
                            <div class="flex items-center gap-4 py-4">
                                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"><x-icon name="phone" class="size-5" /></span>
                                <div>
                                    <p class="text-xs text-slate-500">Telepon kantor</p>
                                    <a href="tel:{{ $company->phone }}" class="text-sm font-semibold text-slate-900 hover:text-primary">{{ $company->phone }}</a>
                                </div>
                            </div>
                        @endif
                        @if ($company->city)
                            <div class="flex items-center gap-4 py-4">
                                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary"><x-icon name="map-pin" class="size-5" /></span>
                                <div>
                                    <p class="text-xs text-slate-500">Lokasi kantor</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $company->city }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="grid gap-2 bg-slate-50 p-5 {{ $company->whatsappUrl() ? 'sm:grid-cols-2' : '' }}">
                        <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center justify-center gap-2 rounded-btn bg-primary px-4 py-3 text-sm font-semibold text-on-primary transition hover:opacity-90"><x-icon name="calendar" class="size-4" /> Buat Janji</a>
                        @if ($company->whatsappUrl())
                            <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 rounded-btn bg-[#25D366] px-4 py-3 text-sm font-semibold text-white transition hover:opacity-90"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
