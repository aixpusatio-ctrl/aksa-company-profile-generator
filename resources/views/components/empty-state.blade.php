@props(['title', 'description' => null, 'icon' => 'sparkles'])
<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 bg-white px-6 py-14 text-center']) }}>
    <span class="inline-flex size-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600"><x-icon :name="$icon" class="size-7" /></span>
    <h3 class="mt-4 text-base font-semibold text-slate-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-slate-500">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>
