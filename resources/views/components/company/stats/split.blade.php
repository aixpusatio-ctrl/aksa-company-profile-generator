{{-- Stats: Split — heading and about excerpt on the left, a hairline 2x2 grid of counters on the right. --}}
@php
    $stats = $company->stats();
    $about = \Illuminate\Support\Str::of(strip_tags(str_replace('<', ' <', (string) $company->about)))->squish()->limit(320)->toString();
@endphp
@if ($stats)
<section id="stats" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid items-center gap-12 lg:grid-cols-2 lg:gap-20">
        <div {!! $ds->reveal(0, 'left') !!}>
            {!! $ds->eyebrow('Dalam Angka', $index) !!}
            <h2 class="heading mt-4 text-h2">{{ $section->title ?: 'Bertumbuh Bersama Klien Kami' }}</h2>
            @if ($section->subtitle)
                <p class="mt-6 text-lead text-muted">{{ $section->subtitle }}</p>
            @elseif ($about)
                <p class="mt-6 text-lead text-muted">{{ $about }}</p>
            @endif
        </div>
        <dl class="grid grid-cols-2 border-t border-l border-line">
            @foreach ($stats as $stat)
                <div class="border-r border-b border-line p-5 sm:p-10 {{ $loop->last && $loop->count % 2 ? 'col-span-2' : '' }}" {!! $ds->reveal($loop->index + 1) !!}>
                    <dd class="heading text-[clamp(1.6rem,7vw,3.75rem)] whitespace-nowrap text-primary" data-count>{{ $stat['value'] }}</dd>
                    <dt class="mt-3 text-sm text-muted">{{ $stat['label'] }}</dt>
                </div>
            @endforeach
        </dl>
    </div>
</section>
@endif
