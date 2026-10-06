{{-- Creative CTA: giant playful headline on primary with spinning sticker. --}}
<section id="cta" class="px-3 py-20 sm:px-5 lg:py-28">
    <div class="relative mx-auto max-w-[90rem] overflow-hidden rounded-[2rem] bg-primary px-6 py-20 text-center text-on-primary sm:px-10 lg:py-28">
        <span class="absolute top-8 left-8 hidden size-28 -rotate-12 items-center justify-center rounded-full bg-neutral-950 text-xs font-extrabold tracking-widest text-white uppercase md:inline-flex">Gratis<br>ngopi ☕</span>
        <span class="absolute right-10 bottom-10 hidden rotate-12 rounded-2xl bg-white px-4 py-2 font-heading font-extrabold text-neutral-950 shadow-xl md:inline-block">Respon &lt; 24 jam ⚡</span>
        <h2 class="mx-auto max-w-5xl font-heading text-5xl leading-[0.9] font-extrabold tracking-tighter md:text-7xl lg:text-8xl">{{ $section->title ?: 'Punya proyek gila? Kami siap.' }}</h2>
        <p class="mx-auto mt-6 max-w-xl text-lg opacity-85">{{ $section->subtitle ?: 'Ceritakan ide Anda — sekecil atau seliar apa pun. Kita wujudkan bersama.' }}</p>
        <div class="mt-10 flex flex-wrap justify-center gap-3">
            <a href="{{ $site->anchor('contact') }}" class="group inline-flex items-center gap-3 rounded-btn bg-neutral-950 py-3 pr-3 pl-7 text-lg font-bold text-white transition hover:scale-105">
                Mulai proyek <span class="inline-flex size-10 items-center justify-center rounded-full bg-white text-neutral-950 transition group-hover:rotate-45"><x-icon name="arrow-up-right" class="size-5" /></span>
            </a>
            @if ($company->whatsappUrl())
                <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-btn border-2 border-current px-7 py-3 text-lg font-bold transition hover:-rotate-2"><x-icon name="whatsapp" class="size-5" /> WhatsApp</a>
            @endif
        </div>
    </div>
</section>
