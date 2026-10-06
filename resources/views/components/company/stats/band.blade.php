{{-- Stats: Band — large counters in a single row separated by dividers. --}}
<section id="stats" class="{{ $ds->section($tone, '!py-14 sm:!py-16') }}">
    <div class="{{ $ds->container() }}">
        @if ($section->title)
            <h2 class="heading mb-10 text-2xl {{ $ds->align() === 'center' ? 'text-center' : '' }}">{{ $section->title }}</h2>
        @endif
        @php($cols = [1 => 'lg:grid-cols-2', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3'][count($company->stats())] ?? 'lg:grid-cols-4')
        <dl class="grid grid-cols-2 gap-y-10 {{ $cols }} lg:divide-x lg:divide-line">
            @foreach ($company->stats() as $stat)
                <div class="px-2 text-center lg:px-6" {!! $ds->reveal($loop->index) !!}>
                    <dd class="heading text-4xl text-primary sm:text-5xl" data-count>{{ $stat['value'] }}</dd>
                    <dt class="mt-2 text-sm text-muted">{{ $stat['label'] }}</dt>
                </div>
            @endforeach
        </dl>
    </div>
</section>
