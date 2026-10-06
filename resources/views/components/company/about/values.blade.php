{{-- About: Values — story intro followed by numbered value cards with icons and the mission list. --}}
@php
    $icons = ['shield', 'award', 'users', 'heart', 'light-bulb', 'leaf', 'scale', 'rocket'];
    $values = collect($company->valueItems())->take(8)->map(function ($v) {
        $parts = preg_split('/\s+[—–-]\s+|:\s+/u', $v, 2);

        return count($parts) === 2 && mb_strlen($parts[0]) <= 40
            ? ['title' => trim($parts[0]), 'text' => \Illuminate\Support\Str::ucfirst(trim($parts[1]))]
            : ['title' => $v, 'text' => null];
    });
    $mission = $company->missionItems();
@endphp
<section id="about" class="{{ $ds->section($tone) }}">
    <div class="{{ $ds->container() }}">
        <div class="grid gap-10 lg:grid-cols-12 lg:gap-16">
            <div class="lg:col-span-5">
                @include('components.company.partials.heading', ['eyebrow' => 'Tentang Kami', 'title' => $section->title ?: 'Nilai yang menggerakkan kami', 'subtitle' => $section->subtitle, 'align' => 'left', 'number' => $index])
            </div>
            <div class="lg:col-span-7 lg:pt-10">
                <div class="site-prose text-lead text-muted" {!! $ds->reveal(1) !!}>{!! $company->about ?: e($company->description) !!}</div>
            </div>
        </div>

        @if ($values->isNotEmpty())
            <div class="mt-16 grid gap-5 sm:grid-cols-2 {{ $values->count() % 3 === 0 || $values->count() === 5 ? 'lg:grid-cols-3' : 'lg:grid-cols-4' }}">
                @foreach ($values as $i => $value)
                    <article class="{{ $ds->card('group relative flex flex-col p-7') }}" {!! $ds->reveal($i) !!}>
                        <div class="flex items-start justify-between gap-4">
                            <span class="inline-flex size-12 items-center justify-center rounded-brand bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-on-primary">
                                <x-icon :name="$icons[$i % count($icons)]" class="size-6" />
                            </span>
                            <span class="heading text-3xl text-ink/15">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h3 class="heading mt-8 text-h3">{{ $value['title'] }}</h3>
                        @if ($value['text'])
                            <p class="mt-3 text-sm leading-relaxed text-muted">{{ $value['text'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif

        @if ($mission)
            <div class="mt-16 grid gap-8 border-t border-line pt-12 lg:grid-cols-12 lg:gap-16">
                <div class="lg:col-span-4" {!! $ds->reveal(0) !!}>
                    <h3 class="heading text-2xl">Misi kami</h3>
                    @if ($company->vision)
                        <p class="mt-4 text-sm text-muted"><span class="font-semibold text-ink">Visi:</span> {{ $company->vision }}</p>
                    @endif
                </div>
                <ul class="grid gap-x-10 gap-y-5 sm:grid-cols-2 lg:col-span-8">
                    @foreach ($mission as $i => $item)
                        <li class="flex gap-3" {!! $ds->reveal($i + 1) !!}>
                            <x-icon name="check-circle" class="mt-0.5 size-5 shrink-0 text-primary" />
                            <span class="text-ink">{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>
