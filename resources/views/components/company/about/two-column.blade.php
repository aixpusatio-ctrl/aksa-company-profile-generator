{{-- About: Two Column — heading & vision left, rich text + mission/values right. --}}
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }} grid gap-12 lg:grid-cols-12 lg:gap-16">
        <div class="lg:col-span-5">
            @include('components.company.partials.heading', ['eyebrow' => 'Tentang Kami', 'title' => $section->title ?: 'Mengenal '.$company->name, 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            @if ($company->vision)
                <blockquote class="mt-10 border-l-2 border-primary pl-6" {!! $ds->reveal(1) !!}>
                    <p class="text-xs font-semibold tracking-[0.2em] text-primary uppercase">Visi</p>
                    <p class="heading mt-3 text-xl leading-snug">{{ $company->vision }}</p>
                </blockquote>
            @endif
        </div>
        <div class="lg:col-span-7">
            <div class="site-prose text-lead text-muted" {!! $ds->reveal(1) !!}>{!! $company->about ?: e($company->description) !!}</div>
            @if ($company->missionItems() || $company->valueItems())
                <div class="mt-10 grid gap-4 sm:grid-cols-2">
                    @foreach (array_slice($company->missionItems() ?: $company->valueItems(), 0, 4) as $i => $item)
                        <div class="{{ $ds->card('flex gap-3 p-5') }}" {!! $ds->reveal($i + 2) !!}>
                            <x-icon name="check-circle" class="mt-0.5 size-5 shrink-0 text-primary" />
                            <p class="text-sm text-ink">{{ $item }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
