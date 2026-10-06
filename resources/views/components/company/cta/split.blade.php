{{-- CTA: Split — image panel beside a primary-colored panel with headline and actions. --}}
@php($image = $company->gallery->first()?->url('image') ?? $company->url('hero_image'))
<section id="cta" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="grid overflow-hidden rounded-brand lg:grid-cols-2" {!! $ds->reveal() !!}>
            <div class="relative min-h-64 sm:min-h-80">
                <x-site.img :src="$image" :alt="$company->name" icon="building" class="absolute inset-0 size-full object-cover" data-parallax="0.04" />
            </div>
            <div class="tone-primary relative overflow-hidden bg-primary px-6 py-12 text-on-primary sm:px-12 sm:py-16 lg:px-14 lg:py-20">
                <div class="absolute -right-24 -bottom-24 size-72 rounded-full border border-current opacity-15"></div>
                <div class="absolute -right-10 -bottom-10 size-44 rounded-full border border-current opacity-15"></div>
                <div class="relative">
                    <p class="text-xs font-semibold tracking-[0.22em] uppercase opacity-75">Mari Berkolaborasi</p>
                    <h2 class="heading mt-4 text-h2">{{ $section->title ?: 'Wujudkan Rencana Anda Bersama Kami' }}</h2>
                    <p class="mt-5 max-w-lg text-lead text-muted">{{ $section->subtitle ?: 'Konsultasikan kebutuhan Anda dan temukan solusi yang paling tepat bersama tim kami.' }}</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                        <a href="{{ $site->anchor('contact') }}" class="ds-btn ds-btn-light">Hubungi Kami <x-icon name="arrow-right" class="size-4" /></a>
                        @if ($company->whatsappUrl())
                            <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="ds-btn border border-current/30 text-on-primary hover:bg-white/10"><x-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                        @endif
                    </div>
                    @if ($company->phone || $company->email)
                        <p class="mt-8 flex flex-wrap gap-x-5 gap-y-1 border-t border-line pt-6 text-sm text-muted">
                            @if ($company->phone)<span>{{ $company->phone }}</span>@endif
                            @if ($company->email)<span class="break-all">{{ $company->email }}</span>@endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
