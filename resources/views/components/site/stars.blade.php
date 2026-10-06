@props(['rating' => 5])
<div {{ $attributes->merge(['class' => 'flex items-center gap-0.5']) }} aria-label="{{ $rating }} / 5">
    @for ($i = 1; $i <= 5; $i++)
        <x-icon name="star" stroke="0" class="size-4 {{ $i <= $rating ? 'text-amber-400' : 'text-current opacity-20' }}" />
    @endfor
</div>
