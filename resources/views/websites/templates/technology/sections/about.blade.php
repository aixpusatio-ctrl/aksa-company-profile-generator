{{-- Technology about: narrative + mono "spec sheet", mission as code list, values as chips. --}}
<section id="about" class="relative border-y border-white/10 bg-slate-900/40 py-24 lg:py-32">
    <div class="absolute inset-0 bg-[radial-gradient(rgb(255_255_255/0.06)_1px,transparent_1px)] bg-[size:22px_22px] [mask-image:radial-gradient(ellipse_at_center,black,transparent_75%)]"></div>
    <div class="relative mx-auto grid max-w-7xl gap-14 px-5 sm:px-6 lg:grid-cols-12">
        <div class="lg:col-span-7">
            <p class="font-mono text-sm text-primary">// tentang</p>
            <h2 class="mt-4 font-heading text-3xl font-bold tracking-tight text-white sm:text-4xl lg:text-5xl">{{ $section->title ?: 'Kami membangun teknologi yang bekerja untuk manusia' }}</h2>
            @if ($section->subtitle)<p class="mt-5 text-lg text-slate-300">{{ $section->subtitle }}</p>@endif
            <div class="site-prose mt-8 text-slate-400 [&_strong]:text-white">{!! $company->about ?: e($company->description) !!}</div>

            @if ($company->vision)
                <div class="mt-10 rounded-brand border border-primary/20 bg-primary/5 p-6">
                    <p class="font-mono text-xs text-primary">const visi =</p>
                    <p class="mt-2 font-heading text-lg leading-snug text-white sm:text-xl">“{{ $company->vision }}”</p>
                </div>
            @endif
        </div>

        <div class="space-y-6 lg:col-span-5">
            {{-- spec sheet --}}
            <div class="overflow-hidden rounded-brand border border-white/10 bg-slate-950/80">
                <div class="flex items-center justify-between border-b border-white/10 px-5 py-3">
                    <span class="font-mono text-xs text-slate-500">company.yml</span>
                    <x-icon name="code" class="size-4 text-slate-600" />
                </div>
                <dl class="divide-y divide-white/5 font-mono text-sm">
                    @foreach (array_filter([
                        'name' => $company->name,
                        'founded' => $company->established_year,
                        'hq' => $company->city,
                        'services' => $company->services->count() ?: null,
                        'team' => $company->team->count() ? $company->team->count().' leaders' : null,
                        'hours' => $company->working_hours,
                    ]) as $key => $value)
                        <div class="flex gap-4 px-5 py-3">
                            <dt class="w-24 shrink-0 text-secondary">{{ $key }}:</dt>
                            <dd class="min-w-0 text-slate-200">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            @if ($company->missionItems())
                <div class="rounded-brand border border-white/10 bg-slate-950/80 p-5">
                    <p class="font-mono text-xs text-slate-500">// misi</p>
                    <ol class="mt-4 space-y-3">
                        @foreach ($company->missionItems() as $mission)
                            <li class="flex gap-3 text-sm text-slate-300"><span class="font-mono text-xs leading-6 text-primary">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span> <span>{{ $mission }}</span></li>
                        @endforeach
                    </ol>
                </div>
            @endif

            @if ($company->valueItems())
                <div class="flex flex-wrap gap-2">
                    @foreach ($company->valueItems() as $value)
                        <span class="rounded-full border border-white/10 bg-white/[0.03] px-3 py-1.5 text-xs text-slate-300">
                            <span class="text-primary">#</span>{{ \Illuminate\Support\Str::before($value, ' —') ?: $value }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
