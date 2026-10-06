{{-- Hero call-to-actions. Vars: $heroLight (bool: buttons on a dark image). --}}
<div class="mt-7 flex flex-wrap gap-3">
    <a href="{{ $site->shop('products') }}" class="{{ ($heroLight ?? false) ? $ds->btn('light') : $ds->btn('primary') }}">
        Belanja Sekarang <x-icon name="arrow-right" class="size-4" />
    </a>
    @if ($saleProducts->isNotEmpty())
        <a href="{{ $site->shop('products') }}?sale=1" class="{{ ($heroLight ?? false) ? 'ds-btn border border-white/40 text-white hover:bg-white/10' : $ds->btn('secondary') }}">Lihat Promo</a>
    @endif
</div>
