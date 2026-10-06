@props(['label', 'value', 'icon' => 'chart', 'color' => 'brand', 'hint' => null])
@php
    $colors = [
        'brand' => 'bg-brand-50 text-brand-600', 'green' => 'bg-emerald-50 text-emerald-600', 'amber' => 'bg-amber-50 text-amber-600',
        'violet' => 'bg-violet-50 text-violet-600', 'rose' => 'bg-rose-50 text-rose-600', 'sky' => 'bg-sky-50 text-sky-600', 'slate' => 'bg-slate-100 text-slate-600',
    ];
@endphp
<div {{ $attributes->merge(['class' => 'card card-body']) }}>
    <div class="flex items-start justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-2 font-display text-3xl font-bold text-slate-900">{{ $value }}</p>
        </div>
        <span class="inline-flex size-11 items-center justify-center rounded-xl {{ $colors[$color] ?? $colors['brand'] }}"><x-icon :name="$icon" class="size-6" /></span>
    </div>
    @if ($hint)
        <p class="mt-3 text-xs text-slate-500">{{ $hint }}</p>
    @endif
</div>
