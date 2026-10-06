{{-- Contact: Cards — row of contact info cards above a wide form card with map. --}}
@php
    $info = collect([
        ['map-pin', 'Alamat', $company->fullAddress(), $company->google_maps_url],
        ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null],
        ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
        ['clock', 'Jam Kerja', $company->working_hours, null],
    ])->filter(fn ($row) => filled($row[2]))->values();
    $cols = [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3'][$info->count()] ?? 'lg:grid-cols-4';
@endphp
<section id="contact" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Kontak', 'title' => $section->title ?: 'Hubungi Kami', 'subtitle' => $section->subtitle ?: 'Tim kami siap membantu. Kami akan merespons dalam 1x24 jam kerja.', 'number' => $index])

        @if ($info->isNotEmpty())
            <div class="mt-12 grid gap-4 sm:grid-cols-2 {{ $cols }}">
                @foreach ($info as [$icon, $label, $value, $href])
                    <div class="{{ $ds->card('grid grid-cols-[auto_1fr] items-start gap-x-4 p-5 sm:flex sm:flex-col sm:p-6') }}" {!! $ds->reveal($loop->index) !!}>
                        <span class="row-span-2 inline-flex size-11 items-center justify-center rounded-brand bg-primary/10 text-primary"><x-icon :name="$icon" class="size-5" /></span>
                        <span class="text-xs font-semibold tracking-[0.16em] text-muted uppercase sm:mt-5">{{ $label }}</span>
                        @if ($href)
                            <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="mt-1 min-w-0 font-medium break-words text-ink hover:text-primary sm:mt-1.5">{{ $value }}</a>
                        @else
                            <span class="mt-1 min-w-0 font-medium break-words text-ink sm:mt-1.5">{{ $value }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="{{ $ds->card('mt-6 grid overflow-hidden lg:grid-cols-12', false) }}" {!! $ds->reveal(2) !!}>
            <div class="p-6 sm:p-10 {{ $company->mapEmbedUrl() ? 'lg:col-span-7' : 'lg:col-span-12' }}">
                <h3 class="heading text-h3">Kirim Pesan</h3>
                <p class="mt-2 text-sm text-muted">Isi formulir di bawah ini dan tim kami akan segera menghubungi Anda.</p>
                <div class="mt-8">
                    @include('components.company.partials.form')
                </div>
            </div>
            @if ($company->mapEmbedUrl())
                <div class="min-h-72 border-t border-line lg:col-span-5 lg:border-t-0 lg:border-l">
                    @include('websites.partials.map', ['mapClass' => 'h-full min-h-72 w-full border-0'])
                </div>
            @endif
        </div>
    </div>
</section>
