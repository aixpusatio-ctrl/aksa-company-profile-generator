{{-- Social media icon links. --}}
@props(['company', 'iconClass' => 'size-4', 'linkClass' => 'inline-flex size-9 items-center justify-center rounded-full transition'])
@if ($company->socialLinks())
    <div {{ $attributes->merge(['class' => 'flex flex-wrap items-center gap-2']) }}>
        @foreach ($company->socialLinks() as $network => $url)
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="{{ $linkClass }}" aria-label="{{ \App\Models\CompanyProfile::SOCIAL_NETWORKS[$network] ?? $network }}">
                <x-icon :name="$network" class="{{ $iconClass }}" />
            </a>
        @endforeach
    </div>
@endif
