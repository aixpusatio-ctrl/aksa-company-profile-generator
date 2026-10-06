{{-- CTA: Band — full-width primary band with headline and two actions. --}}
<section id="cta" class="{{ $ds->section('primary', 'overflow-hidden') }}">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_85%_20%,rgba(255,255,255,.18),transparent_45%)]"></div>
    <div class="{{ $ds->container() }} relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
        <div class="max-w-2xl" {!! $ds->reveal() !!}>
            <h2 class="heading text-h2">{{ $section->title ?: 'Siap Memulai Proyek Berikutnya?' }}</h2>
            <p class="mt-4 text-lead text-muted">{{ $section->subtitle ?: 'Diskusikan kebutuhan Anda dengan tim kami dan dapatkan solusi terbaik.' }}</p>
        </div>
        <div class="flex flex-wrap gap-3" {!! $ds->reveal(1) !!}>
            <a href="{{ $site->anchor('contact') }}" class="ds-btn ds-btn-light">Hubungi Kami</a>
            @if ($company->whatsappUrl())
                <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="ds-btn border border-current/30 text-on-primary hover:bg-white/10"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
            @endif
        </div>
    </div>
</section>
