{{-- Footer: Newsletter — "Dapatkan penawaran" email row that hands the address to the contact form, then link columns. --}}
@php
    $links = $menu->flatMap(fn ($item) => collect([$item])->merge($item->children))->filter(fn ($item) => $item->url)->unique('url')->take(8);
@endphp
<footer class="border-t border-line bg-surface text-ink">
    <div class="{{ $ds->container() }}">
        <div class="grid gap-8 border-b border-line py-14 lg:grid-cols-2 lg:items-center lg:gap-16">
            <div {!! $ds->reveal() !!}>
                <p class="heading text-[clamp(1.75rem,3vw,2.5rem)]">Dapatkan penawaran terbaik</p>
                <p class="mt-3 max-w-md text-muted">Tinggalkan email Anda dan tim {{ $company->name }} akan menyiapkan penawaran sesuai kebutuhan.</p>
            </div>
            <div x-data="{ email: '' }" {!! $ds->reveal(1) !!}>
                <div class="flex flex-col gap-3 rounded-[calc(var(--brand-btn-radius)+0.375rem)] border border-line bg-card p-2 sm:flex-row sm:items-center">
                    <label for="footer-offer-email" class="sr-only">Alamat email</label>
                    <div class="flex min-w-0 flex-1 items-center gap-3 px-3">
                        <x-icon name="mail" class="size-5 shrink-0 text-muted" />
                        <input id="footer-offer-email" type="email" x-model="email" placeholder="nama@perusahaan.com" autocomplete="email"
                               class="h-12 w-full min-w-0 bg-transparent text-base text-ink placeholder:text-muted focus:outline-none">
                    </div>
                    <a href="{{ $site->anchor('contact') }}"
                       @click="const f = document.querySelector('#contact input[type=email]'); if (f && email) { f.value = email; f.dispatchEvent(new Event('input')); }"
                       class="{{ $ds->btn('primary', 'w-full sm:w-auto') }}">Minta Penawaran <x-icon name="arrow-right" class="size-4" /></a>
                </div>
                <p class="mt-3 px-1 text-xs text-muted">Kami akan menghubungi Anda melalui formulir kontak — tanpa spam.</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-x-6 gap-y-12 py-14 lg:grid-cols-12">
            <div class="col-span-2 lg:col-span-4">
                <a href="{{ $site->home() }}" class="inline-block text-ink"><x-site.logo :company="$company" text-class="text-lg font-bold tracking-tight" /></a>
                @if ($company->description)
                    <p class="mt-5 max-w-sm text-sm leading-relaxed text-muted">{{ \Illuminate\Support\Str::limit($company->description, 170) }}</p>
                @endif
                <x-site.social :company="$company" class="mt-6" link-class="inline-flex size-10 items-center justify-center rounded-full border border-line text-muted transition hover:border-primary hover:text-primary" />
            </div>
            <div class="lg:col-span-2 lg:col-start-6">
                <h3 class="text-sm font-semibold text-ink">Navigasi</h3>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach ($links as $item)
                        <li><a {!! $item->attributes() !!} class="text-muted transition hover:text-ink">{{ $item->title }}</a></li>
                    @endforeach
                </ul>
            </div>
            @if ($company->services->isNotEmpty())
                <div class="lg:col-span-3">
                    <h3 class="text-sm font-semibold text-ink">Layanan</h3>
                    <ul class="mt-5 space-y-3 text-sm">
                        @foreach ($company->services->take(6) as $service)
                            <li><a href="{{ $site->anchor('services') }}" class="text-muted transition hover:text-ink">{{ $service->title }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="lg:col-span-2">
                <h3 class="text-sm font-semibold text-ink">Kontak</h3>
                <ul class="mt-5 space-y-3 text-sm text-muted">
                    @if ($company->phone)<li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="transition hover:text-ink">{{ $company->phone }}</a></li>@endif
                    @if ($company->email)<li><a href="mailto:{{ $company->email }}" class="break-all transition hover:text-ink">{{ $company->email }}</a></li>@endif
                    @if ($company->fullAddress())<li class="leading-relaxed">{{ $company->fullAddress() }}</li>@endif
                </ul>
            </div>
        </div>
    </div>
    <div class="border-t border-line">
        <div class="{{ $ds->container() }} flex flex-col gap-3 py-6 text-xs text-muted sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ date('Y') }} {{ $company->name }}. Seluruh hak cipta dilindungi.</p>
            <div class="flex flex-wrap gap-5">
                @foreach ($pages->take(4) as $p)
                    <a href="{{ $site->page($p->slug) }}" class="transition hover:text-ink">{{ $p->title }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
