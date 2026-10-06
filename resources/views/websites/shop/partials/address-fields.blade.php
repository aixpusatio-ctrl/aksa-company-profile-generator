{{-- Address form fields. Vars: $fields (key => [label, required, max]), $addr (CustomerAddress|null), $prefix, $useOld. --}}
<div class="grid gap-3 sm:grid-cols-2">
    @foreach ($fields as $key => [$label, $required, $max])
        @php($value = $useOld ? old($key, $addr?->{$key} ?? ($key === 'country' ? 'Indonesia' : null)) : ($addr?->{$key} ?? ($key === 'country' ? 'Indonesia' : null)))
        <div class="{{ $key === 'address' ? 'sm:col-span-2' : '' }}">
            <label for="{{ $prefix }}-{{ $key }}" class="shop-label">{{ $label }}{{ $required ? ' *' : '' }}</label>
            @if ($key === 'address')
                <textarea id="{{ $prefix }}-{{ $key }}" name="{{ $key }}" rows="2" maxlength="{{ $max }}" @required($required) class="shop-input">{{ $value }}</textarea>
            @else
                <input id="{{ $prefix }}-{{ $key }}" name="{{ $key }}" value="{{ $value }}" maxlength="{{ $max }}" @required($required) @if ($key === 'phone') type="tel" @endif class="shop-input">
            @endif
            @if ($useOld)
                @error($key)<p class="shop-error">{{ $message }}</p>@enderror
            @endif
        </div>
    @endforeach
</div>
<label class="flex items-center gap-2.5 text-sm text-ink">
    <input type="checkbox" name="is_default" value="1" @checked($useOld ? old('is_default', $addr?->is_default) : $addr?->is_default) class="size-4 rounded accent-[var(--brand-primary)]"> Jadikan alamat utama
</label>
