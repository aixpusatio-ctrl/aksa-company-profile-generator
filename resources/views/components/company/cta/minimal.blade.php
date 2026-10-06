{{-- CTA: Minimal — a giant typographic link to the contact section with an animated underline. --}}
<section id="cta" class="{{ $ds->section($tone, 'overflow-hidden') }}">
    <div class="{{ $ds->container() }}">
        <div {!! $ds->reveal() !!}>{!! $ds->eyebrow($section->subtitle ? 'Mulai Proyek' : 'Punya proyek?', $index) !!}</div>
        <a href="{{ $site->anchor('contact') }}" class="group mt-6 block text-ink" {!! $ds->reveal(1) !!}>
            <span class="heading inline bg-[linear-gradient(currentColor,currentColor)] bg-[length:0%_0.06em] bg-left-bottom bg-no-repeat pb-[0.05em] text-[clamp(2.75rem,9.5vw,8.5rem)] !leading-[1.02] transition-[background-size,color] duration-700 ease-out group-hover:bg-[length:100%_0.06em] group-hover:text-primary">{{ $section->title ?: 'Mari bekerja sama' }}</span>
            <span class="heading ml-2 inline-block text-[clamp(2.75rem,9.5vw,8.5rem)] !leading-[1.02] text-primary transition-transform duration-500 group-hover:translate-x-4" aria-hidden="true">→</span>
        </a>
        <div class="mt-12 flex flex-col gap-6 border-t border-line pt-8 sm:flex-row sm:items-start sm:justify-between" {!! $ds->reveal(2) !!}>
            <p class="max-w-md text-muted">{{ $section->subtitle ?: 'Ceritakan ide dan kebutuhan Anda. Kami akan membalas secepatnya.' }}</p>
            <div class="flex flex-col gap-2 text-sm sm:items-end">
                @if ($company->email)<a href="mailto:{{ $company->email }}" class="font-medium break-all text-ink hover:text-primary">{{ $company->email }}</a>@endif
                @if ($company->phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="text-muted hover:text-primary">{{ $company->phone }}</a>@endif
            </div>
        </div>
    </div>
</section>
