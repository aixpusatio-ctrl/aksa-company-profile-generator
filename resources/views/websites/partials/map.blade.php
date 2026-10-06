{{-- Google Maps embed (if an address / coordinates exist). --}}
@php($mapUrl = $company->mapEmbedUrl())
@if ($mapUrl)
    <iframe src="{{ $mapUrl }}" title="Lokasi {{ $company->name }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" class="{{ $mapClass ?? 'h-72 w-full rounded-brand border-0' }}" allowfullscreen></iframe>
@endif
