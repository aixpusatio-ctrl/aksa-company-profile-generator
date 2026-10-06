{{-- Modern Business contact: gradient info panel + floating form card. --}}
<section id="contact" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-5 sm:px-6">
        <div class="grid overflow-hidden rounded-brand bg-white shadow-[0_30px_80px_-20px_rgb(15_23_42/0.2)] ring-1 ring-slate-100 lg:grid-cols-5">
            <div class="relative overflow-hidden bg-linear-to-br from-primary to-secondary p-8 text-on-primary sm:p-10 lg:col-span-2">
                <div class="absolute -right-20 -bottom-20 size-64 rounded-full bg-white/10"></div>
                <div class="absolute right-10 bottom-24 size-24 rounded-full bg-white/10"></div>
                <div class="relative">
                    <span class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-1.5 text-xs font-semibold ring-1 ring-white/25"><x-icon name="chat" class="size-3.5" /> Kontak</span>
                    <h2 class="mt-5 font-heading text-3xl font-semibold tracking-tight sm:text-4xl">{{ $section->title ?: 'Mari Ngobrol' }}</h2>
                    <p class="mt-4 text-on-primary/80">{{ $section->subtitle ?: 'Ceritakan kebutuhan Anda. Tim kami akan merespons dalam 1x24 jam kerja.' }}</p>
                    <ul class="mt-10 space-y-5">
                        @foreach ([
                            ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                            ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                            ['whatsapp', 'WhatsApp', $company->whatsapp, $company->whatsappUrl()],
                            ['map-pin', 'Alamat', $company->fullAddress(), $company->google_maps_url],
                            ['clock', 'Jam Kerja', $company->working_hours, null],
                        ] as [$icon, $label, $value, $href])
                            @if ($value)
                                <li class="flex gap-4">
                                    <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/20"><x-icon :name="$icon" class="size-5" /></span>
                                    <div class="min-w-0">
                                        <p class="text-xs text-on-primary/60">{{ $label }}</p>
                                        @if ($href)
                                            <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="text-sm font-semibold break-words hover:underline">{{ $value }}</a>
                                        @else
                                            <p class="text-sm font-semibold">{{ $value }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                    <x-site.social :company="$company" class="mt-10" link-class="inline-flex size-10 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/20 transition hover:bg-white hover:text-slate-900" />
                </div>
            </div>
            <div class="p-8 sm:p-10 lg:col-span-3 lg:p-12">
                <h3 class="font-heading text-2xl font-semibold text-slate-900">Kirim pesan</h3>
                <p class="mt-1 mb-8 text-sm text-slate-500">Isi formulir di bawah ini dan kami akan segera menghubungi Anda.</p>
                @include('websites.partials.contact-form', [
                    'inputClass' => 'w-full rounded-2xl border-0 bg-slate-50 px-5 py-3.5 text-sm text-slate-900 ring-1 ring-slate-200 outline-none transition placeholder:text-slate-400 focus:bg-white focus:ring-2 focus:ring-primary',
                    'labelClass' => 'mb-2 block text-sm font-medium text-slate-700',
                    'buttonClass' => 'inline-flex items-center justify-center gap-2 rounded-btn bg-linear-to-r from-primary to-secondary px-8 py-4 text-sm font-semibold text-on-primary shadow-lg shadow-primary/30 transition hover:-translate-y-0.5 disabled:opacity-60 disabled:hover:translate-y-0',
                    'buttonLabel' => 'Kirim Pesan',
                ])
            </div>
        </div>
        @if ($company->mapEmbedUrl())
            <div class="mt-8 overflow-hidden rounded-brand shadow-[0_8px_30px_rgb(15_23_42/0.08)] ring-1 ring-slate-100">
                @include('websites.partials.map', ['mapClass' => 'h-80 w-full border-0 grayscale-[30%]'])
            </div>
        @endif
    </div>
</section>
