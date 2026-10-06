{{-- Trust badges under the shop hero. --}}
<ul class="mt-5 grid grid-cols-2 gap-2 text-xs sm:mt-6 sm:grid-cols-4 sm:gap-3 sm:text-sm">
    @foreach ([['truck', 'Pengiriman ke seluruh Indonesia'], ['shield', 'Pembayaran aman'], ['refresh', 'Stok selalu diperbarui'], ['chat', 'Bantuan via WhatsApp']] as [$perkIcon, $perkText])
        <li class="flex items-center gap-2.5 rounded-brand border border-line bg-card px-3 py-2.5 text-ink">
            <x-icon :name="$perkIcon" class="size-5 shrink-0 text-primary" /> <span class="leading-tight">{{ $perkText }}</span>
        </li>
    @endforeach
</ul>
