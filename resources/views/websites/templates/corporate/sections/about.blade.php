{{-- Corporate about: image left, text right, vision & mission tabs, values list. --}}
<section id="about" class="py-20 lg:py-28">
    <div class="mx-auto grid max-w-7xl gap-16 px-6 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <div class="grid grid-cols-2 gap-4">
                <x-site.img :src="$company->gallery->first()?->url('image') ?? $company->url('hero_image')" :alt="$company->name" class="col-span-2 aspect-[16/10] w-full rounded-brand object-cover" />
                <x-site.img :src="$company->gallery->get(1)?->url('image')" alt="" class="aspect-square w-full rounded-brand object-cover" />
                <div class="flex aspect-square flex-col justify-center rounded-brand bg-primary p-6 text-on-primary">
                    <span class="font-heading text-4xl font-extrabold">{{ $company->established_year ?: '—' }}</span>
                    <span class="mt-1 text-sm opacity-80">Tahun berdiri</span>
                </div>
            </div>
        </div>
        <div class="lg:col-span-7" x-data="{ tab: 'about' }">
            <p class="text-sm font-bold tracking-widest text-primary uppercase">Tentang Kami</p>
            <h2 class="mt-3 font-heading text-3xl font-extrabold text-slate-900 md:text-4xl">{{ $section->title ?: 'Mengenal '.$company->name }}</h2>
            @if ($section->subtitle)
                <p class="mt-4 text-lg text-slate-600">{{ $section->subtitle }}</p>
            @endif

            <div class="mt-8 flex flex-wrap gap-2 border-b border-slate-200">
                @foreach (['about' => 'Profil', 'vision' => 'Visi & Misi', 'history' => 'Sejarah', 'values' => 'Nilai'] as $key => $label)
                    @if ($key === 'about' || ($key === 'vision' && ($company->vision || $company->mission)) || ($key === 'history' && $company->history) || ($key === 'values' && $company->company_values))
                        <button type="button" @click="tab = '{{ $key }}'" class="-mb-px border-b-2 px-4 py-2.5 text-sm font-semibold transition" :class="tab === '{{ $key }}' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800'">{{ $label }}</button>
                    @endif
                @endforeach
            </div>

            <div class="mt-6 text-slate-600">
                <div x-show="tab === 'about'" class="site-prose">{!! $company->about ?: e($company->description) !!}</div>
                <div x-cloak x-show="tab === 'vision'" class="space-y-6">
                    @if ($company->vision)
                        <div class="rounded-brand border-l-4 border-primary bg-slate-50 p-6">
                            <p class="text-xs font-bold tracking-widest text-primary uppercase">Visi</p>
                            <p class="mt-2 font-heading text-lg font-semibold text-slate-900">{{ $company->vision }}</p>
                        </div>
                    @endif
                    @if ($company->missionItems())
                        <ul class="space-y-3">
                            @foreach ($company->missionItems() as $mission)
                                <li class="flex gap-3"><x-icon name="check-circle" class="mt-0.5 size-5 shrink-0 text-primary" /> <span>{{ $mission }}</span></li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <div x-cloak x-show="tab === 'history'" class="site-prose">{!! $company->history !!}</div>
                <div x-cloak x-show="tab === 'values'" class="grid gap-4 sm:grid-cols-2">
                    @foreach ($company->valueItems() as $value)
                        <div class="flex gap-3 rounded-brand border border-slate-200 p-4"><x-icon name="sparkles" class="size-5 shrink-0 text-primary" /> <span class="text-sm">{{ $value }}</span></div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
