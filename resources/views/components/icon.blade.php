@props(['name', 'stroke' => '1.5'])
{!! \App\Support\Icons::svg($name, $attributes->get('class', 'size-5'), $stroke, trim($attributes->except('class')->toHtml())) !!}
