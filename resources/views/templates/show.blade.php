<x-layouts.marketing :title="$template->name.' — Template Preview'" :description="$template->description">
    <div x-data="{ device: 'desktop' }" class="bg-slate-100">
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6">
            <div class="grid gap-8 lg:grid-cols-[1fr_320px]">
                <div>
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <a href="{{ route('templates.index') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-slate-600 hover:text-slate-900"><x-icon name="arrow-left" class="size-4" /> Semua template</a>
                        <div class="flex rounded-lg bg-white p-1 shadow-sm ring-1 ring-slate-200">
                            @foreach (['desktop' => 'device-desktop', 'tablet' => 'device-tablet', 'mobile' => 'device-mobile'] as $device => $icon)
                                <button type="button" @click="device = '{{ $device }}'" class="rounded-md p-2" :class="device === '{{ $device }}' ? 'bg-slate-900 text-white' : 'text-slate-500'" aria-label="{{ $device }}"><x-icon :name="$icon" class="size-4" /></button>
                            @endforeach
                        </div>
                    </div>
                    <div class="mx-auto overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl transition-all duration-300"
                         :class="{ 'max-w-full': device === 'desktop', 'max-w-[820px]': device === 'tablet', 'max-w-[400px]': device === 'mobile' }">
                        <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50 px-4 py-2.5">
                            <span class="size-2.5 rounded-full bg-rose-400"></span><span class="size-2.5 rounded-full bg-amber-400"></span><span class="size-2.5 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 truncate text-xs text-slate-500">{{ $template->slug }}.{{ config('platform.domain') }}</span>
                            <a href="{{ route('templates.render', $template) }}" target="_blank" class="ml-auto text-xs font-semibold text-brand-600">Buka penuh ↗</a>
                        </div>
                        <iframe src="{{ route('templates.render', $template) }}" title="Full Website Preview" class="h-[75vh] w-full border-0"></iframe>
                    </div>
                </div>
                <aside class="space-y-6 lg:sticky lg:top-24 lg:self-start">
                    <div class="card card-body">
                        <span class="badge badge-brand">{{ $template->category?->name ?? 'Template' }}</span>
                        <h1 class="mt-3 text-2xl font-extrabold">{{ $template->name }}</h1>
                        <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $template->description }}</p>
                        @php($settings = $template->resolvedSettings())
                        <dl class="mt-5 space-y-2 border-t border-slate-100 pt-5 text-sm">
                            <div class="flex justify-between"><dt class="text-slate-500">Layout</dt><dd class="font-semibold text-slate-900">{{ $template->layoutName() }}</dd></div>
                            <div class="flex justify-between"><dt class="text-slate-500">Font</dt><dd class="font-semibold text-slate-900">{{ $settings['heading_font'] ?? '-' }}</dd></div>
                            <div class="flex items-center justify-between"><dt class="text-slate-500">Warna</dt><dd class="flex gap-1">
                                <span class="size-5 rounded-full ring-1 ring-slate-200" style="background: {{ \App\Support\Website\Brand::color($settings['primary_color'] ?? null) }}"></span>
                                <span class="size-5 rounded-full ring-1 ring-slate-200" style="background: {{ \App\Support\Website\Brand::color($settings['secondary_color'] ?? null) }}"></span>
                            </dd></div>
                        </dl>
                        <a href="{{ auth()->check() ? route('websites.create', ['template' => $template->slug]) : route('register') }}" class="btn btn-primary btn-lg mt-6 w-full">Use This Template</a>
                        <p class="mt-3 text-center text-xs text-slate-500">Warna, font & section dapat diubah setelahnya.</p>
                    </div>
                    @if ($related->isNotEmpty())
                        <div>
                            <p class="mb-3 text-sm font-bold text-slate-900">Template lainnya</p>
                            <div class="space-y-3">
                                @foreach ($related as $item)
                                    <a href="{{ route('templates.show', $item) }}" class="flex items-center gap-3 rounded-xl bg-white p-2 shadow-sm ring-1 ring-slate-200 hover:ring-brand-300">
                                        <x-template-thumb :template="$item" class="w-24 shrink-0 rounded-lg" />
                                        <span class="text-sm font-semibold text-slate-900">{{ $item->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </aside>
            </div>
        </div>
    </div>
</x-layouts.marketing>
