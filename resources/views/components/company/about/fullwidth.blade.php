{{-- About: Full Width — full-bleed photo with dark overlay, glass text card (about + vision) and a stats row at the bottom. --}}
@php
    $image = $company->gallery->first()?->url('image') ?: $company->url('hero_image');
    $stats = array_slice($company->stats(), 0, 4);
@endphp
<section id="about" class="relative isolate overflow-hidden bg-black py-section text-white">
    <div class="absolute inset-0 -z-10">
        <x-site.img :src="$image" alt="" icon="building" class="size-full scale-110 object-cover opacity-70" data-parallax="0.08" />
        <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/60 to-black/25"></div>
        <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/80 to-transparent"></div>
    </div>

    <div class="{{ $ds->container() }}">
        <div class="max-w-2xl" {!! $ds->reveal(0, 'left') !!}>
            <p class="inline-flex items-center gap-3 text-xs font-semibold tracking-[0.22em] text-white/80 uppercase">
                <span class="h-px w-10 bg-primary"></span>Tentang Kami
            </p>
            <h2 class="heading mt-5 text-h2 text-white">{{ $section->title ?: 'Mengenal '.$company->name }}</h2>
            @if ($section->subtitle)
                <p class="mt-5 text-lead text-white/80">{{ $section->subtitle }}</p>
            @endif

            <div class="mt-8 rounded-brand border border-white/15 bg-white/10 p-6 backdrop-blur-md sm:p-8">
                <div class="site-prose text-white/85 [&_a]:text-white">{!! $company->about ?: e($company->description) !!}</div>
                @if ($company->vision)
                    <div class="mt-7 border-t border-white/15 pt-6">
                        <p class="text-xs font-semibold tracking-[0.2em] text-white/60 uppercase">Visi</p>
                        <p class="heading mt-2 text-xl leading-snug text-white">{{ $company->vision }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'light'])
            </div>
        </div>

        @if ($stats)
            <dl class="mt-16 grid grid-cols-2 gap-y-8 border-t border-white/20 pt-10 sm:mt-24 lg:grid-cols-4">
                @foreach ($stats as $i => $stat)
                    <div class="pr-6 {{ $i > 0 ? 'lg:border-l lg:border-white/20 lg:pl-8' : '' }}" {!! $ds->reveal($i) !!}>
                        <dd class="heading text-4xl text-white sm:text-5xl" data-count>{{ $stat['value'] }}</dd>
                        <dt class="mt-2 text-sm text-white/70">{{ $stat['label'] }}</dt>
                    </div>
                @endforeach
            </dl>
        @endif
    </div>
</section>
