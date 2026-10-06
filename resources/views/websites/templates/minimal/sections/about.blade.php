{{-- Minimal about: label column + text column, vision/mission as plain definition rows. --}}
<section id="about" class="mx-auto max-w-4xl px-6">
    <div class="grid gap-8 border-t border-neutral-200 py-16 md:grid-cols-4 md:py-24">
        <div>
            <p class="text-xs text-neutral-400 tabular-nums">({{ str_pad($index, 2, '0', STR_PAD_LEFT) }})</p>
            <h2 class="mt-1 text-sm font-medium text-neutral-950">Tentang</h2>
        </div>
        <div class="md:col-span-3">
            <p class="font-heading text-2xl leading-snug font-medium tracking-tight text-neutral-950 md:text-3xl">{{ $section->title ?: ($company->vision ?: 'Tentang '.$company->name) }}</p>
            @if ($section->subtitle)
                <p class="mt-4 text-neutral-500">{{ $section->subtitle }}</p>
            @endif
            <div class="site-prose mt-10 max-w-2xl text-[17px] text-neutral-600">{!! $company->about ?: e($company->description) !!}</div>

            <dl class="mt-14 divide-y divide-neutral-200 border-y border-neutral-200 text-sm">
                @if ($company->established_year)
                    <div class="grid gap-1 py-4 sm:grid-cols-3"><dt class="text-neutral-400">Berdiri</dt><dd class="text-neutral-900 sm:col-span-2">{{ $company->established_year }}</dd></div>
                @endif
                @if ($company->vision && $section->title)
                    <div class="grid gap-1 py-4 sm:grid-cols-3"><dt class="text-neutral-400">Visi</dt><dd class="text-neutral-900 sm:col-span-2">{{ $company->vision }}</dd></div>
                @endif
                @if ($company->missionItems())
                    <div class="grid gap-1 py-4 sm:grid-cols-3">
                        <dt class="text-neutral-400">Misi</dt>
                        <dd class="space-y-1.5 text-neutral-900 sm:col-span-2">
                            @foreach ($company->missionItems() as $mission)
                                <p><span class="mr-2 text-neutral-400 tabular-nums">{{ $loop->iteration }}.</span>{{ $mission }}</p>
                            @endforeach
                        </dd>
                    </div>
                @endif
                @if ($company->valueItems())
                    <div class="grid gap-1 py-4 sm:grid-cols-3">
                        <dt class="text-neutral-400">Nilai</dt>
                        <dd class="space-y-1.5 text-neutral-900 sm:col-span-2">
                            @foreach ($company->valueItems() as $value)
                                <p>{{ $value }}</p>
                            @endforeach
                        </dd>
                    </div>
                @endif
                @if ($company->history)
                    <div class="grid gap-1 py-4 sm:grid-cols-3"><dt class="text-neutral-400">Sejarah</dt><dd class="site-prose text-neutral-900 sm:col-span-2">{!! $company->history !!}</dd></div>
                @endif
            </dl>
        </div>
    </div>
</section>
