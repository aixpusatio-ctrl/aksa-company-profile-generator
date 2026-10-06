{{-- Product badges: discount %, new, tags, out of stock. Vars: $product, $badgeMax (tags, default 1), $badgeClass (wrapper). --}}
@php
    $tagColors = [
        'slate' => 'bg-slate-700 text-white', 'sky' => 'bg-sky-500 text-white', 'amber' => 'bg-amber-400 text-amber-950',
        'rose' => 'bg-rose-500 text-white', 'violet' => 'bg-violet-500 text-white', 'emerald' => 'bg-emerald-500 text-white',
    ];
    $discount = $product->discountPercent();
    $tagList = $product->tags->reject(fn ($t) => in_array($t->slug, ['sale', 'new'], true))->take($badgeMax ?? 1);
@endphp
<div class="{{ $badgeClass ?? 'pointer-events-none absolute top-2.5 left-2.5 z-10 flex flex-col items-start gap-1' }}">
    @unless ($product->isInStock())
        <span class="rounded-full bg-neutral-900/85 px-2 py-0.5 text-[10px] font-bold tracking-wide text-white uppercase">Stok habis</span>
    @endunless
    @if ($discount)
        <span class="rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-bold text-white">-{{ $discount }}%</span>
    @endif
    @if ($product->isNew())
        <span class="rounded-full bg-primary px-2 py-0.5 text-[10px] font-bold tracking-wide text-on-primary uppercase">Baru</span>
    @endif
    @foreach ($tagList as $tag)
        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $tagColors[$tag->color] ?? $tagColors['slate'] }}">{{ $tag->name }}</span>
    @endforeach
</div>
