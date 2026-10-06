{{-- Rich text editor (Trix). Output is sanitized server-side. --}}
@props(['name', 'label' => null, 'value' => null, 'help' => null])
@php($id = 'rt-'.$name.'-'.\Illuminate\Support\Str::random(4))
<div {{ $attributes->only('class') }}>
    @if ($label)<label for="{{ $id }}" class="form-label">{{ $label }}</label>@endif
    <input id="{{ $id }}" type="hidden" name="{{ $name }}" value="{{ old($name, $value) }}">
    <trix-editor input="{{ $id }}" class="prose-content"></trix-editor>
    @if ($help)<p class="form-help">{{ $help }}</p>@endif
    @error($name)<p class="form-error">{{ $message }}</p>@enderror
</div>
