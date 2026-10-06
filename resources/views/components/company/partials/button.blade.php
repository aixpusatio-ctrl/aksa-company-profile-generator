{{-- @include('components.company.partials.button', ['href' => ..., 'label' => ..., 'kind' => 'primary|secondary|ghost|light|link', 'icon' => null, 'class' => '']) --}}
@php($kind ??= 'primary')
<a href="{{ $href }}" class="{{ $ds->btn($kind, $class ?? '') }}" @if (! empty($external)) target="_blank" rel="noopener noreferrer" @endif>
    @if (! empty($icon))<x-icon :name="$icon" class="size-4" />@endif
    {{ $label }}
    @if (empty($icon) && ($ds->arrows() || $kind === 'link'))<x-icon name="arrow-right" class="size-4" />@endif
</a>
