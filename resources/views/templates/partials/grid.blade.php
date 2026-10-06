{{--
    Template gallery (public /templates and dashboard): category tabs with
    counts + instant search over name, category, style and description.
    Live previews load lazily, so browsing 50 templates stays fast.
    Params: $templates, $categories, $activeCategory, $link (fn), $useLink (optional fn)
--}}
@php
    $useLink ??= fn ($t) => auth()->check() ? route('websites.create', ['template' => $t->slug]) : route('register', ['template' => $t->slug]);
    $counts = $templates->groupBy(fn ($t) => $t->category?->slug)->map->count();
@endphp
<div x-data="{ category: @js($activeCategory ?: 'all'), q: @js(request('q', '')),
               matches(el) { const t = el.dataset; const q = this.q.trim().toLowerCase();
                             return (this.category === 'all' || t.category === this.category) && (!q || t.search.includes(q)); },
               get visible() { return [...this.$root.querySelectorAll('[data-template]')].filter((el) => this.matches(el)).length; } }">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1 lg:mx-0 lg:flex-wrap lg:px-0" role="tablist" aria-label="Kategori template">
            <button type="button" role="tab" @click="category = 'all'" :aria-selected="category === 'all'"
                    class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition"
                    :class="category === 'all' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300'">
                All <span class="ml-1 opacity-60">{{ $templates->count() }}</span>
            </button>
            @foreach ($categories as $category)
                @continue(! ($counts[$category->slug] ?? 0))
                <button type="button" role="tab" @click="category = @js($category->slug)" :aria-selected="category === @js($category->slug)"
                        class="shrink-0 rounded-full px-4 py-2 text-sm font-semibold transition"
                        :class="category === @js($category->slug) ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300'">
                    {{ $category->name }} <span class="ml-1 opacity-60">{{ $counts[$category->slug] }}</span>
                </button>
            @endforeach
        </div>
        <label class="relative block w-full lg:w-72">
            <span class="sr-only">Search Template</span>
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" x-model.debounce.150ms="q" placeholder="Search template..." class="form-input pl-9">
        </label>
    </div>

    <p class="mt-4 text-sm text-slate-500" aria-live="polite"><span x-text="visible"></span> template</p>

    <div class="mt-4 grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($templates as $template)
            <article data-template data-category="{{ $template->category?->slug }}"
                     data-search="{{ \Illuminate\Support\Str::lower($template->name.' '.$template->category?->name.' '.$template->style.' '.$template->description) }}"
                     x-show="matches($el)" x-transition.opacity
                     class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <a href="{{ $link($template) }}" class="relative block border-b border-slate-100">
                    <x-template-thumb :template="$template" />
                    <div class="absolute top-3 left-3 flex gap-1.5">
                        @if ($template->is_featured)
                            <span class="badge bg-white text-amber-700 ring-amber-600/20">★ Featured</span>
                        @endif
                        <span class="badge bg-white/90 text-slate-600 ring-slate-200">{{ $template->isComposed() ? 'Composed' : 'Crafted' }}</span>
                    </div>
                    <span class="absolute inset-0 flex items-center justify-center bg-slate-900/0 opacity-0 transition group-hover:bg-slate-900/40 group-hover:opacity-100">
                        <span class="btn btn-secondary">Preview <x-icon name="eye" class="size-4" /></span>
                    </span>
                </a>
                <div class="flex flex-1 flex-col p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="truncate font-bold text-slate-900">{{ $template->name }}</h3>
                            <p class="text-xs font-medium text-brand-600">{{ $template->category?->name ?? 'Uncategorized' }}</p>
                        </div>
                        <div class="flex shrink-0 -space-x-1" aria-hidden="true">
                            @foreach (['primary_color', 'secondary_color'] as $color)
                                <span class="size-5 rounded-full ring-2 ring-white" style="background: {{ \App\Support\Website\Brand::color($template->resolvedSettings()[$color] ?? null) }}"></span>
                            @endforeach
                        </div>
                    </div>
                    @if ($template->styleTags())
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach (array_slice($template->styleTags(), 0, 4) as $tag)
                                <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[11px] font-medium text-slate-600">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                    <p class="mt-3 line-clamp-2 flex-1 text-sm text-slate-500">{{ $template->description }}</p>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('templates.show', $template) }}" class="btn btn-secondary btn-sm flex-1">Preview</a>
                        <a href="{{ $useLink($template) }}" class="btn btn-primary btn-sm flex-1">Use This Template</a>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div x-cloak x-show="visible === 0" class="mt-8">
        <x-empty-state icon="template" title="Template tidak ditemukan" description="Coba kategori atau kata kunci lain." />
    </div>
</div>
