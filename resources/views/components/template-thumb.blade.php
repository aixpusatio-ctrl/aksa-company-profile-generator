{{--
    Live, scaled-down preview of a template (or any URL) rendered in an iframe.
    Uses the template thumbnail image when an admin uploaded one.
--}}
@props(['template' => null, 'src' => null, 'aspect' => 'aspect-[16/10]', 'live' => false])
@php($src ??= $template ? route('templates.render', $template) : null)
<div {{ $attributes->merge(['class' => "relative overflow-hidden bg-slate-100 $aspect"]) }}
     x-data="{ scale: 0.25 }" x-init="const fit = () => scale = $el.clientWidth / 1440; fit(); new ResizeObserver(fit).observe($el)">
    @if ($template?->thumbnail && ! $live)
        <img src="{{ $template->url('thumbnail') }}" alt="{{ $template->name }}" class="absolute inset-0 size-full object-cover object-top" loading="lazy">
    @else
        <iframe src="{{ $src }}" title="{{ $template?->name ?? 'Preview' }}" loading="lazy" tabindex="-1" scrolling="no"
                class="pointer-events-none absolute top-0 left-0 origin-top-left border-0"
                style="width:1440px;height:2400px" :style="`width:1440px;height:2400px;transform:scale(${scale})`"></iframe>
    @endif
</div>
