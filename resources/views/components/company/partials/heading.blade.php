{{--
    Section heading in the template's style.
    @include('components.company.partials.heading', [
        'eyebrow' => 'Layanan', 'title' => '...', 'subtitle' => '...',
        'align' => null (template default) | 'left' | 'center', 'number' => 2,
        'class' => '', 'titleClass' => '',
    ])
--}}
@php
    $align ??= $ds->align();
    $centered = $align === 'center';
@endphp
<div class="{{ $centered ? 'mx-auto max-w-3xl text-center' : 'max-w-3xl' }} {{ $class ?? '' }}" {!! $ds->reveal() !!}>
    {!! $ds->eyebrow($eyebrow ?? null, $number ?? null) !!}
    <h2 class="heading text-h2 {{ filled($eyebrow ?? null) ? 'mt-4' : '' }} {{ $titleClass ?? '' }}">{{ $title }}</h2>
    @if (filled($subtitle ?? null))
        <p class="mt-5 text-lead text-muted {{ $centered ? 'mx-auto max-w-2xl' : 'max-w-2xl' }}">{{ $subtitle }}</p>
    @endif
</div>
