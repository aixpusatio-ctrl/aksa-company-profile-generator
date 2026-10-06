{{-- Executive contact: dark split, office details with gold hairlines, refined underline form. --}}
<section id="contact" class="relative bg-secondary py-24 lg:py-32">
    <div class="mx-auto grid max-w-7xl gap-16 px-6 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <p class="flex items-center gap-4 text-[11px] font-medium tracking-[0.4em] text-primary uppercase"><span class="h-px w-10 bg-primary"></span> Kontak</p>
            <h2 class="mt-6 font-heading text-4xl leading-[1.1] font-medium text-on-secondary md:text-5xl">{{ $section->title ?: 'Mari Berbicara' }}</h2>
            <p class="mt-5 text-on-secondary/60">{{ $section->subtitle ?: 'Setiap percakapan diperlakukan dengan kerahasiaan penuh. Tim kami akan menghubungi Anda dalam satu hari kerja.' }}</p>

            <dl class="mt-12 divide-y divide-primary/15 border-y border-primary/15">
                @foreach ([
                    ['Kantor', $company->fullAddress(), $company->google_maps_url],
                    ['Telepon', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                    ['Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                    ['WhatsApp', $company->whatsapp, $company->whatsappUrl()],
                    ['Jam Kerja', $company->working_hours, null],
                ] as [$label, $value, $href])
                    @if ($value)
                        <div class="grid gap-1 py-5 sm:grid-cols-3 sm:gap-4">
                            <dt class="text-[10px] tracking-[0.3em] text-primary uppercase sm:pt-1">{{ $label }}</dt>
                            <dd class="text-sm break-words text-on-secondary/85 sm:col-span-2">
                                @if ($href)
                                    <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="transition hover:text-primary">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                    @endif
                @endforeach
            </dl>
        </div>

        <div class="lg:col-span-7">
            <div class="border border-primary/25 p-8 md:p-12">
                <p class="font-heading text-2xl text-on-secondary italic">Kirim pesan pribadi</p>
                <div class="mt-8">
                    @include('websites.partials.contact-form', [
                        'inputClass' => 'w-full rounded-none border-0 border-b border-on-secondary/20 bg-transparent px-0 py-3 text-sm text-on-secondary outline-none transition focus:border-primary focus:ring-0',
                        'labelClass' => 'block text-[10px] tracking-[0.3em] text-on-secondary/50 uppercase',
                        'buttonClass' => 'mt-4 inline-flex items-center justify-center gap-3 rounded-btn bg-primary px-9 py-4 text-[11px] font-semibold tracking-[0.3em] text-on-primary uppercase transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-50',
                        'buttonLabel' => 'Kirim Pesan',
                    ])
                </div>
            </div>
        </div>
    </div>
    @if ($company->mapEmbedUrl())
        <div class="mx-auto mt-16 max-w-7xl px-6">
            <div class="border border-primary/25 p-2">
                @include('websites.partials.map', ['mapClass' => 'h-80 w-full border-0 grayscale invert-[.9] hue-rotate-180'])
            </div>
        </div>
    @endif
</section>
