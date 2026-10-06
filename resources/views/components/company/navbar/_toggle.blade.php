{{-- Shared hamburger button for navbar variants. Params: $class --}}
<button type="button" @click="open = !open" :aria-expanded="open" aria-label="Buka menu"
        class="inline-flex size-11 shrink-0 items-center justify-center rounded-full transition {{ $class ?? 'text-ink hover:bg-ink/5 lg:hidden' }}">
    <x-icon name="menu" class="size-6" />
</button>
