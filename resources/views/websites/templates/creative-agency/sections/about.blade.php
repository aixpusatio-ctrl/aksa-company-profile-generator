{{-- Creative about: bright primary block with oversized statement, stickers and stats. --}}
<section id="about" class="px-3 py-20 sm:px-5 lg:py-28">
    <div class="relative mx-auto max-w-[90rem] overflow-hidden rounded-[2rem] bg-primary px-6 py-16 text-on-primary sm:px-10 lg:py-24">
        <span class="absolute -top-20 -right-20 size-72 rounded-full border-[3rem] border-black/10"></span>
        <div class="relative grid gap-12 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="inline-flex rotate-[-3deg] rounded-full bg-neutral-950 px-4 py-1.5 text-sm font-extrabold text-white">Hai, kami {{ \Illuminate\Support\Str::of($company->name)->replace(['PT ', 'CV '], '') }} 👋</p>
                <h2 class="mt-8 font-heading text-4xl leading-[0.95] font-extrabold tracking-tighter md:text-6xl lg:text-7xl">{{ $section->title ?: 'Kami membuat brand yang mustahil diabaikan.' }}</h2>
                @if ($section->subtitle)<p class="mt-6 text-lg opacity-85">{{ $section->subtitle }}</p>@endif
            </div>
            <div class="lg:col-span-5 lg:pt-14">
                <div class="site-prose text-lg opacity-90 [&_a]:text-current">{!! $company->about ?: e($company->description) !!}</div>
            </div>
        </div>

        <div class="relative mt-16 grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach ([
                [$company->established_year ? date('Y') - $company->established_year.'+' : '10+', 'tahun berkarya', 'rotate-2'],
                [max($company->projects->count() * 20, 50).'+', 'proyek diluncurkan', '-rotate-1'],
                [$company->team->count() ?: '12', 'kepala kreatif', 'rotate-1'],
                [$company->services->count() ?: '6', 'disiplin layanan', '-rotate-2'],
            ] as [$num, $label, $rot])
                <div class="rounded-3xl bg-white p-6 text-neutral-950 transition hover:rotate-0 {{ $rot }}">
                    <p class="font-heading text-5xl font-extrabold tracking-tighter md:text-6xl">{{ $num }}</p>
                    <p class="mt-2 text-sm font-semibold text-neutral-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        @if ($company->vision || $company->valueItems())
            <div class="relative mt-16 grid gap-8 border-t border-current/20 pt-10 lg:grid-cols-2">
                @if ($company->vision)
                    <div>
                        <p class="text-sm font-extrabold tracking-widest uppercase opacity-70">Visi kami</p>
                        <p class="mt-3 font-heading text-2xl leading-snug font-bold md:text-3xl">{{ $company->vision }}</p>
                    </div>
                @endif
                @if ($company->valueItems())
                    <div class="flex flex-wrap content-start gap-2">
                        @foreach ($company->valueItems() as $value)
                            <span class="rounded-full border-2 border-current px-4 py-2 text-sm font-bold {{ $loop->odd ? 'rotate-1' : '-rotate-1' }}">{{ \Illuminate\Support\Str::before($value, ' — ') }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
