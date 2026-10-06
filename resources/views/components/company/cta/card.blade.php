{{-- CTA: Card — contained card with a gradient hairline border and soft primary glow, headline, actions and contact line. --}}
@php($phone = $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null)
<section id="cta" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="rounded-[calc(var(--brand-radius)*1.5+1px)] bg-gradient-to-br from-primary/60 via-line to-secondary/50 p-px shadow-[0_30px_80px_-40px_color-mix(in_oklab,var(--brand-primary)_55%,transparent)]" {!! $ds->reveal() !!}>
            <div class="relative isolate overflow-hidden rounded-[calc(var(--brand-radius)*1.5)] bg-card px-6 py-14 text-center sm:px-12 sm:py-20">
                <div class="absolute inset-0 -z-10 bg-primary/[0.04]"></div>
                <div class="absolute -top-24 left-1/2 -z-10 h-56 w-[36rem] max-w-full -translate-x-1/2 rounded-full bg-primary/20 blur-3xl"></div>
                {!! $ds->eyebrow('Mulai Sekarang', $index) !!}
                <h2 class="heading mx-auto mt-4 max-w-3xl text-h2">{{ $section->title ?: 'Siap Mewujudkan Proyek Anda Bersama Kami?' }}</h2>
                <p class="mx-auto mt-5 max-w-2xl text-lead text-muted">{{ $section->subtitle ?: 'Ceritakan kebutuhan Anda — tim '.$company->name.' siap memberikan solusi yang tepat.' }}</p>
                <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                    @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'primary'])
                    @if ($company->whatsappUrl())
                        @include('components.company.partials.button', ['href' => $company->whatsappUrl(), 'label' => 'Chat WhatsApp', 'kind' => 'secondary', 'icon' => 'whatsapp', 'external' => true])
                    @endif
                </div>
                @if ($company->phone || $company->email)
                    <p class="mt-8 flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-sm text-muted">
                        @if ($company->phone)<a href="{{ $phone }}" class="inline-flex items-center gap-2 hover:text-primary"><x-icon name="phone" class="size-4" />{{ $company->phone }}</a>@endif
                        @if ($company->email)<a href="mailto:{{ $company->email }}" class="inline-flex items-center gap-2 break-all hover:text-primary"><x-icon name="mail" class="size-4" />{{ $company->email }}</a>@endif
                    </p>
                @endif
            </div>
        </div>
    </div>
</section>
