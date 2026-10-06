{{-- Compact rating (only when the product has reviews). Vars: $product. --}}
@if ($product->rating_count > 0)
    <div class="flex items-center gap-1 text-xs text-muted">
        <x-icon name="star" stroke="0" class="size-3.5 text-amber-400" />
        <span class="font-semibold text-ink">{{ number_format((float) $product->rating_avg, 1, ',', '.') }}</span>
        <span>({{ $product->rating_count }})</span>
        @if ($product->sold_count > 0)
            <span class="hidden sm:inline">· {{ $product->sold_count }} terjual</span>
        @endif
    </div>
@endif
