{{-- Product price with struck-through original. Vars: $product, $priceSize (sm|md|lg), $priceClass. --}}
@php
    $original = $product->originalPrice();
    [$pmin, $pmax] = $product->priceRange();
    $sizeClass = match ($priceSize ?? 'md') { 'sm' => 'text-sm', 'lg' => 'text-2xl sm:text-3xl', default => 'text-base' };
@endphp
<div class="{{ $priceClass ?? 'flex flex-wrap items-baseline gap-x-2 gap-y-0.5' }}">
    <span class="font-bold {{ $sizeClass }} {{ $original ? 'text-rose-600' : 'text-ink' }}">{{ $product->formattedPrice() }}</span>
    @if ($original && $pmin === $pmax)
        <span class="text-xs text-muted line-through">{{ \App\Support\Shop\Money::format($original) }}</span>
    @endif
</div>
