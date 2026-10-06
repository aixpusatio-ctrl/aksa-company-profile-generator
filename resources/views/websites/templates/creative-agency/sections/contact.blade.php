{{-- Creative contact: dark block with big type, chunky contact chips and a rounded form card. --}}
<section id="contact" class="px-3 pb-20 sm:px-5 lg:pb-28">
    <div class="mx-auto grid max-w-[90rem] gap-4 lg:grid-cols-2">
        <div class="flex flex-col rounded-[2rem] bg-secondary p-8 text-on-secondary sm:p-12">
            <h2 class="font-heading text-5xl leading-[0.9] font-extrabold tracking-tighter md:text-7xl">{{ $section->title ?: 'Halo, mari ngobrol!' }}</h2>
            <p class="mt-6 max-w-md text-on-secondary/60">{{ $section->subtitle ?: 'Isi formulir atau langsung sapa kami lewat kanal favorit Anda.' }}</p>
            <div class="mt-10 flex flex-col gap-3">
                @foreach ([
                    ['mail', $company->email, $company->email ? 'mailto:'.$company->email : null],
                    ['phone', $company->phone, $company->phone ? 'tel:'.$company->phone : null],
                    ['whatsapp', $company->whatsapp, $company->whatsappUrl()],
                    ['map-pin', $company->fullAddress(), $company->google_maps_url],
                    ['clock', $company->working_hours, null],
                ] as [$icon, $value, $href])
                    @if ($value)
                        @if ($href)
                            <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="group flex items-center gap-4 rounded-2xl bg-white/5 p-4 transition hover:bg-primary hover:text-on-primary">
                        @else
                            <div class="flex items-center gap-4 rounded-2xl bg-white/5 p-4">
                        @endif
                            <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-full bg-primary text-on-primary transition group-hover:bg-white group-hover:text-neutral-950"><x-icon :name="$icon" class="size-5" /></span>
                            <span class="min-w-0 flex-1 font-semibold break-words">{{ $value }}</span>
                            @if ($href)<x-icon name="arrow-up-right" class="size-5 shrink-0 opacity-40 transition group-hover:rotate-45 group-hover:opacity-100" />@endif
                        @if ($href)</a>@else</div>@endif
                    @endif
                @endforeach
            </div>
            @if ($company->mapEmbedUrl())
                <div class="mt-8 overflow-hidden rounded-2xl">
                    @include('websites.partials.map', ['mapClass' => 'h-56 w-full border-0 grayscale invert-[0.9] hue-rotate-180'])
                </div>
            @endif
        </div>
        <div class="rounded-[2rem] bg-white p-8 sm:p-12">
            <p class="inline-flex rotate-[-2deg] rounded-full bg-primary px-4 py-1.5 text-sm font-extrabold text-on-primary">Brief singkat ✍️</p>
            <div class="mt-8">
                @include('websites.partials.contact-form', [
                    'inputClass' => 'w-full rounded-2xl border-2 border-neutral-200 bg-neutral-50 px-5 py-4 text-neutral-950 outline-none transition focus:border-neutral-950 focus:bg-white',
                    'labelClass' => 'mb-2 block text-sm font-extrabold text-neutral-950',
                    'buttonClass' => 'inline-flex w-full items-center justify-center gap-2 rounded-btn bg-neutral-950 px-8 py-4 text-lg font-bold text-white transition hover:bg-primary hover:text-on-primary disabled:cursor-not-allowed disabled:opacity-60',
                    'buttonLabel' => 'Kirim brief',
                ])
            </div>
        </div>
    </div>
</section>
