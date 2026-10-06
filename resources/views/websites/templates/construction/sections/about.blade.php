{{-- Construction about: offset image stack with experience block, mission checklist, safety & quality badges. --}}
@php($years = $company->established_year ? max(date('Y') - $company->established_year, 1) : null)
<section id="about" class="relative overflow-hidden bg-white py-20 lg:py-32">
    <div class="absolute top-0 left-0 font-heading text-[18rem] leading-none font-bold text-stone-100 uppercase select-none" aria-hidden="true">{{ $years ?: '' }}</div>
    <div class="relative mx-auto grid max-w-7xl gap-16 px-5 sm:px-6 lg:grid-cols-2 lg:gap-20">
        <div class="relative pr-8 pb-8 sm:pr-16 sm:pb-16">
            <x-site.img :src="$company->gallery->first()?->url('image') ?? $company->url('hero_image')" :alt="$company->name" class="aspect-[4/5] w-full object-cover sm:w-4/5" />
            <x-site.img :src="$company->gallery->get(1)?->url('image')" alt="" class="absolute right-0 bottom-0 hidden aspect-square w-1/2 border-8 border-white object-cover sm:block" />
            <div class="absolute top-8 -right-0 bg-primary p-6 text-on-primary sm:right-4 sm:top-12 sm:p-8">
                <p class="font-heading text-6xl leading-none font-bold sm:text-7xl">{{ $years ?: '—' }}</p>
                <p class="mt-2 max-w-[8rem] font-heading text-xs font-semibold tracking-[0.2em] uppercase">Tahun membangun negeri</p>
            </div>
        </div>
        <div class="lg:pt-6">
            <p class="flex items-center gap-4 font-heading text-sm font-semibold tracking-[0.3em] text-primary uppercase"><span class="h-1 w-10 bg-primary"></span> Tentang Kami</p>
            <h2 class="mt-5 font-heading text-4xl leading-[0.95] font-bold tracking-tight text-stone-950 uppercase sm:text-5xl lg:text-6xl">{{ $section->title ?: 'Membangun dengan Presisi & Integritas' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-lg text-stone-500">{{ $section->subtitle }}</p>@endif
            <div class="site-prose mt-7 text-stone-600">{!! $company->about ?: e($company->description) !!}</div>

            @if ($company->vision)
                <div class="mt-8 bg-stone-950 p-6 text-white">
                    <p class="font-heading text-xs font-semibold tracking-[0.3em] text-primary uppercase">Visi</p>
                    <p class="mt-2 font-heading text-xl leading-snug font-medium uppercase">{{ $company->vision }}</p>
                </div>
            @endif

            @if ($company->missionItems())
                <ul class="mt-8 space-y-3">
                    @foreach ($company->missionItems() as $mission)
                        <li class="flex gap-4"><span class="mt-0.5 inline-flex size-6 shrink-0 items-center justify-center bg-primary text-on-primary"><x-icon name="check" class="size-4" /></span> <span class="text-stone-700">{{ $mission }}</span></li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Safety & quality badges --}}
    <div class="relative mx-auto mt-20 max-w-7xl px-5 sm:px-6">
        <div class="grid border-2 border-stone-950 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['shield', 'Keselamatan K3', 'Zero accident sebagai target di setiap lokasi proyek.'],
                ['award', 'Kontrol Mutu', 'Inspeksi bertahap & material sesuai spesifikasi.'],
                ['clock', 'Tepat Waktu', 'Penjadwalan terukur dengan laporan progres rutin.'],
                ['check-circle', 'Bergaransi', 'Masa pemeliharaan & dukungan purna proyek.'],
            ] as [$icon, $title, $text])
                <div class="group flex gap-4 border-stone-950 p-6 transition hover:bg-stone-950 {{ $loop->last ? '' : 'max-sm:border-b-2' }} sm:max-lg:[&:nth-child(-n+2)]:border-b-2 sm:max-lg:odd:border-r-2 lg:border-r-2 lg:last:border-r-0">
                    <span class="inline-flex size-14 shrink-0 items-center justify-center bg-primary text-on-primary"><x-icon :name="$icon" class="size-7" /></span>
                    <div>
                        <p class="font-heading text-lg font-bold tracking-wide text-stone-950 uppercase transition group-hover:text-white">{{ $title }}</p>
                        <p class="mt-1 text-sm text-stone-500 transition group-hover:text-stone-400">{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
