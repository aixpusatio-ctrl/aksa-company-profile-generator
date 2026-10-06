{{-- Stats: Cards — grid of stat cards with accent bar, index number and counters. --}}
@php
    $stats = $company->stats();
    $cols = [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3', 4 => 'lg:grid-cols-4', 5 => 'lg:grid-cols-5'][count($stats)] ?? 'lg:grid-cols-3';
@endphp
@if ($stats)
<section id="stats" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        @include('components.company.partials.heading', ['eyebrow' => 'Dalam Angka', 'title' => $section->title ?: 'Pencapaian '.$company->name, 'subtitle' => $section->subtitle, 'number' => $index])
        <dl class="mt-12 grid gap-3 sm:gap-4 {{ count($stats) === 1 ? 'grid-cols-1' : 'grid-cols-2' }} {{ $cols }} {{ count($stats) === 1 ? 'max-w-sm' : '' }}">
            @foreach ($stats as $stat)
                <div class="{{ $ds->card('relative flex flex-col overflow-hidden p-5 sm:p-8') }}" {!! $ds->reveal($loop->index) !!}>
                    <div class="flex items-center justify-between">
                        <span class="h-1 w-10 rounded-full bg-primary"></span>
                        <span class="font-mono text-xs text-muted">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <dd class="heading mt-6 text-[clamp(1.75rem,8vw,3.75rem)] whitespace-nowrap text-ink sm:mt-10" data-count>{{ $stat['value'] }}</dd>
                    <dt class="mt-3 text-sm font-medium text-muted">{{ $stat['label'] }}</dt>
                    <span class="pointer-events-none absolute -right-10 -bottom-10 size-32 rounded-full bg-primary/[0.07]"></span>
                </div>
            @endforeach
        </dl>
    </div>
</section>
@endif
