{{-- Consulting contact: understated two-column with underline-only form fields. --}}
<section id="contact" class="py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-16 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Kontak</p>
                <h2 class="mt-6 font-heading text-4xl leading-tight text-stone-900 md:text-5xl">{{ $section->title ?: 'Mulai percakapan.' }}</h2>
                <p class="mt-6 leading-relaxed text-stone-500">{{ $section->subtitle ?: 'Ceritakan sedikit tentang organisasi dan tantangan Anda. Seorang partner akan menghubungi Anda dalam satu hari kerja.' }}</p>
                <dl class="mt-12 space-y-8">
                    @foreach ([
                        ['Kantor', $company->fullAddress(), $company->google_maps_url],
                        ['Telepon', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                        ['Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                        ['WhatsApp', $company->whatsapp, $company->whatsappUrl()],
                        ['Jam Kerja', $company->working_hours, null],
                    ] as [$label, $value, $href])
                        @if ($value)
                            <div class="border-t border-stone-200 pt-4">
                                <dt class="text-[11px] tracking-[0.25em] text-stone-400 uppercase">{{ $label }}</dt>
                                <dd class="mt-2 font-heading text-lg break-words text-stone-900">
                                    @if ($href)
                                        <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="hover:text-primary">{{ $value }}</a>
                                    @else
                                        {{ $value }}
                                    @endif
                                </dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
            </div>
            <div class="lg:col-span-6 lg:col-start-7">
                <div class="border border-stone-200 bg-white p-8 md:p-12">
                    @include('websites.partials.contact-form', [
                        'inputClass' => 'w-full border-0 border-b border-stone-300 bg-transparent px-0 py-3 text-stone-900 outline-none transition focus:border-primary focus:ring-0',
                        'labelClass' => 'block text-[11px] tracking-[0.25em] text-stone-400 uppercase',
                        'buttonClass' => 'mt-4 inline-flex items-center gap-3 rounded-btn bg-stone-900 px-8 py-4 text-sm tracking-wide text-white transition hover:bg-primary hover:text-on-primary disabled:cursor-not-allowed disabled:opacity-60',
                        'buttonLabel' => 'Kirim Pesan',
                    ])
                </div>
            </div>
        </div>
        @if ($company->mapEmbedUrl())
            <div class="mt-20">
                @include('websites.partials.map', ['mapClass' => 'h-80 w-full border-0 grayscale opacity-90'])
            </div>
        @endif
    </div>
</section>
