{{-- Hero: Globe — global presence: headline left, dotted world map with pins for the HQ city and project locations. --}}
@php
    $title = $section->title ?: ($company->tagline ?: $company->name);
    $lead = $section->subtitle ?: $company->description;
    $stats = array_slice($company->stats(), 0, 4);

    // Rough world land mask: [row => [[fromCol, toCol], ...]] on a 72x27 grid (5° cells, lat 80..-50).
    $land = [
        1 => [[14, 22], [26, 31], [52, 60]],
        2 => [[3, 10], [12, 24], [26, 31], [36, 40], [44, 68]],
        3 => [[2, 22], [27, 30], [35, 42], [44, 70]],
        4 => [[4, 20], [23, 25], [34, 68]],
        5 => [[8, 22], [33, 66]],
        6 => [[9, 22], [33, 64], [66, 67]],
        7 => [[10, 23], [34, 45], [46, 63], [64, 65]],
        8 => [[11, 22], [34, 44], [46, 62], [63, 64]],
        9 => [[12, 20], [34, 46], [47, 61]],
        10 => [[13, 19], [33, 47], [48, 60]],
        11 => [[15, 18], [32, 47], [50, 58]],
        12 => [[16, 18], [32, 46], [51, 54], [56, 58]],
        13 => [[17, 19], [32, 45], [52, 53], [56, 58]],
        14 => [[19, 24], [32, 45], [56, 57]],
        15 => [[21, 26], [34, 45], [55, 58], [60, 62]],
        16 => [[21, 27], [38, 45], [56, 59], [60, 62], [63, 64]],
        17 => [[21, 28], [39, 45], [57, 59], [61, 63], [64, 66]],
        18 => [[22, 28], [39, 45], [58, 62], [63, 66]],
        19 => [[22, 28], [40, 46], [60, 65]],
        20 => [[23, 28], [40, 46], [59, 66]],
        21 => [[24, 27], [40, 43], [59, 66]],
        22 => [[24, 27], [41, 43], [59, 66]],
        23 => [[24, 26], [60, 65], [69, 70]],
        24 => [[24, 26], [64, 65], [69, 70]],
        25 => [[24, 25], [69, 70]],
        26 => [[24, 25]],
    ];

    // Approximate coordinates [lon, lat] of common cities, matched by name.
    $places = [
        'jakarta' => [106.8, -6.2], 'tangerang' => [106.6, -6.2], 'bekasi' => [107.0, -6.2], 'bogor' => [106.8, -6.6], 'depok' => [106.8, -6.4],
        'cikarang' => [107.2, -6.3], 'karawang' => [107.3, -6.3], 'cilegon' => [106.0, -6.0], 'bandung' => [107.6, -6.9], 'cirebon' => [108.6, -6.7],
        'semarang' => [110.4, -7.0], 'yogyakarta' => [110.4, -7.8], 'jogja' => [110.4, -7.8], 'solo' => [110.8, -7.6], 'surakarta' => [110.8, -7.6],
        'surabaya' => [112.8, -7.3], 'gresik' => [112.6, -7.2], 'sidoarjo' => [112.7, -7.4], 'malang' => [112.6, -8.0], 'bali' => [115.2, -8.4],
        'denpasar' => [115.2, -8.7], 'lombok' => [116.3, -8.6], 'mataram' => [116.1, -8.6], 'kupang' => [123.6, -10.2], 'medan' => [98.7, 3.6],
        'aceh' => [95.3, 5.5], 'padang' => [100.4, -0.9], 'pekanbaru' => [101.4, 0.5], 'riau' => [101.4, 0.5], 'batam' => [104.0, 1.1],
        'jambi' => [103.6, -1.6], 'palembang' => [104.8, -3.0], 'lampung' => [105.3, -5.4], 'pontianak' => [109.3, 0.0], 'banjarmasin' => [114.6, -3.3],
        'balikpapan' => [116.8, -1.2], 'samarinda' => [117.1, -0.5], 'ikn' => [116.7, -0.9], 'nusantara' => [116.7, -0.9], 'kalimantan' => [114.0, -1.0],
        'makassar' => [119.4, -5.1], 'manado' => [124.8, 1.5], 'palu' => [119.9, -0.9], 'kendari' => [122.5, -4.0], 'sulawesi' => [120.5, -2.0],
        'ambon' => [128.2, -3.7], 'ternate' => [127.4, 0.8], 'sorong' => [131.3, -0.9], 'jayapura' => [140.7, -2.5], 'papua' => [138.0, -4.0],
        'timika' => [136.9, -4.5], 'singapore' => [103.8, 1.3], 'singapura' => [103.8, 1.3], 'kuala lumpur' => [101.7, 3.1], 'malaysia' => [101.7, 3.1],
        'bangkok' => [100.5, 13.8], 'manila' => [121.0, 14.6], 'ho chi minh' => [106.7, 10.8], 'hanoi' => [105.8, 21.0], 'vietnam' => [106.0, 16.0],
        'hong kong' => [114.2, 22.3], 'shanghai' => [121.5, 31.2], 'beijing' => [116.4, 39.9], 'tokyo' => [139.7, 35.7], 'jepang' => [139.7, 35.7],
        'seoul' => [127.0, 37.6], 'sydney' => [151.2, -33.9], 'melbourne' => [145.0, -37.8], 'perth' => [115.9, -32.0], 'australia' => [134.0, -25.0],
        'dubai' => [55.3, 25.2], 'riyadh' => [46.7, 24.7], 'jeddah' => [39.2, 21.5], 'mumbai' => [72.9, 19.1], 'delhi' => [77.2, 28.6],
        'istanbul' => [29.0, 41.0], 'cairo' => [31.2, 30.0], 'nairobi' => [36.8, -1.3], 'johannesburg' => [28.0, -26.2], 'london' => [-0.1, 51.5],
        'paris' => [2.4, 48.9], 'amsterdam' => [4.9, 52.4], 'rotterdam' => [4.5, 51.9], 'frankfurt' => [8.7, 50.1], 'berlin' => [13.4, 52.5],
        'new york' => [-74.0, 40.7], 'san francisco' => [-122.4, 37.8], 'los angeles' => [-118.2, 34.1], 'toronto' => [-79.4, 43.7], 'sao paulo' => [-46.6, -23.5],
    ];
    $locate = function (?string $name) use ($places) {
        $key = mb_strtolower((string) $name);
        foreach ($places as $place => $coords) {
            if ($key !== '' && str_contains($key, $place)) {
                return ['x' => ($coords[0] + 180) / 5 * 2 + 1, 'y' => (80 - $coords[1]) / 5 * 2 + 1];
            }
        }

        return null;
    };

    $hq = $locate($company->city) ?? $locate($company->province) ?? $locate($company->country) ?? $locate('jakarta');
    $locations = $company->projects->pluck('location')->filter()->map(fn ($l) => trim($l))->unique()
        ->reject(fn ($l) => $company->city && mb_strtolower($l) === mb_strtolower($company->city))->values();
    $pins = $locations->map(fn ($l) => ['label' => $l, 'pos' => $locate($l)])->filter(fn ($p) => $p['pos'])->take(10)->values();
    $hqLabel = $company->city ?: ($company->country ?: 'Kantor pusat');
