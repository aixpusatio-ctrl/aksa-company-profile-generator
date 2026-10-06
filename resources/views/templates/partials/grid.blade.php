{{-- Category filter + template cards (shared by public gallery and dashboard). --}}
<div class="flex flex-wrap gap-2">
    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ ! $activeCategory ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300' }}">Semua</a>
    @foreach ($categories as $category)
        <a href="{{ request()->fullUrlWithQuery(['category' => $category->slug]) }}" class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $activeCategory === $category->slug ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200 hover:ring-slate-300' }}">{{ $category->name }}</a>
    @endforeach
</div>

@if ($templates->isEmpty())
    <x-empty-state class="mt-8" icon="template" title="Template tidak ditemukan" description="Coba kategori atau kata kunci lain." />
@else
    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($templates as $template)
            <div class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl">
                <a href="{{ $link($template) }}" class="relative block">
                    <x-template-thumb :template="$template" />
                    @if ($template->is_featured)
                        <span class="absolute top-3 left-3 badge badge-brand bg-white">★ Featured</span>
                    @endif
                    <span class="absolute inset-0 flex items-center justify-center bg-slate-900/0 opacity-0 transition group-hover:bg-slate-900/40 group-hover:opacity-100">
                        <span class="btn btn-secondary">Preview <x-icon name="eye" class="size-4" /></span>
                    </span>
                </a>
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $template->name }}</h3>
                            <p class="text-xs font-medium text-brand-600">{{ $template->category?->name ?? 'Uncategorized' }}</p>
                        </div>
                        <div class="flex -space-x-1">
                            @foreach (['primary_color', 'secondary_color'] as $color)
                                <span class="size-5 rounded-full ring-2 ring-white" style="background: {{ \App\Support\Website\Brand::color($template->resolvedSettings()[$color] ?? null) }}"></span>
                            @endforeach
                        </div>
                    </div>
                    <p class="mt-2 line-clamp-2 text-sm text-slate-500">{{ $template->description }}</p>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('templates.show', $template) }}" class="btn btn-secondary btn-sm flex-1">Preview</a>
                        <a href="{{ auth()->check() ? route('websites.create', ['template' => $template->slug]) : route('register') }}" class="btn btn-primary btn-sm flex-1">Use This Template</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
