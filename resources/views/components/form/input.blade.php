@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'help' => null, 'prefix' => null, 'suffix' => null])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $attributes->get('id', 'f-'.str_replace('.', '-', $key));
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)
        <label for="{{ $id }}" class="form-label">{{ $label }} @if ($attributes->has('required'))<span class="text-rose-500">*</span>@endif</label>
    @endif
    <div class="relative flex">
        @if ($prefix)
            <span class="inline-flex items-center rounded-l-lg border border-r-0 border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">{{ $prefix }}</span>
        @endif
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $type === 'password' ? '' : old($key, $value) }}"
            {{ $attributes->except(['class', 'id'])->merge(['class' => 'form-input'.($prefix ? ' rounded-l-none' : '').($suffix ? ' rounded-r-none' : '').($errors->has($key) ? ' border-rose-400' : '')]) }}>
        @if ($suffix)
            <span class="inline-flex items-center rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 px-3 text-sm text-slate-500">{{ $suffix }}</span>
        @endif
    </div>
    @if ($help)<p class="form-help">{{ $help }}</p>@endif
    @error($key)<p class="form-error">{{ $message }}</p>@enderror
</div>
