{{-- About: Stats — narrative and vision quote beside a large animated figures grid. --}}
@php($stats = array_slice($company->stats(), 0, 4))
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid items-start gap-14 lg:grid-cols-12 lg:gap-20">
        <div class="{{ $stats ? 'lg:col-span-6' : 'lg:col-span-8' }}">
            @include('components.company.partials.heading', ['eyebrow' => 'Tentang Kami', 'title' => $section->title ?: 'Mengenal '.$company->name, 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <div class="site-prose mt-8 text-muted" {!! $ds->reveal(1) !!}>{!! $company->about ?: e($company->description) !!}</div>
            @if ($company->vision)
                <figure class="mt-10 flex gap-5" {!! $ds->reveal(2) !!}>
                    <x-icon name="quote" class="size-8 shrink-0 text-primary" />
                    <blockquote>
                        <p class="heading text-xl leading-snug sm:text-2xl">{{ $company->vision }}</p>
                        <figcaption class="mt-3 text-xs font-semibold tracking-[0.2em] text-muted uppercase">Visi perusahaan</figcaption>
                    </blockquote>
                </figure>
            @endif
        </div>

        @if ($stats)
            <dl class="grid grid-cols-2 gap-px overflow-hidden rounded-brand bg-line ring-1 ring-line lg:col-span-6">
                @foreach ($stats as $i => $stat)
                    <div class="flex min-h-40 flex-col justify-between gap-6 p-6 sm:min-h-52 sm:p-8 {{ $loop->first ? 'bg-primary text-on-primary' : 'bg-surface' }} {{ count($stats) === 3 && $loop->first ? 'col-span-2' : '' }}" {!! $ds->reveal($i) !!}>
                        <dt class="text-sm {{ $loop->first ? 'text-on-primary/80' : 'text-muted' }}">{{ $stat['label'] }}</dt>
                        <dd class="heading text-[clamp(2rem,3.6vw,3.25rem)] leading-none sm:whitespace-nowrap" data-count>{{ $stat['value'] }}</dd>
                    </div>
                @endforeach
                @if (count($stats) === 1)
                    <div class="bg-surface"></div>
                @endif
            </dl>
        @endif
    </div>
</section>