@endphp
<section id="hero" class="relative isolate overflow-hidden bg-surface">
    <div class="pointer-events-none absolute top-1/3 right-0 -z-10 size-[40rem] rounded-full bg-primary/10 blur-3xl" aria-hidden="true"></div>
    <div class="{{ $ds->container('wide') }} grid items-center gap-12 pt-28 pb-16 lg:grid-cols-12 lg:gap-8 lg:pt-36 lg:pb-24">
        <div class="lg:col-span-5">
            <div {!! $ds->reveal(0) !!}>{!! $ds->eyebrow($locations->count() ? 'Jejak di '.($locations->count() + 1).' lokasi' : 'Jangkauan Global') !!}</div>
            <h1 class="heading mt-6 text-[min(var(--display),4.5rem)] max-sm:text-[min(var(--display),10vw)]" {!! $ds->reveal(1) !!}>{{ $title }}</h1>
            @if ($lead)
                <p class="mt-6 max-w-xl text-lead text-muted" {!! $ds->reveal(2) !!}>{{ $lead }}</p>
            @endif
            <div class="mt-9 flex flex-col gap-3 sm:flex-row sm:flex-wrap" {!! $ds->reveal(3) !!}>
                @include('components.company.partials.button', ['href' => $site->anchor('contact'), 'label' => 'Hubungi Kami', 'kind' => 'primary'])
                @include('components.company.partials.button', ['href' => $site->anchor('projects'), 'label' => 'Jejak Proyek', 'kind' => 'secondary'])
            </div>
        </div>

        <div class="relative lg:col-span-7" {!! $ds->reveal(2, 'right') !!}>
            <div class="relative">
            <svg viewBox="0 0 144 54" class="w-full" role="img" aria-label="Peta lokasi {{ $company->name }}">
                <g class="fill-ink/25">
                    @foreach ($land as $row => $spans)
                        @foreach ($spans as [$from, $to])
                            @for ($c = $from; $c <= $to; $c++)
                                <circle cx="{{ $c * 2 + 1 }}" cy="{{ $row * 2 + 1 }}" r=".62" />
                            @endfor
                        @endforeach
                    @endforeach
                </g>
                @foreach ($pins as $pin)
                    <line x1="{{ $hq['x'] }}" y1="{{ $hq['y'] }}" x2="{{ $pin['pos']['x'] }}" y2="{{ $pin['pos']['y'] }}" class="stroke-primary/40" stroke-width=".25" stroke-dasharray=".8 .6" />
                @endforeach
                @foreach ($pins as $pin)
                    <circle cx="{{ $pin['pos']['x'] }}" cy="{{ $pin['pos']['y'] }}" r=".9" class="fill-secondary stroke-surface" stroke-width=".3"><title>{{ $pin['label'] }}</title></circle>
                @endforeach
                <circle cx="{{ $hq['x'] }}" cy="{{ $hq['y'] }}" r="3.2" class="fill-primary/20"><animate attributeName="r" values="1.5;4.5;1.5" dur="3s" repeatCount="indefinite" /><animate attributeName="opacity" values="1;0;1" dur="3s" repeatCount="indefinite" /></circle>
                <circle cx="{{ $hq['x'] }}" cy="{{ $hq['y'] }}" r="1.3" class="fill-primary stroke-surface" stroke-width=".4"><title>{{ $hqLabel }}</title></circle>
            </svg>
            <div class="absolute flex items-center gap-2 rounded-full border border-line bg-card px-3 py-1.5 text-xs font-semibold whitespace-nowrap text-ink shadow-lg"
                style="left: {{ round($hq['x'] / 144 * 100, 2) }}%; top: {{ round($hq['y'] / 54 * 100, 2) }}%; transform: translate(-50%, calc(-100% - 14px))">
                <x-icon name="map-pin" class="size-3.5 text-primary" />{{ $hqLabel }}
            </div>
            </div>
            @if ($locations->isNotEmpty())
                <ul class="mt-6 flex flex-wrap gap-2">
                    <li class="inline-flex items-center gap-1.5 rounded-full bg-primary px-3 py-1 text-xs font-semibold text-on-primary"><span class="size-1.5 rounded-full bg-on-primary"></span>{{ $hqLabel }}</li>
                    @foreach ($locations->take(8) as $location)
                        <li class="inline-flex items-center gap-1.5 rounded-full border border-line px-3 py-1 text-xs text-muted"><span class="size-1.5 rounded-full bg-secondary"></span>{{ $location }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if ($stats)
            <dl class="grid grid-cols-2 gap-6 border-t border-line pt-8 sm:grid-cols-4 lg:col-span-12" {!! $ds->reveal(4) !!}>
                @foreach ($stats as $stat)
                    <div>
                        <dd class="heading text-3xl lg:text-4xl" data-count>{{ $stat['value'] }}</dd>
                        <dt class="mt-1 text-sm text-muted">{{ $stat['label'] }}</dt>
                    </div>
                @endforeach
            </dl>
        @endif
    </div>
</section>
