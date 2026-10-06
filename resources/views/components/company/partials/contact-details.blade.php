{{-- Contact details list. Params: $class, $itemClass --}}
<ul class="space-y-5 {{ $class ?? '' }}">
    @foreach ([
        ['map-pin', 'Alamat', $company->fullAddress(), $company->google_maps_url],
        ['phone', 'Telepon', $company->phone, $company->phone ? 'tel:'.preg_replace('/[^0-9+]/', '', $company->phone) : null],
        ['mail', 'Email', $company->email, $company->email ? 'mailto:'.$company->email : null],
        ['whatsapp', 'WhatsApp', $company->whatsapp, $company->whatsappUrl()],
        ['clock', 'Jam Kerja', $company->working_hours, null],
    ] as [$icon, $label, $value, $href])
        @if ($value)
            <li class="flex gap-4 {{ $itemClass ?? '' }}" {!! $ds->reveal($loop->index + 1) !!}>
                <span class="inline-flex size-11 shrink-0 items-center justify-center rounded-brand bg-primary/10 text-primary"><x-icon :name="$icon" class="size-5" /></span>
                <span class="min-w-0">
                    <span class="block text-xs font-semibold tracking-wide text-muted uppercase">{{ $label }}</span>
                    @if ($href)
                        <a href="{{ $href }}" @if (str_starts_with($href, 'http')) target="_blank" rel="noopener noreferrer" @endif class="mt-0.5 block font-medium break-words text-ink hover:text-primary">{{ $value }}</a>
                    @else
                        <span class="mt-0.5 block font-medium text-ink">{{ $value }}</span>
                    @endif
                </span>
            </li>
        @endif
    @endforeach
</ul>
