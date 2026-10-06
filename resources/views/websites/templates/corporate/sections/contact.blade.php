{{-- Corporate contact: info cards + form, map below. --}}
<section id="contact" class="bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-6">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-bold tracking-widest text-primary uppercase">Kontak</p>
            <h2 class="mt-3 font-heading text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $section->title ?: 'Mari Berdiskusi' }}</h2>
            <p class="mt-4 text-slate-600">{{ $section->subtitle ?: 'Tim kami siap membantu. Kirimkan pesan dan kami akan merespons dalam 1x24 jam kerja.' }}</p>
        </div>
        <div class="mt-14 grid gap-8 lg:grid-cols-5">
            <div class="space-y-4 lg:col-span-2">
                @foreach ([
                    ['map-pin', 'Alamat', $company->fullAddress(), $company->google_maps_url],
                    ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                    ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                    ['whatsapp', 'WhatsApp', $company->whatsapp, $company->whatsappUrl()],
                    ['clock', 'Jam Kerja', $company->working_hours, null],
                ] as [$icon, $label, $value, $href])
                    @if ($value)
                        <div class="flex gap-4 rounded-brand bg-white p-5 shadow-sm">
                            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-brand bg-primary/10 text-primary"><x-icon :name="$icon" class="size-5" /></span>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">{{ $label }}</p>
                                @if ($href)
                                    <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="mt-0.5 block text-sm font-semibold break-words text-slate-900 hover:text-primary">{{ $value }}</a>
                                @else
                                    <p class="mt-0.5 text-sm font-semibold text-slate-900">{{ $value }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="rounded-brand bg-white p-6 shadow-sm md:p-8 lg:col-span-3">
                @include('websites.partials.contact-form')
            </div>
        </div>
        @if ($company->mapEmbedUrl())
            <div class="mt-8 overflow-hidden rounded-brand shadow-sm">
                @include('websites.partials.map', ['mapClass' => 'h-80 w-full border-0'])
            </div>
        @endif
    </div>
</section>
