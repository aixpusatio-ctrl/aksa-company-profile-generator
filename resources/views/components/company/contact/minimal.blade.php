{{-- Contact: Minimal — contact details in large type beside an underline form, no cards. --}}
@php($tel = $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null)
<section id="contact" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-14 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-6">
            <div {!! $ds->reveal() !!}>
                {!! $ds->eyebrow('Kontak', $index) !!}
                <h2 class="heading mt-4 text-h2">{{ $section->title ?: 'Mari Berbincang' }}</h2>
                @if ($section->subtitle)<p class="mt-5 max-w-md text-lead text-muted">{{ $section->subtitle }}</p>@endif
            </div>
            <dl class="mt-12 space-y-8">
                @if ($company->email)
                    <div {!! $ds->reveal(1) !!}>
                        <dt class="text-xs tracking-[0.2em] text-muted uppercase">Email</dt>
                        <dd class="mt-2"><a href="mailto:{{ $company->email }}" class="heading bg-[linear-gradient(currentColor,currentColor)] bg-[length:0%_1px] bg-left-bottom bg-no-repeat text-[clamp(1.5rem,3.2vw,2.5rem)] break-all transition-[background-size] duration-500 hover:bg-[length:100%_1px]">{{ $company->email }}</a></dd>
                    </div>
                @endif
                @if ($company->phone)
                    <div {!! $ds->reveal(2) !!}>
                        <dt class="text-xs tracking-[0.2em] text-muted uppercase">Telepon</dt>
                        <dd class="mt-2"><a href="{{ $tel }}" class="heading bg-[linear-gradient(currentColor,currentColor)] bg-[length:0%_1px] bg-left-bottom bg-no-repeat text-[clamp(1.5rem,3.2vw,2.5rem)] transition-[background-size] duration-500 hover:bg-[length:100%_1px]">{{ $company->phone }}</a></dd>
                    </div>
                @endif
                <div class="grid gap-8 sm:grid-cols-2" {!! $ds->reveal(3) !!}>
                    @if ($company->fullAddress())
                        <div>
                            <dt class="text-xs tracking-[0.2em] text-muted uppercase">Alamat</dt>
                            <dd class="mt-2 leading-relaxed text-ink">
                                {{ $company->fullAddress() }}
                                @if ($company->google_maps_url)<a href="{{ $company->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="mt-2 block text-sm text-primary hover:underline">Lihat peta ↗</a>@endif
                            </dd>
                        </div>
                    @endif
                    <div class="space-y-6">
                        @if ($company->working_hours)
                            <div>
                                <dt class="text-xs tracking-[0.2em] text-muted uppercase">Jam Kerja</dt>
                                <dd class="mt-2 text-ink">{{ $company->working_hours }}</dd>
                            </div>
                        @endif
                        @if ($company->whatsappUrl())
                            <div>
                                <dt class="text-xs tracking-[0.2em] text-muted uppercase">WhatsApp</dt>
                                <dd class="mt-2"><a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="text-ink hover:text-primary">{{ $company->whatsapp }} ↗</a></dd>
                            </div>
                        @endif
                    </div>
                </div>
            </dl>
        </div>
        <div class="lg:col-span-6 lg:pt-2" {!! $ds->reveal(2) !!}>
            <p class="mb-6 text-xs tracking-[0.2em] text-muted uppercase">Kirim pesan</p>
            @include('components.company.partials.form', ['variant' => 'underline'])
        </div>
    </div>
</section>
