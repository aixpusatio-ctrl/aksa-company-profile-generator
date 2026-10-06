{{-- Hero: Split Screen — headline & CTAs left, framed image right, stats strip. --}}
@php($stats = array_slice($company->stats(), 0, 3))
<section id="hero" class="relative overflow-hidden bg-surface">
    <div class="absolute -top-32 -right-32 size-[30rem] rounded-full bg-primary/10 blur-3xl"></div>
    <div class="{{ $ds->container() }} relative grid items-center gap-12 pt-28 pb-16 lg:grid-cols-12 lg:gap-16 lg:pt-36 lg:pb-24">
        <div class="lg:col-span-6">
            <div {!! $ds->reveal(0) !!}>{!! $ds->eyebrow($company->established_year ? 'Sejak '.$company->established_year : ($company->city ?: 'Profil Perusahaan')) !!}</div>
            <h1 class="heading mt-6 text-[min(var(--display),4.25rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $section->title ?: ($company->tagline ?: $company->name) }}</h1>
            <p class="mt-6 max-w-xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $section->subtitle ?: $company->description }}</p>
            <div class="mt-9 flex flex-wrap gap-3" {!! $ds->reveal(3) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Konsultasi Sekarang', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('services'), 'label' => 'Lihat Layanan', 'kind' => 'secondary'])
            </div>
            @if ($stats)
                <dl class="mt-12 grid max-w-lg grid-cols-3 gap-6 border-t border-line pt-8" {!! $ds->reveal(4) !!}>
                    @foreach ($stats as $stat)
                        <div>
                            <dd class="heading text-3xl" data-count>{{ $stat['value'] }}</dd>
                            <dt class="mt-1 text-xs text-muted">{{ $stat['label'] }}</dt>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
        <div class="relative lg:col-span-6" {!! $ds->reveal(2, 'right') !!}>
            <x-site.img :src="$company->url('hero_image')" :alt="$company->name" icon="building" class="{{ $ds->img('aspect-[4/3] w-full object-cover shadow-2xl shadow-black/10 lg:aspect-[5/4]') }}" />
            @if ($company->services->isNotEmpty())
                <div class="{{ $ds->card('absolute -bottom-6 left-4 hidden max-w-xs p-5 sm:block lg:left-6', false) }}">
                    <p class="text-xs font-semibold tracking-wide text-primary uppercase">Layanan utama</p>
                    <p class="mt-1 font-semibold text-ink">{{ $company->services->first()->title }}</p>
                    <p class="mt-1 line-clamp-2 text-sm text-muted">{{ $company->services->first()->description }}</p>
                </div>
            @endif
        </div>
    </div>
</section>
