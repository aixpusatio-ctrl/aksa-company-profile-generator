{{-- Construction CTA: full-width primary band, huge uppercase headline, black block button, hazard stripes. --}}
<section id="cta" class="relative overflow-hidden bg-primary text-on-primary">
    <div class="absolute inset-y-0 right-0 hidden w-1/3 bg-[repeating-linear-gradient(-45deg,rgb(0_0_0/0.12)_0_18px,transparent_18px_36px)] lg:block"></div>
    <div class="relative mx-auto flex max-w-7xl flex-col items-start justify-between gap-10 px-5 py-16 sm:px-6 lg:flex-row lg:items-center lg:py-24">
        <div class="max-w-3xl">
            <h2 class="font-heading text-4xl leading-[0.92] font-bold tracking-tight uppercase sm:text-6xl lg:text-7xl">{{ $section->title ?: 'Siap Membangun Proyek Anda?' }}</h2>
            <p class="mt-5 max-w-xl text-base font-medium opacity-80 sm:text-lg">{{ $section->subtitle ?: 'Dapatkan estimasi biaya dan jadwal pengerjaan gratis dari tim estimator kami.' }}</p>
        </div>
        <div class="flex shrink-0 flex-col gap-3 sm:flex-row lg:flex-col">
            <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center justify-between gap-6 rounded-btn bg-stone-950 px-8 py-5 font-heading text-sm font-bold tracking-widest text-white uppercase transition hover:bg-stone-800">Minta Penawaran <x-icon name="arrow-right" class="size-5 text-primary" /></a>
            @if ($company->phone)
                <a href="tel:{{ $company->phone }}" class="inline-flex items-center justify-between gap-6 rounded-btn border-2 border-current px-8 py-[1.125rem] font-heading text-sm font-bold tracking-widest uppercase transition hover:bg-black/10"><span class="flex items-center gap-3"><x-icon name="phone" class="size-5" /> {{ $company->phone }}</span></a>
            @endif
        </div>
    </div>
</section>
