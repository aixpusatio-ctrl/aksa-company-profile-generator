{{--
    Template / website thumbnail.
    - Uses the uploaded or generated screenshot when available.
    - Otherwise renders a live preview: an iframe loaded lazily when the card
      scrolls into view, rendered at desktop (1440px) or mobile (390px) width
      and scaled down to the card.
--}}
@props(['template' => null, 'src' => null, 'aspect' => 'aspect-[16/10]', 'live' => false, 'mobile' => false])
@php
    $src ??= $template ? route('templates.render', $template) : null;
    $image = $template && ! $live ? ($mobile ? $template->url('mobile_thumbnail') : $template->url('thumbnail')) : null;
    $width = $mobile ? 390 : 1440;
@endphp
<div {{ $attributes->merge(['class' => "relative overflow-hidden bg-slate-100 $aspect"]) }}>
    @if ($image)
        <img src="{{ $image }}" alt="{{ $template->name }}{{ $mobile ? ' (mobile)' : '' }}" class="absolute inset-0 size-full object-cover object-top" loading="lazy" decoding="async">
    @else
        <div x-data="lazyFrame(@js($src), {{ $width }})" class="absolute inset-0">
            <div class="absolute inset-0 animate-pulse bg-gradient-to-br from-slate-100 to-slate-200" x-show="!loaded"></div>
            <template x-if="src">
                <iframe :src="src" title="{{ $template?->name ?? 'Preview' }}" tabindex="-1" scrolling="no" aria-hidden="true" @load="loaded = true"
                        class="pointer-events-none absolute top-0 left-0 origin-top-left border-0 bg-white transition-opacity duration-500"
                        :class="loaded ? 'opacity-100' : 'opacity-0'"
                        :style="`width:${width}px;height:${Math.round(width * 1.8)}px;transform:scale(${scale})`"></iframe>
            </template>
        </div>
    @endif
</div>
