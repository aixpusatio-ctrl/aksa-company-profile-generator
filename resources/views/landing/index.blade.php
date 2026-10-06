@php
    $cta = auth()->check() ? route('websites.create') : route('register');
    $heroTemplate = $templates->first();
@endphp
<x-layouts.marketing :title="setting('default_seo_title')">
    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(60%_50%_at_50%_0%,rgba(59,101,245,.14),transparent),radial-gradient(40%_40%_at_90%_30%,rgba(139,92,246,.12),transparent)]"></div>
        <div class="absolute inset-0 -z-10 bg-[linear-gradient(rgba(15,23,42,.035)_1px,transparent_1px),linear-gradient(90deg,rgba(15,23,42,.035)_1px,transparent_1px)] bg-[size:56px_56px] [mask-image:radial-gradient(ellipse_at_top,black,transparent_70%)]"></div>
        <div class="mx-auto max-w-7xl px-4 pt-16 pb-20 text-center sm:px-6 lg:pt-24">
            <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-white px-4 py-1.5 text-xs font-semibold text-brand-700 shadow-sm">
                <span class="rounded-full bg-brand-600 px-2 py-0.5 text-[10px] text-white">BARU</span> {{ $templateCount }}+ template profesional siap pakai <x-icon name="arrow-right" class="size-3.5" />
            </a>
            <h1 class="mx-auto mt-8 max-w-4xl text-4xl leading-[1.1] font-extrabold tracking-tight sm:text-6xl lg:text-7xl">
                Buat Company Profile Profesional <span class="bg-gradient-to-r from-brand-600 to-violet-600 bg-clip-text text-transparent">Dalam Hitungan Menit.</span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-lg leading-relaxed text-slate-600">
                Pilih template, isi informasi perusahaan, dan website Anda langsung online di subdomain gratis — atau gunakan domain sendiri. Tanpa coding, tanpa ribet.
            </p>
            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <a href="{{ $cta }}" class="btn btn-primary btn-lg shadow-lg shadow-brand-600/30">Buat Company Profile <x-icon name="arrow-right" class="size-4" /></a>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary btn-lg">Lihat Template</a>
            </div>
            <p class="mt-4 text-xs text-slate-500">Gratis {{ config('platform.trial_days') }} hari paket Professional · Tanpa kartu kredit</p>

            @if ($heroTemplate)
                <div class="relative mx-auto mt-16 max-w-5xl">
                    <div class="absolute -inset-x-10 -top-10 -bottom-10 -z-10 rounded-[3rem] bg-gradient-to-tr from-brand-500/20 via-violet-500/10 to-transparent blur-2xl"></div>
                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/15">
                        <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50 px-4 py-3">
                            <span class="size-3 rounded-full bg-rose-400"></span><span class="size-3 rounded-full bg-amber-400"></span><span class="size-3 rounded-full bg-emerald-400"></span>
                            <span class="mx-auto flex items-center gap-1.5 rounded-md bg-white px-4 py-1 text-xs text-slate-500 ring-1 ring-slate-200"><x-icon name="lock" class="size-3" /> perusahaan-anda.{{ config('platform.domain') }}</span>
                        </div>
                        <x-template-thumb :template="$heroTemplate" live aspect="aspect-[16/9]" />
                    </div>
                    <div class="absolute -bottom-6 -left-6 hidden rounded-xl bg-white p-4 text-left shadow-xl ring-1 ring-slate-200 md:block">
                        <p class="flex items-center gap-2 text-sm font-bold text-slate-900"><span class="inline-flex size-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><x-icon name="check" class="size-4" /></span> Website dipublikasikan</p>
                        <p class="mt-1 text-xs text-slate-500">pt-maju-jaya.{{ config('platform.domain') }}</p>
                    </div>
                    <div class="absolute -right-6 -top-6 hidden rounded-xl bg-white p-4 text-left shadow-xl ring-1 ring-slate-200 md:block">
                        <p class="text-xs text-slate-500">Responsive</p>
                        <p class="flex items-center gap-1.5 font-display text-sm font-extrabold text-slate-900"><x-icon name="device-mobile" class="size-4 text-brand-600" /> Mobile & desktop</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Logos / trust --}}
    <section class="border-y border-slate-100 bg-slate-50/60 py-10">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-center gap-x-12 gap-y-4 px-4 text-sm font-bold tracking-wider text-slate-400 uppercase">
            <span>Kontraktor</span><span>Manufaktur</span><span>Konsultan</span><span>Software House</span><span>Firma Hukum</span><span>Agensi Kreatif</span>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="scroll-mt-20 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-bold tracking-widest text-brand-600 uppercase">Features</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Semua yang dibutuhkan website perusahaan</h2>
                <p class="mt-4 text-slate-600">Dirancang untuk pengguna non-teknis, dengan arsitektur yang siap untuk skala SaaS.</p>
            </div>
            <div class="mt-16 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                    ['template', 'Template Profesional', 'Desain berbeda untuk setiap industri — layout, tipografi, hero hingga footer, bukan sekadar ganti warna.'],
                    ['sparkles', 'Wizard Langkah demi Langkah', 'Isi data perusahaan melalui wizard 11 langkah dengan autosave. Bisa kembali kapan saja.'],
                    ['squares', 'Section Builder', 'Aktifkan, nonaktifkan dan atur urutan section dengan drag & drop.'],
                    ['list', 'Menu & Submenu Builder', 'Susun navigasi bertingkat: anchor section, halaman, atau URL eksternal.'],
                    ['document', 'Halaman Tanpa Batas', 'Karir, berita, kebijakan privasi — buat halaman kustom dengan rich text editor.'],
                    ['globe', 'Subdomain & Custom Domain', 'Langsung online di subdomain gratis, lalu hubungkan domain sendiri dengan panduan DNS.'],
                    ['paint', 'Kustomisasi Brand', 'Warna, font, gaya tombol, radius, logo dan favicon sesuai identitas perusahaan.'],
                    ['search', 'SEO Siap Pakai', 'Meta tag, Open Graph, Twitter Card, sitemap.xml, robots.txt dan canonical URL otomatis.'],
                    ['chart', 'Analytics & Pesan', 'Pantau kunjungan dan terima pesan dari contact form langsung di dashboard.'],
                ] as [$icon, $title, $text])
                    <div class="group rounded-2xl border border-slate-200 bg-white p-7 transition hover:-translate-y-1 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-600/5">
                        <span class="inline-flex size-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white"><x-icon :name="$icon" class="size-6" /></span>
                        <h3 class="mt-5 text-lg font-bold">{{ $title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $text }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Templates --}}
    <section id="templates" class="scroll-mt-20 bg-slate-950 py-24 text-slate-300">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold tracking-widest text-brand-400 uppercase">Templates</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Template untuk setiap industri</h2>
                    <p class="mt-4 text-slate-400">Setiap template memiliki struktur, tipografi dan gaya yang berbeda. Branding tetap bisa disesuaikan.</p>
                </div>
                <a href="{{ route('templates.index') }}" class="btn btn-secondary">Semua template <x-icon name="arrow-right" class="size-4" /></a>
            </div>
            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($templates as $template)
                    <a href="{{ route('templates.show', $template) }}" class="group overflow-hidden rounded-2xl border border-white/10 bg-white/5 transition hover:border-white/25">
                        <x-template-thumb :template="$template" class="transition duration-500 group-hover:opacity-90" />
                        <div class="flex items-center justify-between p-5">
                            <div>
                                <h3 class="font-bold text-white">{{ $template->name }}</h3>
                                <p class="text-xs text-slate-400">{{ $template->category?->name }}</p>
                            </div>
                            <span class="inline-flex size-9 items-center justify-center rounded-full bg-white/10 text-white transition group-hover:bg-brand-600"><x-icon name="arrow-up-right" class="size-4" /></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section id="how-it-works" class="py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-bold tracking-widest text-brand-600 uppercase">How It Works</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Online dalam 4 langkah</h2>
            </div>
            <ol class="mt-16 grid gap-8 md:grid-cols-4">
                @foreach ([
                    ['Daftar akun', 'Buat akun gratis dalam 30 detik.'],
                    ['Pilih template', 'Pilih desain yang sesuai industri Anda.'],
                    ['Isi informasi', 'Lengkapi profil, layanan, produk, tim & portofolio.'],
                    ['Publish', 'Website live di subdomain atau domain Anda.'],
                ] as $i => [$title, $text])
                    <li class="relative">
                        @if (! $loop->last)
                            <div class="absolute top-6 left-14 hidden h-px w-[calc(100%-3rem)] bg-gradient-to-r from-brand-300 to-transparent md:block"></div>
                        @endif
                        <span class="inline-flex size-12 items-center justify-center rounded-2xl bg-brand-600 font-display text-lg font-bold text-white shadow-lg shadow-brand-600/30">{{ $i + 1 }}</span>
                        <h3 class="mt-5 text-lg font-bold">{{ $title }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $text }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Example websites --}}
    @if ($examples->isNotEmpty())
        <section id="examples" class="bg-slate-50 py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="text-sm font-bold tracking-widest text-brand-600 uppercase">Example Websites</p>
                    <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Dibuat dengan {{ app_name() }}</h2>
                </div>
                <div class="mt-12 grid gap-6 md:grid-cols-3">
                    @foreach ($examples as $example)
                        <a href="{{ $example->publicUrl() }}" target="_blank" rel="noopener" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:shadow-xl">
                            <x-template-thumb :src="$example->subdomainUrl()" />
                            <div class="p-5">
                                <h3 class="font-bold text-slate-900">{{ $example->name }}</h3>
                                <p class="mt-1 flex items-center gap-1.5 text-xs text-slate-500"><x-icon name="globe" class="size-3.5" /> {{ $example->primaryHost() }}</p>
                                <p class="mt-3 text-xs font-medium text-brand-600">Template: {{ $example->template?->name }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Pricing --}}
    <section id="pricing" class="scroll-mt-20 py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6">
            <div class="mx-auto max-w-2xl text-center">
                <p class="text-sm font-bold tracking-widest text-brand-600 uppercase">Pricing</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Harga sederhana, tanpa biaya tersembunyi</h2>
            </div>
            <div class="mx-auto mt-14 grid max-w-5xl gap-6 lg:grid-cols-3">
                @foreach ($plans as $key => $plan)
                    @php($featured = $key === 'pro')
                    <div class="relative flex flex-col rounded-2xl p-8 {{ $featured ? 'bg-slate-950 text-slate-300 shadow-2xl ring-1 ring-slate-900 lg:-my-4 lg:py-12' : 'border border-slate-200 bg-white' }}">
                        @if ($featured)
                            <span class="absolute -top-3 left-8 rounded-full bg-gradient-to-r from-brand-500 to-violet-500 px-3 py-1 text-xs font-bold text-white">Paling populer</span>
                        @endif
                        <h3 class="text-lg font-bold {{ $featured ? 'text-white' : '' }}">{{ $plan['name'] }}</h3>
                        <p class="mt-1 text-sm {{ $featured ? 'text-slate-400' : 'text-slate-500' }}">{{ $plan['description'] }}</p>
                        <p class="mt-6 flex items-baseline gap-1">
                            <span class="font-display text-4xl font-extrabold {{ $featured ? 'text-white' : 'text-slate-900' }}">{{ $plan['price'] ? 'Rp '.number_format($plan['price'] / 1000, 0).'rb' : 'Gratis' }}</span>
                            @if ($plan['price'])<span class="text-sm">/bulan</span>@endif
                        </p>
                        <ul class="mt-8 flex-1 space-y-3 text-sm">
                            @foreach ($plan['features'] as $feature)
                                <li class="flex gap-2.5"><x-icon name="check" class="size-5 shrink-0 {{ $featured ? 'text-brand-400' : 'text-brand-600' }}" /> {{ $feature }}</li>
                            @endforeach
                        </ul>
                        <a href="{{ $cta }}" class="btn mt-8 {{ $featured ? 'btn-primary' : 'btn-secondary' }}">Mulai sekarang</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="scroll-mt-20 bg-slate-50 py-24">
        <div class="mx-auto max-w-3xl px-4 sm:px-6">
            <div class="text-center">
                <p class="text-sm font-bold tracking-widest text-brand-600 uppercase">FAQ</p>
                <h2 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">Pertanyaan yang sering diajukan</h2>
            </div>
            <div class="mt-12 space-y-3" x-data="{ active: 0 }">
                @foreach ([
                    ['Apakah saya perlu kemampuan coding?', 'Tidak sama sekali. Semua diatur lewat wizard dan form yang mudah: pilih template, isi data, lalu publish.'],
                    ['Bisakah saya memakai domain sendiri?', 'Bisa. Tambahkan domain di menu Domain, ikuti instruksi DNS (CNAME/A record), lalu klik Verify. Website juga tetap bisa diakses melalui subdomain.'],
                    ['Apakah template bisa diganti setelah website jadi?', 'Bisa kapan saja. Data perusahaan Anda tidak hilang karena website dirender ulang dari data, bukan HTML statis.'],
                    ['Apakah website saya SEO friendly?', 'Ya. Setiap website memiliki meta tag, Open Graph, Twitter Card, sitemap.xml, robots.txt dan canonical URL yang bisa Anda atur.'],
                    ['Bagaimana pesan dari pengunjung diterima?', 'Pesan dari contact form tersimpan di dashboard dan Anda mendapatkan notifikasi setiap ada pesan baru.'],
                ] as $i => [$q, $a])
                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
                        <button type="button" class="flex w-full items-center justify-between gap-4 px-6 py-4 text-left font-semibold text-slate-900" @click="active = active === {{ $i }} ? null : {{ $i }}">
                            {{ $q }}
                            <x-icon name="chevron-down" class="size-5 shrink-0 text-slate-400 transition" ::class="active === {{ $i }} && 'rotate-180'" />
                        </button>
                        <div x-show="active === {{ $i }}" x-collapse><p class="px-6 pb-5 text-sm leading-relaxed text-slate-600">{{ $a }}</p></div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-24">
        <div class="mx-auto max-w-5xl px-4 sm:px-6">
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 via-brand-700 to-violet-700 px-8 py-16 text-center shadow-2xl shadow-brand-700/30">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_0%,rgba(255,255,255,.2),transparent_50%)]"></div>
                <h2 class="relative text-3xl font-extrabold tracking-tight text-white sm:text-4xl">Siap tampil profesional secara online?</h2>
                <p class="relative mx-auto mt-4 max-w-xl text-brand-100">Bergabung dan buat company profile pertama Anda hari ini. Gratis untuk memulai.</p>
                <a href="{{ $cta }}" class="btn btn-lg relative mt-8 bg-white text-brand-700 hover:bg-brand-50">Buat Company Profile <x-icon name="arrow-right" class="size-4" /></a>
            </div>
        </div>
    </section>
</x-layouts.marketing>
