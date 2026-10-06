{{-- Technology contact: mono key/value channels + dark form panel. --}}
<section id="contact" class="relative py-24 lg:py-32">
    <div class="mx-auto grid max-w-7xl gap-12 px-5 sm:px-6 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <p class="font-mono text-sm text-primary">// kontak</p>
            <h2 class="mt-4 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Mari bicara' }}</h2>
            <p class="mt-5 text-slate-400">{{ $section->subtitle ?: 'Kirim pesan, kami biasanya membalas dalam satu hari kerja.' }}</p>
            <div class="mt-10 divide-y divide-white/10 rounded-brand border border-white/10 bg-white/[0.02]">
                @foreach ([
                    ['mail', 'email', $company->email, $company->email ? 'mailto:'.$company->email : null],
                    ['phone', 'phone', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                    ['whatsapp', 'whatsapp', $company->whatsapp, $company->whatsappUrl()],
                    ['map-pin', 'address', $company->fullAddress(), $company->google_maps_url],
                    ['clock', 'hours', $company->working_hours, null],
                ] as [$icon, $key, $value, $href])
                    @if ($value)
                        <div class="group flex gap-4 p-4 sm:p-5">
                            <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-slate-900 text-primary"><x-icon :name="$icon" class="size-4" /></span>
                            <div class="min-w-0">
                                <p class="font-mono text-xs text-slate-500">{{ $key }}</p>
                                @if ($href)
                                    <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="mt-0.5 block text-sm break-words text-slate-200 transition hover:text-primary">{{ $value }}</a>
                                @else
                                    <p class="mt-0.5 text-sm text-slate-200">{{ $value }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="lg:col-span-7">
            <div class="rounded-brand border border-white/10 bg-slate-900/60 p-6 shadow-2xl shadow-black/40 sm:p-8">
                <div class="mb-6 flex items-center gap-2 font-mono text-xs text-slate-500">
                    <span class="size-2 rounded-full bg-primary shadow-[0_0_10px_2px_var(--brand-primary)]"></span> POST /pesan
                </div>
                @include('websites.partials.contact-form', [
                    'inputClass' => 'w-full rounded-lg border border-white/10 bg-slate-950/70 px-4 py-3 text-sm text-white outline-none transition placeholder:text-slate-600 focus:border-primary/60 focus:ring-4 focus:ring-primary/15',
                    'labelClass' => 'mb-1.5 block font-mono text-xs text-slate-400',
                    'buttonClass' => 'inline-flex items-center justify-center gap-2 rounded-btn bg-primary px-6 py-3.5 text-sm font-semibold text-on-primary shadow-[0_0_30px_-8px_var(--brand-primary)] transition hover:shadow-[0_0_40px_-4px_var(--brand-primary)] disabled:opacity-60',
                    'buttonLabel' => 'Kirim Pesan',
                ])
            </div>
            @if ($company->mapEmbedUrl())
                <div class="mt-5 overflow-hidden rounded-brand border border-white/10">
                    @include('websites.partials.map', ['mapClass' => 'h-64 w-full border-0 opacity-80 invert-[0.92] hue-rotate-180'])
                </div>
            @endif
        </div>
    </div>
</section>
