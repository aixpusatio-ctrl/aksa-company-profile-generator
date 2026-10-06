{{-- Navbar: Centered — centered logo with utility links on both sides, nav row between hairlines that stays sticky; drawer on mobile. --}}
<header x-data="siteNav" class="sticky top-0 z-50 border-b border-line bg-surface text-ink lg:-top-20">
    <div class="{{ $ds->container() }} grid h-16 grid-cols-[1fr_auto_1fr] items-center gap-4 lg:h-20">
        <div class="flex items-center gap-5 text-xs text-muted">
            @include('components.company.navbar._toggle', ['class' => '-ml-2 text-ink hover:bg-ink/5 lg:hidden'])
            @if ($company->phone)
                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="hidden items-center gap-2 transition hover:text-ink lg:inline-flex"><x-icon name="phone" class="size-3.5" />{{ $company->phone }}</a>
            @endif
            @if ($company->email)
                <a href="mailto:{{ $company->email }}" class="hidden items-center gap-2 transition hover:text-ink xl:inline-flex"><x-icon name="mail" class="size-3.5" />{{ $company->email }}</a>
            @endif
        </div>
        <a href="{{ $site->home() }}" class="flex min-w-0 justify-center text-ink">
            <x-site.logo :company="$company" text-class="text-lg font-semibold tracking-tight lg:text-2xl" />
        </a>
        <div class="flex items-center justify-end gap-3">
            <x-site.social :company="$company" class="hidden lg:flex" link-class="inline-flex size-8 items-center justify-center rounded-full text-muted transition hover:text-ink" icon-class="size-3.5" />
            <a href="{{ $site->anchor('contact') }}" class="hidden text-xs font-semibold tracking-[0.16em] text-ink uppercase underline decoration-primary decoration-2 underline-offset-[6px] transition hover:text-primary lg:inline">Hubungi</a>
            <a href="{{ $site->anchor('contact') }}" class="inline-flex size-11 items-center justify-center rounded-full text-ink hover:bg-ink/5 lg:hidden" aria-label="Hubungi Kami"><x-icon name="phone" class="size-5" /></a>
        </div>
    </div>
    <div class="hidden border-t border-line lg:block">
        <nav class="{{ $ds->container() }} flex h-12 items-center justify-center divide-x divide-line" aria-label="Menu utama">
            @include('components.company.partials.nav-links', [
                'linkClass' => 'px-6 text-xs font-semibold tracking-[0.16em] text-ink/70 uppercase transition hover:text-primary',
                'activeClass' => '!text-primary',
            ])
        </nav>
    </div>
    @include('components.company.navbar._drawer', ['side' => 'left'])
</header>
