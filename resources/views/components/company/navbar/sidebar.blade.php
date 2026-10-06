{{-- Navbar: Sidebar — fixed vertical sidebar on desktop (logo, vertical nav, contact, socials) that offsets the page; top bar + slide-in drawer on mobile. --}}
<style>@media (min-width: 1024px) { body { padding-left: 16rem; } }</style>
<header x-data="siteNav" class="sticky top-0 z-50 text-ink lg:fixed lg:inset-y-0 lg:left-0 lg:w-64 lg:border-r lg:border-line lg:bg-surface">
    {{-- Mobile top bar --}}
    <div class="flex h-16 items-center justify-between gap-4 border-b border-line bg-surface/95 px-5 backdrop-blur-md sm:px-8 lg:hidden">
        <a href="{{ $site->home() }}" class="min-w-0 text-ink">
            <x-site.logo :company="$company" img-class="h-8 w-auto" text-class="text-base font-bold tracking-tight" />
        </a>
        @include('components.company.navbar._toggle', ['class' => '-mr-2 text-ink hover:bg-ink/5'])
    </div>

    {{-- Desktop sidebar --}}
    <div class="hidden h-full flex-col overflow-y-auto px-8 py-10 lg:flex">
        <a href="{{ $site->home() }}" class="text-ink">
            <x-site.logo :company="$company" img-class="h-12 w-auto" text-class="text-lg font-bold tracking-tight" />
        </a>
        @if ($company->tagline)
            <p class="mt-4 text-xs leading-relaxed text-muted">{{ $company->tagline }}</p>
        @endif

        <nav class="mt-12 flex flex-col" aria-label="Menu utama">
            @foreach ($menu as $item)
                @if ($item->hasChildren())
                    <div x-data="{ sub: {{ $item->active ? 'true' : 'false' }} }">
                        <button type="button" @click="sub = !sub" :aria-expanded="sub"
                                class="group flex w-full items-center justify-between py-2.5 text-left text-sm font-medium {{ $item->active ? 'text-ink' : 'text-ink/65' }} transition hover:text-ink">
                            <span class="flex items-center gap-3"><span class="h-px w-3 bg-current opacity-40 transition-all group-hover:w-6 group-hover:bg-primary group-hover:opacity-100"></span>{{ $item->title }}</span>
                            <x-icon name="chevron-down" class="size-3.5 transition" ::class="sub && 'rotate-180'" />
                        </button>
                        <div x-show="sub" x-collapse>
                            <div class="mb-2 ml-1.5 flex flex-col border-l border-line pl-5">
                                @foreach ($item->children as $child)
                                    <a {!! $child->attributes() !!} class="py-1.5 text-sm text-muted transition hover:text-ink">{{ $child->title }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <a {!! $item->attributes() !!} class="group flex items-center gap-3 py-2.5 text-sm font-medium {{ $item->active ? 'text-ink' : 'text-ink/65' }} transition hover:text-ink">
                        <span class="h-px bg-current transition-all group-hover:w-6 group-hover:bg-primary group-hover:opacity-100 {{ $item->active ? 'w-6 bg-primary opacity-100' : 'w-3 opacity-40' }}"></span>{{ $item->title }}
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="mt-auto space-y-6 pt-12">
            <a href="{{ $site->anchor('contact') }}" class="{{ $ds->btn('primary', 'w-full') }}">Hubungi Kami</a>
            <div class="space-y-2 text-xs text-muted">
                @if ($company->phone)<a href="tel:{{ preg_replace('/[^0-9+]/', '', $company->phone) }}" class="block transition hover:text-ink">{{ $company->phone }}</a>@endif
                @if ($company->email)<a href="mailto:{{ $company->email }}" class="block break-all transition hover:text-ink">{{ $company->email }}</a>@endif
                @if ($company->city)<p>{{ $company->city }}{{ $company->country ? ', '.$company->country : '' }}</p>@endif
            </div>
            <x-site.social :company="$company" class="-ml-2" link-class="inline-flex size-9 items-center justify-center rounded-full text-muted transition hover:bg-ink/5 hover:text-ink" />
            <p class="text-[11px] text-muted">&copy; {{ date('Y') }} {{ $company->name }}</p>
        </div>
    </div>

    @include('components.company.navbar._drawer', ['side' => 'right'])
</header>
