{{-- Manufacturing CTA: full-width primary band with checklist and RFQ button. --}}
<section id="cta" class="relative overflow-hidden bg-primary text-on-primary">
    <div class="absolute inset-0 bg-[repeating-linear-gradient(135deg,transparent_0_22px,rgb(0_0_0/0.06)_22px_24px)]"></div>
    <div class="relative mx-auto grid max-w-7xl items-center gap-10 px-6 py-16 lg:grid-cols-12 lg:py-20">
        <div class="lg:col-span-7">
            <p class="font-mono text-xs font-semibold tracking-widest uppercase opacity-80">// Request for Quotation</p>
            <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight uppercase md:text-4xl">{{ $section->title ?: 'Siap Memproduksi Kebutuhan Anda' }}</h2>
            <p class="mt-4 max-w-xl opacity-85">{{ $section->subtitle ?: 'Kirimkan spesifikasi, volume, dan jadwal — tim kami akan menyiapkan penawaran teknis dan harga terbaik.' }}</p>
        </div>
        <div class="lg:col-span-5">
            <ul class="grid gap-2 text-sm font-semibold sm:grid-cols-2">
                @foreach (['Sampel & prototipe', 'MOQ fleksibel', 'Lead time terjadwal', 'Dokumen QC lengkap'] as $point)
                    <li class="flex items-center gap-2"><x-icon name="check-circle" class="size-4" /> {{ $point }}</li>
                @endforeach
            </ul>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-secondary px-7 py-4 text-xs font-bold tracking-wider text-on-secondary uppercase transition hover:opacity-90">Request Quote <x-icon name="arrow-right" class="size-4" /></a>
                @if ($company->whatsappUrl())
                    <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-btn border-2 border-current px-7 py-3.5 text-xs font-bold tracking-wider uppercase transition hover:bg-black/10"><x-icon name="whatsapp" class="size-4" /> WhatsApp Sales</a>
                @endif
            </div>
        </div>
    </div>
</section>
