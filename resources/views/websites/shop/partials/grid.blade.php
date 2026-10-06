{{-- Product grid using the template's product card. Vars: $products, $gridCols (optional override). --}}
@php
    $gridClass = $gridCols ?? match ($ds->get('product_card')) {
        'horizontal' => 'grid gap-4 md:grid-cols-2',
        'compact' => 'grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-5',
        'luxury' => 'grid grid-cols-2 gap-x-4 gap-y-10 lg:grid-cols-3 xl:grid-cols-4',
        'minimal' => 'grid grid-cols-2 gap-x-4 gap-y-8 sm:grid-cols-3 lg:grid-cols-4',
        'modern' => 'grid grid-cols-2 gap-x-3 gap-y-5 sm:gap-x-5 sm:gap-y-7 lg:grid-cols-4',
        default => 'grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4',
    };
@endphp
<div class="{{ $gridClass }}">
    @foreach ($products as $p)
        <div class="min-w-0" {!! $ds->reveal($loop->index % 4) !!}>
            @include($ds->productCard(), ['product' => $p])
        </div>
    @endforeach
</div>
