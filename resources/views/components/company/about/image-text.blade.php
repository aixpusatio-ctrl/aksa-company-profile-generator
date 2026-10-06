{{-- About: Image + Text — large photo with overlapping inset & "Sejak" badge, text with mission checklist and CTA. --}}
@php
    $mainImage = $company->gallery->first()?->url('image') ?: $company->url('hero_image');
    $insetImage = $company->gallery->skip(1)->first()?->url('image') ?: $company->projects->first()?->url('image');
    $checklist = array_slice($company->missionItems() ?: $company->valueItems(), 0, 4);
@endphp
<section id="about" class="{{ $ds->section($tone, 'overflow-hidden') }}">
    <div class="{{ $ds->container() }} grid items-center gap-16 lg:grid-cols-2 lg:gap-20">
        <div class="relative pb-10 pl-3 sm:pr-14 sm:pb-14 sm:pl-6">
            <div {!! $ds->reveal(0) !!}>
                <x-site.img :src="$mainImage" :alt="'Tentang '.$company->name" icon="building" class="{{ $ds->img('aspect-[4/5] w-full object-cover sm:aspect-[5/6]') }}" />
            </div>
            @if ($insetImage)
                <div class="absolute right-0 bottom-0 w-[46%] rounded-brand bg-surface p-2 shadow-2xl shadow-black/15 ring-1 ring-line sm:p-2.5">
                    <x-site.img :src="$insetImage" :alt="$company->name" class="{{ $ds->img('aspect-square w-full object-cover') }}" />
                </div>
            @endif
            @if ($company->established_year)
                <div class="absolute top-6 left-0 flex size-28 flex-col items-center justify-center rounded-full bg-primary text-center text-on-primary shadow-xl shadow-primary/30 sm:top-8 sm:left-0 sm:size-32">
                    <span class="text-[0.65rem] font-semibold tracking-[0.2em] uppercase opacity-80">Sejak</span>
                    <span class="heading text-3xl sm:text-4xl">{{ $company->established_year }}</span>
                </div>
            @endif
        </div>

        <div>
            @include('components.company.partials.heading', ['eyebrow' => 'Tentang Kami', 'title' => $section->title ?: 'Mengenal '.$company->name, 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            <div class="site-prose mt-8 text-muted" {!! $ds->reveal(1) !!}>{!! $company->about ?: e($company->description) !!}</div>

            @if ($checklist)
                <ul class="mt-9 space-y-4 border-t border-line pt-8">
                    @foreach ($checklist as $i => $item)
                        <li class="flex items-start gap-4" {!! $ds->reveal($i + 2) !!}>
                            <span class="mt-0.5 inline-flex size-7 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <x-icon name="check" class="size-4" />
                            </span>
                            <span class="text-ink">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div class="mt-10 flex flex-wrap items-center gap-x-6 gap-y-4" {!! $ds->reveal(6) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'primary'])
                @if ($company->services->isNotEmpty())
                    @include('components.company.partials.button', ['href' => $site->anchor('services'), 'label' => 'Lihat Layanan', 'kind' => 'link'])
                @endif
            </div>
        </div>
    </div>
</section>
