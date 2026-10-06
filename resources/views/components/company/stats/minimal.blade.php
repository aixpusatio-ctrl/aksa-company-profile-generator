{{-- Stats: Minimal — inline row of numbers with labels between hairlines, no background treatment. --}}
@php($stats = $company->stats())
@if ($stats)
<section id="stats" class="{{ $ds->section($tone, '!py-14 sm:!py-20') }}">
    <div class="{{ $ds->container() }}">
        @if ($section->title)
            <p class="mb-8 text-xs font-semibold tracking-[0.22em] text-muted uppercase" {!! $ds->reveal() !!}>{{ $section->title }}</p>
        @endif
        <dl class="grid grid-cols-2 gap-x-6 gap-y-10 border-y border-line py-10 lg:flex lg:gap-0 lg:divide-x lg:divide-line">
            @foreach ($stats as $stat)
                <div class="flex flex-col gap-2 lg:flex-1 lg:px-8 lg:first:pl-0 lg:last:pr-0" {!! $ds->reveal($loop->index) !!}>
                    <dd class="heading text-4xl text-ink sm:text-5xl" data-count>{{ $stat['value'] }}</dd>
                    <dt class="text-sm text-muted">{{ $stat['label'] }}</dt>
                </div>
            @endforeach
        </dl>
    </div>
</section>
@endif
