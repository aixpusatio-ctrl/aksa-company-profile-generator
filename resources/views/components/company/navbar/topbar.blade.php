{{-- Navbar: Topbar — institutional: colored utility bar (address, phone, email, hours, socials) above a white bar with stacked name + tagline, underline links and CTA. --}}
<header x-data="siteNav" class="sticky -top-9 z-50 bg-surface text-ink md:-top-10">
    <div class="bg-secondary text-on-secondary">
        <div class="{{ $ds->container('wide') }} flex h-9 items-center justify-between gap-6 text-xs md:h-10">
            <div class="flex min-w-0 items-center gap-5 opacity-90">
                @if ($company->fullAddress())
                    <span class="hidden min-w-0 items-center gap-2 xl:inline-flex"><x-icon name="map-pin" class="size-3.5 shrink-0" /><span class="truncate">{{ \Illuminate\Support\Str::limit($company->fullAddress(), 60) }}</span></span>
                @endif
                @if ($company->phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="inline-flex shrink-0 items-center gap-2 hover:underline"><x-icon name="phone" class="size-3.5" />{{ $company->phone }}</a>
                @endif
                @if ($company->email)
                    <a href="mailto:{{ $company->email }}" class="hidden shrink-0 items-center gap-2 hover:underline sm:inline-flex"><x-icon name="mail" class="size-3.5" />{{ $company->email }}</a>
                @endif
                @if ($company->working_hours)
                    <span class="hidden shrink-0 items-center gap-2 lg:inline-flex"><x-icon name="clock" class="size-3.5" />{{ $company->working_hours }}</span>
                @endif
            </div>
            <x-site.social :company="$company" class="hidden gap-0 md:flex" link-class="inline-flex size-8 items-center justify-center opacity-80 transition hover:opacity-100" icon-class="size-3.5" />
        </div>
    </div>

    <div class="border-b border-line transition-shadow" :class="scrolled && 'shadow-[0_10px_30px_-22px_rgb(0_0_0/0.45)]'">
        <div class="{{ $ds->container('wide') }} flex h-18 items-center justify-between gap-6 lg:h-22">
            <a href="{{ $site->home() }}" class="flex min-w-0 items-center gap-3 text-ink">
                <x-site.logo :company="$company" img-class="h-11 w-auto" text-class="sr-only" />
                <span class="min-w-0">
                    <span class="heading line-clamp-2 text-[15px] leading-tight sm:text-lg">{{ $company->name }}</span>
                    @if ($company->tagline)
                        <span class="mt-0.5 hidden truncate text-xs text-muted sm:block">{{ $company->tagline }}</span>
                    @endif
                </span>
            </a>
            <nav class="hidden items-center gap-6 xl:gap-7 lg:flex" aria-label="Menu utama">
                @include('components.company.partials.nav-links', [
                    'linkClass' => 'relative py-2 text-sm font-semibold text-ink/75 transition hover:text-ink after:absolute after:inset-x-0 after:-bottom-0.5 after:h-0.5 after:origin-left after:scale-x-0 after:bg-primary after:transition-transform after:duration-300 hover:after:scale-x-100',
                    'activeClass' => '!text-primary after:scale-x-100!',
                    'dropdownClass' => 'min-w-60 rounded-b-brand border-t-2 border-primary bg-card p-2 shadow-xl',
                ])
            </nav>
            <div class="flex shrink-0 items-center gap-2">
                <a href="{{ $site->anchor('contact') }}" class="hidden size-11 items-center justify-center rounded-full border border-line text-ink transition hover:border-primary hover:text-primary sm:inline-flex" aria-label="Hubungi Kami"><x-icon name="chat" class="size-5" /></a>
                <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'max-md:hidden! !py-2.5') }}">Hubungi Kami</a>
                @include('components.company.navbar._toggle', ['class' => '-mr-2 text-ink hover:bg-ink/5 lg:hidden'])
            </div>
        </div>
    </div>

    <div x-cloak x-show="open" x-collapse class="border-b border-line bg-surface lg:hidden">
        <div class="{{ $ds->container() }} max-h-[calc(100dvh-8rem)] overflow-y-auto pt-2 pb-6">
            @include('components.company.partials.mobile-menu')
            <a href="{{ $site->anchor('contact') }}" @click="close()" class="{{ $ds->btn('primary', 'mt-4 w-full') }}">Hubungi Kami</a>
        </div>
    </div>
</header>
