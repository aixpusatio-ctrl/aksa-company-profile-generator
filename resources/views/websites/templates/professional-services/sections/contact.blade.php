{{-- Professional services contact: appointment request form with office card. --}}
<section id="contact" class="bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid overflow-hidden rounded-brand bg-white shadow-xl shadow-slate-900/5 ring-1 ring-slate-200 lg:grid-cols-12">
            <div class="bg-secondary/10 p-8 md:p-10 lg:col-span-5">
                <p class="flex items-center gap-3 text-xs font-semibold tracking-[0.2em] text-primary uppercase"><span class="h-px w-10 bg-secondary"></span> Kontak</p>
                <h2 class="mt-4 font-heading text-3xl leading-tight text-slate-900">{{ $section->title ?: 'Jadwalkan Konsultasi Anda' }}</h2>
                <p class="mt-4 text-slate-600">{{ $section->subtitle ?: 'Isi formulir dan staf kami akan menghubungi Anda untuk mengonfirmasi jadwal. Seluruh informasi dijaga kerahasiaannya.' }}</p>

                <ul class="mt-8 space-y-5">
                    @foreach ([
                        ['map-pin', 'Alamat kantor', $company->fullAddress(), $company->google_maps_url],
                        ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                        ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                        ['clock', 'Jam layanan', $company->working_hours, null],
                    ] as [$icon, $label, $value, $href])
                        @if ($value)
                            <li class="flex gap-4">
                                <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-white text-primary ring-1 ring-slate-200"><x-icon :name="$icon" class="size-4" /></span>
                                <div class="min-w-0">
                                    <p class="text-xs font-semibold text-slate-500 uppercase">{{ $label }}</p>
                                    @if ($href)
                                        <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="mt-0.5 block text-sm font-medium break-words text-slate-900 hover:text-primary">{{ $value }}</a>
                                    @else
                                        <p class="mt-0.5 text-sm font-medium text-slate-900">{{ $value }}</p>
                                    @endif
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                @if ($company->whatsappUrl())
                    <a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="mt-8 inline-flex items-center gap-2 rounded-btn bg-[#25D366] px-5 py-3 text-sm font-semibold text-white transition hover:opacity-90"><x-icon name="whatsapp" class="size-4" /> Chat WhatsApp</a>
                @endif

                @if ($company->mapEmbedUrl())
                    <div class="mt-8 overflow-hidden rounded-brand ring-1 ring-slate-200">
                        @include('websites.partials.map', ['mapClass' => 'h-56 w-full border-0'])
                    </div>
                @endif
            </div>
            <div class="p-8 md:p-10 lg:col-span-7">
                <div class="mb-6 flex items-center gap-3">
                    <span class="inline-flex size-10 items-center justify-center rounded-full bg-primary text-on-primary"><x-icon name="calendar" class="size-5" /></span>
                    <div>
                        <p class="font-heading text-lg text-slate-900">Formulir janji temu</p>
                        <p class="text-xs text-slate-500">Respons dalam 1 hari kerja</p>
                    </div>
                </div>
                @include('websites.partials.contact-form', [
                    'inputClass' => 'w-full rounded-brand border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-primary focus:ring-4 focus:ring-primary/10',
                    'labelClass' => 'mb-1.5 block text-xs font-semibold tracking-wide text-slate-600 uppercase',
                    'buttonClass' => 'inline-flex w-full items-center justify-center gap-2 rounded-btn bg-primary px-6 py-3.5 text-sm font-semibold text-on-primary transition hover:opacity-90 disabled:cursor-not-allowed disabled:opacity-60 sm:w-auto',
                    'buttonLabel' => 'Kirim Permintaan Konsultasi',
                ])
            </div>
        </div>
    </div>
</section>
