{{-- Technology CTA: grid panel with glow and command-style button. --}}
<section id="cta" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="relative overflow-hidden rounded-brand border border-white/10 bg-slate-900/60 px-6 py-16 text-center sm:px-12 lg:py-24">
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgb(255_255_255/0.05)_1px,transparent_1px),linear-gradient(to_bottom,rgb(255_255_255/0.05)_1px,transparent_1px)] bg-[size:40px_40px] [mask-image:radial-gradient(ellipse_at_center,black,transparent_70%)]"></div>
            <div class="absolute top-full left-1/2 h-80 w-[44rem] max-w-full -translate-x-1/2 -translate-y-1/2 rounded-full bg-primary/40 blur-[100px]"></div>
            <div class="relative">
                <p class="font-mono text-sm text-primary">// siap_deploy?</p>
                <h2 class="mx-auto mt-5 max-w-3xl font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Bangun produk digital Anda bersama kami' }}</h2>
                <p class="mx-auto mt-5 max-w-xl text-slate-400">{{ $section->subtitle ?: 'Ceritakan tantangan Anda, kami bantu rancang solusi dan peta jalannya.' }}</p>
                <div class="mt-10 flex flex-wrap justify-center gap-3">
                    <a href="{{ $site->anchor('contact') }}" class="inline-flex items-center gap-2 rounded-btn bg-primary px-6 py-3.5 text-sm font-semibold text-on-primary shadow-[0_0_40px_-8px_var(--brand-primary)] transition hover:shadow-[0_0_50px_-4px_var(--brand-primary)]">Mulai Diskusi <x-icon name="arrow-right" class="size-4" /></a>
                    @if ($company->whatsappUrl())
                        <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 rounded-btn border border-white/15 bg-slate-950/60 px-6 py-3.5 font-mono text-sm text-slate-200 transition hover:border-white/30"><span class="text-primary">$</span> chat --whatsapp</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
