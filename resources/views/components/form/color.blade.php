@props(['name', 'label' => null, 'value' => '#1d4ed8'])
@php($key = trim(str_replace(['[', ']'], ['.', ''], $name), '.'))
<div {{ $attributes->only('class') }} x-data="{ color: @js(old($key, $value) ?: '#1d4ed8') }">
    @if ($label)<label class="form-label">{{ $label }}</label>@endif
    <div class="flex items-center gap-2">
        <input type="color" x-model="color" class="h-10 w-14 cursor-pointer rounded-lg border border-slate-300 bg-white p-1">
        <input type="text" name="{{ $name }}" x-model="color" maxlength="7" pattern="#[0-9a-fA-F]{6}" class="form-input font-mono uppercase">
    </div>
    @error($key)<p class="form-error">{{ $message }}</p>@enderror
</div>
