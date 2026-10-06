{{-- Navbar: Minimal — only the logo and a "Menu" button that opens a full-screen overlay with giant links, contact info and socials. --}}
@php($overlay = $heroFirst ?? false)
<header x-data="siteNav" class="{{ $overlay ? 'fixed inset-x-0 top-0' : 'sticky top-0' }} z-50 text-ink">
    <div class="border-b transition-[background-color,border-color,box-shadow] duration-300 {{ $overlay ? '' : 'bg-surface/90 backdrop-blur-md' }}"
         @if ($overlay)
             :class="scrolled ? 'border-line bg-surface/90 backdrop-blur-md' : 'border-transparent bg-surface/70 backdrop-blur-md'"
         @else
             :class="scrolled ? 'border-line' : 'border-transparent'"
         @endif>
        <div class="{{ $ds->container('wide') }} flex h-16 items-center justify-between gap-6 sm:h-20">
            <a href="{{ $site->home() }}" class="min-w-0 text-ink">
                <x-site.logo :company="$company" text-class="text-lg font-semibold tracking-tight" />
            </a>
            <div class="flex items-center gap-6">
                <a href="{{ $site->anchor('contact') }}" class="hidden text-sm font-medium text-ink/70 transition hover:text-ink md:inline">Mulai proyek</a>
                <button type="button" @click="open = true" :aria-expanded="open" aria-label="Buka menu"
                        class="group inline-flex h-11 items-center gap-3 rounded-full border border-line bg-surface/60 pr-2 pl-5 text-sm font-semibold text-ink transition hover:border-ink">
                    Menu
                    <span class="inline-flex size-8 flex-col items-center justify-center gap-1 rounded-full bg-ink text-surface transition group-hover:bg-primary group-hover:text-on-primary">
                        <span class="h-px w-3.5 bg-current"></span><span class="h-px w-3.5 bg-current"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @include('components.company.navbar._overlay', ['hideClass' => '', 'large' => true])
</header>
