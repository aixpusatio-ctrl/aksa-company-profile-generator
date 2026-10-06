@props(['name', 'label' => null, 'value' => null, 'help' => null, 'rows' => 4])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = 'f-'.str_replace('.', '-', $key);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)<label for="{{ $id }}" class="form-label">{{ $label }}</label>@endif
    <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->except('class')->merge(['class' => 'form-input']) }}>{{ old($key, $value) }}</textarea>
    @if ($help)<p class="form-help">{{ $help }}</p>@endif
    @error($key)<p class="form-error">{{ $message }}</p>@enderror
</div>
