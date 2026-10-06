{{--
    Website image with an elegant branded placeholder when no image exists.
    <x-site.img :src="$service->url('image')" alt="..." class="aspect-video w-full object-cover" icon="photo" />
--}}
@props(['src' => null, 'alt' => '', 'icon' => 'photo'])
@if ($src)
    <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy" decoding="async" {{ $attributes }}>
@else
    <div role="img" aria-label="{{ $alt }}" {{ $attributes->merge(['class' => 'flex items-center justify-center bg-gradient-to-br from-primary/80 to-secondary/90 text-on-primary/70']) }}>
        <x-icon :name="$icon" class="size-10 opacity-60" />
    </div>
@endif
