{{-- Consulting about: editorial two-column with pull-quote vision and roman-numbered values. --}}
<section id="about" class="border-t border-stone-200 bg-white py-24 lg:py-32">
    <div class="mx-auto max-w-7xl px-6">
        <div class="grid gap-14 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Tentang Kami</p>
                <h2 class="mt-6 font-heading text-4xl leading-tight text-stone-900 md:text-5xl">{{ $section->title ?: 'Mitra berpikir untuk para pemimpin.' }}</h2>
                @if ($section->subtitle)<p class="mt-6 text-stone-500">{{ $section->subtitle }}</p>@endif
                @if ($company->established_year)
                    <div class="mt-12 border-t border-stone-200 pt-6">
                        <p class="font-heading text-6xl text-primary">{{ date('Y') - $company->established_year }}<span class="text-3xl">+</span></p>
                        <p class="mt-2 text-sm text-stone-500">tahun mendampingi organisasi di Indonesia</p>
                    </div>
                @endif
            </div>
            <div class="lg:col-span-7 lg:col-start-6">
                <div class="site-prose text-lg text-stone-600 first-letter:float-left first-letter:mr-3 first-letter:font-heading first-letter:text-7xl first-letter:leading-[0.8] first-letter:text-stone-900">{!! $company->about ?: e($company->description) !!}</div>

                @if ($company->vision)
                    <blockquote class="mt-14 border-l border-primary pl-8">
                        <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Visi</p>
                        <p class="mt-4 font-heading text-2xl leading-snug text-stone-900 italic md:text-3xl">“{{ $company->vision }}”</p>
                    </blockquote>
                @endif

                @if ($company->missionItems())
                    <div class="mt-14">
                        <p class="text-xs tracking-[0.3em] text-stone-400 uppercase">Misi</p>
                        <ol class="mt-4 divide-y divide-stone-200 border-y border-stone-200">
                            @foreach ($company->missionItems() as $mission)
                                <li class="flex gap-6 py-4"><span class="w-8 shrink-0 font-heading text-primary italic">{{ ['i','ii','iii','iv','v','vi','vii','viii','ix','x'][$loop->index] ?? $loop->iteration }}.</span> <span>{{ $mission }}</span></li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                @if ($company->valueItems())
                    <div class="mt-14 grid gap-px border border-stone-200 bg-stone-200 sm:grid-cols-2">
                        @foreach ($company->valueItems() as $value)
                            <div class="bg-white p-6 text-sm leading-relaxed">{{ $value }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
