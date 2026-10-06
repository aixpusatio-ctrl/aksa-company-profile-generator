{{--
    Contact form styled by the design system.
    Params: $variant = 'boxed' (default) | 'underline', $buttonLabel
--}}
@php
    $variant ??= 'boxed';
    $inputClass = $variant === 'underline'
        ? 'w-full border-0 border-b border-line bg-transparent px-0 py-3 text-ink placeholder:text-muted/70 focus:border-primary focus:ring-0 focus:outline-none'
        : 'w-full rounded-brand border border-line bg-surface px-4 py-3 text-sm text-ink placeholder:text-muted/70 transition focus:border-primary focus:ring-4 focus:ring-primary/15 focus:outline-none';
@endphp
@include('websites.partials.contact-form', [
    'inputClass' => $inputClass,
    'labelClass' => 'mb-1.5 block text-sm font-medium text-ink',
    'buttonClass' => $ds->btn('primary', 'mt-2 w-full sm:w-auto'),
    'buttonLabel' => $buttonLabel ?? 'Kirim Pesan',
])
