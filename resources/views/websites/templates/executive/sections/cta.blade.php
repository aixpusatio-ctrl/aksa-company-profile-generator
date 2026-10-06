{{-- Executive CTA: image backdrop inside a gold double frame. --}}
<section id="cta" class="relative isolate overflow-hidden bg-secondary py-28 lg:py-40">
    <x-site.img :src="$company->gallery->get(1)?->url('image') ?? $company->url('hero_image')" alt="" class="absolute inset-0 -z-20 size-full object-cover" />
    <div class="absolute inset-0 -z-10 bg-secondary/85"></div>
    <div class="mx-auto max-w-5xl px-6">
        <div class="border border-primary/40 p-2">
            <div class="border border-primary/20 px-6 py-16 text-center md:px-16 md:py-20">
                <x-icon name="award" class="mx-auto size-10 text-primary" stroke="1" />
                <h2 class="mt-8 font-heading text-4xl leading-[1.1] font-medium text-on-secondary md:text-6xl">{{ $section->title ?: 'Percayakan Masa Depan Anda' }}</h2>
                <p class="mx-auto mt-6 max-w-2xl text-on-secondary/70">{{ $section->subtitle ?: 'Pertemuan pribadi dengan mitra senior kami — sepenuhnya rahasia, tanpa kewajiban.' }}</p>
                <div class="mt-12 flex flex-col justify-center gap-4 sm:flex-row">
                    <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center justify-center rounded-btn bg-primary px-9 py-4 text-[11px] font-semibold tracking-[0.3em] text-on-primary uppercase transition hover:opacity-90">Jadwalkan Pertemuan</a>
                    @if ($company->whatsappUrl())
                        <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-3 rounded-btn border border-on-secondary/30 px-9 py-4 text-[11px] font-semibold tracking-[0.3em] text-on-secondary uppercase transition hover:border-primary hover:text-primary"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
