@props(['name', 'label' => null, 'options' => [], 'value' => null, 'help' => null, 'placeholder' => null])
@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = 'f-'.str_replace('.', '-', $key);
    $selected = (string) old($key, $value);
@endphp
<div {{ $attributes->only('class') }}>
    @if ($label)<label for="{{ $id }}" class="form-label">{{ $label }}</label>@endif
    <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->except('class')->merge(['class' => 'form-input']) }}>
        @if ($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>
    @if ($help)<p class="form-help">{{ $help }}</p>@endif
    @error($key)<p class="form-error">{{ $message }}</p>@enderror
</div>
