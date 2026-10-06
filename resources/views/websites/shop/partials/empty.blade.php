{{-- Empty state. Vars: $icon, $emptyTitle, $emptyText, $actionUrl, $actionLabel. --}}
<div class="flex flex-col items-center rounded-brand border border-dashed border-line px-6 py-16 text-center">
    <span class="inline-flex size-16 items-center justify-center rounded-full bg-primary/10 text-primary">
        <x-shop.icon :name="$icon ?? 'bag'" class="size-8" />
    </span>
    <h2 class="mt-5 font-heading text-lg font-bold text-ink">{{ $emptyTitle }}</h2>
    @if (! empty($emptyText))
        <p class="mt-1.5 max-w-md text-sm text-muted">{{ $emptyText }}</p>
    @endif
    @if (! empty($actionUrl))
        <a href="{{ $actionUrl }}" class="{{ $ds->btn('primary', 'mt-6') }}">{{ $actionLabel ?? 'Mulai belanja' }}</a>
    @endif
</div>
