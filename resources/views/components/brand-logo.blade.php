{{-- Platform logo (Admin → Settings → Logo) or default mark. --}}
@props(['dark' => false])
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-2.5']) }}>
    @if (setting('app_logo'))
        <img src="{{ \App\Support\MediaUrl::resolve(setting('app_logo')) }}" alt="{{ app_name() }}" class="h-8 w-auto">
    @else
        <span class="inline-flex size-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-violet-600 text-white shadow-md shadow-brand-600/30">
            <svg viewBox="0 0 24 24" fill="none" class="size-5" aria-hidden="true"><path d="M4 5.5A1.5 1.5 0 0 1 5.5 4h6A1.5 1.5 0 0 1 13 5.5v13a1.5 1.5 0 0 1-1.5 1.5h-6A1.5 1.5 0 0 1 4 18.5v-13Z" fill="currentColor" opacity=".55"/><path d="M15 5.5A1.5 1.5 0 0 1 16.5 4h2A1.5 1.5 0 0 1 20 5.5v5a1.5 1.5 0 0 1-1.5 1.5h-2a1.5 1.5 0 0 1-1.5-1.5v-5ZM15 15.5a1.5 1.5 0 0 1 1.5-1.5h2a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-2a1.5 1.5 0 0 1-1.5-1.5v-3Z" fill="currentColor"/></svg>
        </span>
        <span class="font-display text-lg font-extrabold tracking-tight {{ $dark ? 'text-white' : 'text-slate-900' }}">{{ app_name() }}</span>
    @endif
</span>
