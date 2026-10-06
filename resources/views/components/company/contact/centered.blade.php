{{-- Contact: Centered — centered heading, narrow form card and a row of contact chips. --}}
@php
    $chips = collect([
        ['phone', $company->phone, $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null],
        ['mail', $company->email, $company->email ? 'mailto:'.$company->email : null],
        ['whatsapp', $company->whatsapp, $company->whatsappUrl()],
        ['map-pin', $company->city ?: $company->fullAddress(), $company->google_maps_url],
        ['clock', $company->working_hours, null],
    ])->filter(fn ($c) => filled($c[1]));
@endphp
<section id="contact" class="{{ $ds->section($tone, 'overflow-hidden') }}">
    <div class="pointer-events-none absolute top-0 left-1/2 h-80 w-[48rem] max-w-full -translate-x-1/2 rounded-full bg-primary/10 blur-3xl"></div>
    <div class="{{ $ds->container() }} relative">
        @include('components.company.partials.heading', ['eyebrow' => 'Kontak', 'title' => $section->title ?: 'Ada yang Bisa Kami Bantu?', 'subtitle' => $section->subtitle ?: 'Kirimkan pesan Anda dan tim kami akan merespons dalam 1x24 jam kerja.', 'align' => 'center', 'number' => $index])

        <div class="{{ $ds->card('mx-auto mt-12 max-w-2xl p-6 sm:p-10', false) }}" {!! $ds->reveal(1) !!}>
            @include('components.company.partials.form')
        </div>

        @if ($chips->isNotEmpty())
            <ul class="mx-auto mt-10 flex max-w-4xl flex-wrap justify-center gap-2.5" {!! $ds->reveal(2) !!}>
                @foreach ($chips as [$icon, $value, $href])
                    <li class="max-w-full">
                        @if ($href)
                            <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="inline-flex min-h-11 max-w-full items-center gap-2 rounded-full border border-line bg-card px-4 py-2 text-sm text-ink transition hover:border-primary hover:text-primary"><x-icon :name="$icon" class="size-4 shrink-0 text-primary" /><span class="truncate">{{ $value }}</span></a>
                        @else
                            <span class="inline-flex min-h-11 max-w-full items-center gap-2 rounded-full border border-line bg-card px-4 py-2 text-sm text-ink"><x-icon :name="$icon" class="size-4 shrink-0 text-primary" /><span class="truncate">{{ $value }}</span></span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
