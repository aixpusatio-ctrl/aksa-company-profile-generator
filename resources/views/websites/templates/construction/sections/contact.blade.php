{{-- Construction contact: dark info column + white form panel on stone, map full width. --}}
<section id="contact" class="bg-stone-100 lg:bg-[linear-gradient(90deg,#0c0a09_calc(50%-8rem),transparent_calc(50%-8rem))]">
    <div class="mx-auto grid max-w-7xl lg:grid-cols-5">
        <div class="relative bg-stone-950 px-5 py-16 text-white sm:px-10 lg:col-span-2 lg:py-24">
            <div class="absolute top-0 right-0 h-2 w-full bg-primary lg:w-[50vw]"></div>
            <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase"><span class="h-1 w-10 bg-primary"></span> Kontak</p>
            <h2 class="mt-5 font-heading text-4xl leading-[0.95] font-bold tracking-tight uppercase sm:text-5xl">{{ $section->title ?: 'Diskusikan Proyek Anda' }}</h2>
            <p class="mt-5 text-stone-400">{{ $section->subtitle ?: 'Hubungi kami untuk survei lokasi, estimasi biaya, atau konsultasi teknis.' }}</p>
            <ul class="mt-10 space-y-6">
                @foreach ([
                    ['map-pin', 'Alamat', $company->fullAddress(), $company->google_maps_url],
                    ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                    ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                    ['whatsapp', 'WhatsApp', $company->whatsapp, $company->whatsappUrl()],
                    ['clock', 'Jam Operasional', $company->working_hours, null],
                ] as [$icon, $label, $value, $href])
                    @if ($value)
                        <li class="flex gap-4">
                            <span class="inline-flex size-12 shrink-0 items-center justify-center bg-primary text-on-primary"><x-icon :name="$icon" class="size-5" /></span>
                            <div class="min-w-0">
                                <p class="font-heading text-xs font-semibold tracking-[0.2em] text-stone-500 uppercase">{{ $label }}</p>
                                @if ($href)
                                    <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="mt-1 block font-medium break-words text-white transition hover:text-primary">{{ $value }}</a>
                                @else
                                    <p class="mt-1 font-medium text-white">{{ $value }}</p>
                                @endif
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="px-5 py-16 sm:px-10 lg:col-span-3 lg:py-24 lg:pl-16">
            <h3 class="font-heading text-2xl font-bold tracking-wide text-stone-950 uppercase">Formulir Permintaan</h3>
            <p class="mt-2 mb-8 text-sm text-stone-500">Lengkapi data berikut, estimator kami akan menghubungi Anda.</p>
            @include('websites.partials.contact-form', [
                'inputClass' => 'w-full border-0 border-b-2 border-stone-300 bg-white px-4 py-3.5 text-sm text-stone-900 outline-none transition focus:border-primary focus:ring-0',
                'labelClass' => 'mb-2 block font-heading text-xs font-semibold tracking-[0.2em] text-stone-700 uppercase',
                'buttonClass' => 'inline-flex items-center justify-center gap-3 rounded-btn bg-stone-950 px-8 py-4 font-heading text-sm font-bold tracking-widest text-white uppercase transition hover:bg-primary hover:text-on-primary disabled:opacity-60',
                'buttonLabel' => 'Kirim Permintaan',
            ])
        </div>
    </div>
    @if ($company->mapEmbedUrl())
        <div class="border-t-4 border-primary">
            @include('websites.partials.map', ['mapClass' => 'block h-96 w-full border-0 grayscale'])
        </div>
    @endif
</section>
