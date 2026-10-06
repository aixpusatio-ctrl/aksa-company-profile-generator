{{-- Company logo, or a typographic monogram fallback. --}}
@props(['company', 'imgClass' => 'h-10 w-auto', 'textClass' => 'text-xl font-bold', 'boxClass' => 'bg-primary text-on-primary'])
@if ($company->logo)
    <img src="{{ $company->url('logo') }}" alt="{{ $company->name }}" class="{{ $imgClass }}">
@else
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
        <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-brand {{ $boxClass }} text-sm font-bold">{{ \Illuminate\Support\Str::of($company->name)->replace(['PT ', 'CV ', 'PT. ', 'CV. '], '')->explode(' ')->filter()->take(2)->map(fn ($w) => mb_substr($w, 0, 1))->implode('') }}</span>
        <span class="font-heading {{ $textClass }} leading-none">{{ $company->name }}</span>
    </span>
@endif
