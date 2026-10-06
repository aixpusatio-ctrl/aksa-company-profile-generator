{{-- Professional services CTA: appointment steps band. --}}
<section id="cta" class="bg-primary py-16 lg:py-20">
    <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-secondary uppercase"><span class="h-px w-10 bg-secondary"></span> Janji Temu</p>
            <h2 class="mt-4 font-heading text-3xl leading-tight text-on-primary md:text-4xl">{{ $section->title ?: 'Mulai dengan Konsultasi Awal' }}</h2>
            <p class="mt-4 text-on-primary/75">{{ $section->subtitle ?: 'Ceritakan kebutuhan Anda. Kami akan menjelaskan langkah, waktu, dan estimasi biaya secara jelas sebelum Anda memutuskan.' }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-secondary px-6 py-3.5 text-sm font-semibold text-on-secondary transition hover:opacity-90"><x-icon name="calendar" class="size-4" /> Jadwalkan Konsultasi</a>
                @if ($company->whatsappUrl())
                    <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-btn border border-white/30 px-6 py-3.5 text-sm font-semibold text-on-primary transition hover:bg-white/10"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                @endif
            </div>
        </div>
        <ol class="grid gap-4 sm:grid-cols-3 lg:col-span-7">
            @foreach ([['chat', 'Hubungi kami', 'Sampaikan kebutuhan melalui telepon, WhatsApp, atau formulir.'], ['calendar', 'Atur jadwal', 'Pilih waktu konsultasi tatap muka atau daring.'], ['check-circle', 'Dapatkan solusi', 'Terima rekomendasi dan rencana penanganan tertulis.']] as [$icon, $title, $text])
                <li class="relative rounded-brand border border-white/15 bg-white/5 p-6">
                    <span class="absolute top-5 right-5 font-heading text-3xl text-on-primary/15">{{ $loop->iteration }}</span>
                    <x-icon :name="$icon" class="size-6 text-secondary" />
                    <p class="mt-4 font-heading text-lg text-on-primary">{{ $title }}</p>
                    <p class="mt-2 text-sm text-on-primary/70">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
