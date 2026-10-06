{{-- Radio grid of templates with category tabs, instant search and live previews. Field name: template --}}
@php
    $groups = $templates->groupBy(fn ($t) => $t->category?->name ?? 'Other');
@endphp
<div x-data="{ category: 'all', q: '', matches(el) { const q = this.q.trim().toLowerCase(); return (this.category === 'all' || el.dataset.category === this.category) && (!q || el.dataset.search.includes(q)); } }">
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:flex-wrap lg:px-0">
            <button type="button" @click="category = 'all'" class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-semibold transition" :class="category === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200'">All ({{ $templates->count() }})</button>
            @foreach ($groups as $name => $items)
                <button type="button" @click="category = @js($name)" class="shrink-0 rounded-full px-3.5 py-1.5 text-xs font-semibold transition" :class="category === @js($name) ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200'">{{ $name }} ({{ $items->count() }})</button>
            @endforeach
        </div>
        <input type="search" x-model.debounce.150ms="q" placeholder="Search template..." class="form-input lg:w-64" aria-label="Search template">
    </div>
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($templates as $template)
            <label class="group relative cursor-pointer" data-category="{{ $template->category?->name ?? 'Other' }}"
                   data-search="{{ \Illuminate\Support\Str::lower($template->name.' '.$template->style.' '.$template->category?->name) }}" x-show="matches($el)">
                <input type="radio" name="template" value="{{ $template->slug }}" class="peer sr-only" @checked(old('template', $selected) === $template->slug) required>
                <div class="overflow-hidden rounded-2xl border-2 border-transparent bg-white shadow-sm ring-1 ring-slate-200 transition peer-checked:border-brand-500 peer-checked:ring-4 peer-checked:ring-brand-500/15 hover:shadow-lg">
                    <x-template-thumb :template="$template" />
                    <div class="flex items-center justify-between gap-2 p-4">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-slate-900">{{ $template->name }} @if ($template->is_featured)<span class="text-amber-500">★</span>@endif</p>
                            <p class="truncate text-xs text-slate-500">{{ $template->category?->name }} · {{ \Illuminate\Support\Str::before((string) $template->style, ' ·') }}</p>
                        </div>
                        <a href="{{ route('templates.show', $template) }}" target="_blank" class="btn btn-ghost btn-sm shrink-0" @click.stop>Preview <x-icon name="external" class="size-3.5" /></a>
                    </div>
                </div>
                <span class="absolute top-3 right-3 hidden size-7 items-center justify-center rounded-full bg-brand-600 text-white shadow-lg peer-checked:inline-flex"><x-icon name="check" class="size-4" /></span>
            </label>
        @endforeach
    </div>
</div>
