{{-- Minimal contact: plain text details + underline-input form. --}}
<section id="contact" class="mx-auto max-w-4xl px-6">
    <div class="grid gap-8 border-t border-neutral-200 py-16 md:grid-cols-4 md:py-24">
        <div>
            <p class="text-xs text-neutral-400 tabular-nums">({{ str_pad($index, 2, '0', STR_PAD_LEFT) }})</p>
            <h2 class="mt-1 text-sm font-medium text-neutral-950">Kontak</h2>
        </div>
        <div class="md:col-span-3">
            <p class="font-heading text-2xl leading-snug font-medium tracking-tight text-neutral-950 md:text-3xl">{{ $section->title ?: 'Tulis kepada kami.' }}</p>
            <p class="mt-4 max-w-xl text-neutral-500">{{ $section->subtitle ?: 'Kami membaca setiap pesan dan membalas dalam satu hari kerja.' }}</p>

            <dl class="mt-10 grid gap-x-8 gap-y-6 text-sm sm:grid-cols-2">
                @if ($company->email)
                    <div><dt class="text-neutral-400">Email</dt><dd class="mt-1"><a href="mailto:{{ $company->email }}" class="break-all text-neutral-950 underline decoration-neutral-300 underline-offset-4 hover:decoration-neutral-950">{{ $company->email }}</a></dd></div>
                @endif
                @if ($company->phone)
                    <div><dt class="text-neutral-400">Telepon</dt><dd class="mt-1"><a href="tel:{{ $company->phone }}" class="text-neutral-950 underline decoration-neutral-300 underline-offset-4 hover:decoration-neutral-950">{{ $company->phone }}</a></dd></div>
                @endif
                @if ($company->whatsappUrl())
                    <div><dt class="text-neutral-400">WhatsApp</dt><dd class="mt-1"><a href="{{ $company->whatsappUrl() }}" target="_blank" rel="noopener noreferrer" class="text-neutral-950 underline decoration-neutral-300 underline-offset-4 hover:decoration-neutral-950">{{ $company->whatsapp }}</a></dd></div>
                @endif
                @if ($company->working_hours)
                    <div><dt class="text-neutral-400">Jam kerja</dt><dd class="mt-1 text-neutral-950">{{ $company->working_hours }}</dd></div>
                @endif
                @if ($company->fullAddress())
                    <div class="sm:col-span-2"><dt class="text-neutral-400">Alamat</dt><dd class="mt-1 max-w-md text-neutral-950">
                        @if ($company->google_maps_url)
                            <a href="{{ $company->google_maps_url }}" target="_blank" rel="noopener noreferrer" class="underline decoration-neutral-300 underline-offset-4 hover:decoration-neutral-950">{{ $company->fullAddress() }}</a>
                        @else
                            {{ $company->fullAddress() }}
                        @endif
                    </dd></div>
                @endif
            </dl>

            <div class="mt-16">
                @include('websites.partials.contact-form', [
                    'inputClass' => 'w-full rounded-none border-0 border-b border-neutral-300 bg-transparent px-0 py-2.5 text-[15px] text-neutral-950 outline-none transition placeholder:text-neutral-300 focus:border-neutral-950 focus:ring-0',
                    'labelClass' => 'block text-xs text-neutral-400',
                    'buttonClass' => 'mt-4 inline-flex items-center gap-2 border-b border-neutral-950 pb-1 text-sm font-medium text-neutral-950 transition hover:gap-3 disabled:cursor-not-allowed disabled:opacity-40',
                    'buttonLabel' => 'Kirim pesan',
                ])
            </div>
        </div>
    </div>
</section>
