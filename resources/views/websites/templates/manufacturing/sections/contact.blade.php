{{-- Manufacturing contact: RFQ form panel + contact spec list + map. --}}
<section id="contact" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-5">
                <p class="font-mono text-xs font-semibold tracking-widest text-primary uppercase">{{ str_pad($index, 2, '0', STR_PAD_LEFT) }} — Kontak</p>
                <h2 class="mt-3 font-heading text-3xl font-bold tracking-tight text-slate-900 uppercase md:text-4xl">{{ $section->title ?: 'Hubungi Tim Sales' }}</h2>
                <p class="mt-4 text-slate-600">{{ $section->subtitle ?: 'Ceritakan kebutuhan produksi Anda. Kami merespons setiap permintaan penawaran dalam 1x24 jam kerja.' }}</p>
                <dl class="mt-10 border-t-2 border-slate-900">
                    @foreach ([
                        ['map-pin', 'Alamat', $company->fullAddress(), $company->google_maps_url],
                        ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                        ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                        ['whatsapp', 'WhatsApp', $company->whatsapp, $company->whatsappUrl()],
                        ['clock', 'Jam Operasional', $company->working_hours, null],
                    ] as [$icon, $label, $value, $href])
                        @if ($value)
                            <div class="grid grid-cols-3 gap-4 border-b border-slate-200 py-4">
                                <dt class="flex items-center gap-2 font-mono text-[11px] font-semibold tracking-widest text-slate-500 uppercase"><x-icon :name="$icon" class="size-4 text-primary" /> {{ $label }}</dt>
                                <dd class="col-span-2 text-sm font-semibold break-words text-slate-900">
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
            <div class="lg:col-span-7">
                <div class="rounded-brand border border-slate-200 bg-slate-50 p-6 md:p-10">
                    <div class="mb-6 flex items-center justify-between border-b border-slate-200 pb-4">
                        <p class="font-heading text-lg font-bold text-slate-900 uppercase">Formulir RFQ</p>
                        <span class="font-mono text-[11px] text-slate-500 uppercase">Respons ≤ 24 jam</span>
                    </div>
                    @include('websites.partials.contact-form', [
                        'inputClass' => 'w-full rounded-brand border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-900 focus:ring-2 focus:ring-primary/30',
                        'labelClass' => 'mb-1.5 block font-mono text-[11px] font-semibold tracking-widest text-slate-600 uppercase',
                        'buttonClass' => 'inline-flex w-full items-center justify-center gap-2 rounded-btn bg-primary px-7 py-4 text-xs font-bold tracking-wider text-on-primary uppercase transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto',
                        'buttonLabel' => 'Kirim Permintaan Penawaran',
                    ])
                </div>
            </div>
        </div>
        @if ($company->mapEmbedUrl())
            <div class="mt-12 border border-slate-200 p-1.5">
                @include('websites.partials.map', ['mapClass' => 'h-80 w-full border-0 grayscale'])
            </div>
        @endif
    </div>
</section>
