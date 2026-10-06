{{-- Navbar: Dark — dark sticky bar with a thin utility row (hours, phone, email) and bold uppercase links. --}}
<header x-data="siteNav" class="{{ $ds->isDark() ? '' : 'tone-inverse' }} sticky top-0 z-50 bg-surface text-ink shadow-[0_10px_30px_-20px_rgb(0_0_0/0.6)] md:-top-10">
    <div class="hidden border-b border-line md:block">
        <div class="{{ $ds->container('wide') }} flex h-10 items-center justify-between gap-6 text-xs text-muted">
            <div class="flex min-w-0 items-center gap-6">
                @if ($company->working_hours)
                    <span class="inline-flex items-center gap-2"><x-icon name="clock" class="size-3.5 text-primary" />{{ $company->working_hours }}</span>
                @endif
                @if ($company->phone)
                    <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="inline-flex items-center gap-2 transition hover:text-ink"><x-icon name="phone" class="size-3.5 text-primary" />{{ $company->phone }}</a>
                @endif
                @if ($company->email)
                    <a href="mailto:{{ $company->email }}" class="hidden items-center gap-2 transition hover:text-ink lg:inline-flex"><x-icon name="mail" class="size-3.5 text-primary" />{{ $company->email }}</a>
                @endif
            </div>
            <x-site.social :company="$company" class="gap-0.5" link-class="inline-flex size-8 items-center justify-center text-muted transition hover:text-primary" icon-class="size-3.5" />
        </div>
    </div>
    <div class="{{ $ds->container('wide') }} flex h-16 items-center justify-between gap-6 lg:h-20">
        <a href="{{ $site->home() }}" class="min-w-0 text-ink">
            <x-site.logo :company="$company" text-class="text-lg font-extrabold tracking-tight uppercase" />
        </a>
        <nav class="hidden h-full items-stretch gap-1 lg:flex" aria-label="Menu utama">
            @include('components.company.partials.nav-links', [
                'linkClass' => 'flex h-full items-center border-b-2 border-transparent px-3 text-[13px] font-bold tracking-[0.08em] text-ink/75 uppercase transition hover:border-primary hover:text-ink',
                'activeClass' => '!border-primary !text-ink',
                'dropdownClass' => 'min-w-60 border-t-2 border-primary bg-card p-2 shadow-2xl',
                'childClass' => 'block px-3 py-2.5 text-sm font-medium text-muted hover:bg-surface-alt hover:text-ink',
            ])
        </nav>
        <div class="flex items-center gap-2">
            <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'max-sm:hidden! !py-2.5 uppercase tracking-[0.08em]') }}">Hubungi Kami</a>
            @include('components.company.navbar._toggle', ['class' => '-mr-2 text-ink hover:bg-ink/10 lg:hidden'])
        </div>
    </div>
    @include('components.company.navbar._drawer')
</header>
