@php
    $settings = $template->resolvedSettings();
    $ds = $template->isComposed() ? $template->designSystem() : null;
    $useUrl = auth()->check() ? route('websites.create', ['template' => $template->slug]) : route('register', ['template' => $template->slug]);
    $sections = collect($settings['sections'] ?? array_keys(config('website-templates.sections')))
        ->map(fn ($key) => config('website-templates.sections.'.$key))->filter();
@endphp
<x-layouts.marketing :title="$template->name.' — Template Preview'" :description="$template->description">
    <div x-data="{ device: 'desktop' }" class="bg-slate-100">
        <div class="mx-auto max-w-[96rem] px-4 py-6 sm:px-6 lg:py-8">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                <div class="flex min-w-0 items-center gap-3">
                    <a href="{{ route('templates.index') }}" class="btn btn-secondary btn-sm"><x-icon name="arrow-left" class="size-4" /> Templates</a>
                    <h1 class="truncate text-lg font-bold sm:text-xl">{{ $template->name }}</h1>
                    <span class="badge badge-brand hidden sm:inline-flex">{{ $template->category?->name }}</span>
                    @if ($template->is_featured)<span class="badge badge-amber hidden sm:inline-flex">★ Featured</span>@endif
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex rounded-lg bg-white p-1 shadow-sm ring-1 ring-slate-200">
                        @foreach (['desktop' => 'device-desktop', 'tablet' => 'device-tablet', 'mobile' => 'device-mobile'] as $device => $icon)
                            <button type="button" @click="device = '{{ $device }}'" class="rounded-md p-2" :class="device === '{{ $device }}' ? 'bg-slate-900 text-white' : 'text-slate-500'" aria-label="{{ ucfirst($device) }} preview"><x-icon :name="$icon" class="size-4" /></button>
                        @endforeach
                    </div>
                    <a href="{{ $useUrl }}" class="btn btn-primary">Use This Template</a>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
                {{-- Full website preview --}}
                <div>
                    <div class="mx-auto overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl transition-all duration-300"
                         :class="{ 'max-w-full': device === 'desktop', 'max-w-[834px]': device === 'tablet', 'max-w-[400px]': device === 'mobile' }">
                        <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50 px-4 py-2.5">
                            <span class="size-2.5 rounded-full bg-rose-400"></span><span class="size-2.5 rounded-full bg-amber-400"></span><span class="size-2.5 rounded-full bg-emerald-400"></span>
                            <span class="ml-3 truncate text-xs text-slate-500">{{ $template->slug }}.{{ config('platform.domain') }}</span>
                            <a href="{{ route('templates.render', $template) }}" target="_blank" class="ml-auto text-xs font-semibold text-brand-600">Buka penuh ↗</a>
                        </div>
                        <iframe src="{{ route('templates.render', $template) }}" title="Full Website Preview — {{ $template->name }}" class="h-[78vh] w-full border-0"></iframe>
                    </div>
                </div>

                <aside class="space-y-5 xl:sticky xl:top-24 xl:self-start">
                    <div class="card card-body">
                        <p class="text-sm leading-relaxed text-slate-600">{{ $template->description }}</p>
                        @if ($template->styleTags())
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @foreach ($template->styleTags() as $tag)
                                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-600">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                        <a href="{{ $useUrl }}" class="btn btn-primary btn-lg mt-5 w-full">Use This Template</a>
                        <p class="mt-2 text-center text-xs text-slate-500">Warna, font, section & menu dapat diubah setelahnya.</p>
                    </div>

                    {{-- Mobile preview --}}
                    <div class="card hidden p-4 xl:block" x-show="device === 'desktop'">
                        <p class="mb-3 text-xs font-semibold tracking-wide text-slate-500 uppercase">Mobile preview</p>
                        <div class="mx-auto w-[210px] rounded-[2rem] border-[6px] border-slate-900 bg-slate-900 shadow-xl">
                            <x-template-thumb :template="$template" mobile live aspect="aspect-[390/780]" class="rounded-[1.6rem]" />
                        </div>
                    </div>

                    <div class="card card-body">
                        <p class="text-xs font-semibold tracking-wide text-slate-500 uppercase">Design system</p>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Tipe</dt><dd class="text-right font-semibold text-slate-900">{{ $ds ? 'Composed (komponen)' : 'Crafted ('.$template->layoutName().')' }}</dd></div>
                            @if ($ds)
                                @foreach ($ds->summary() as $label => $value)
                                    <div class="flex justify-between gap-3"><dt class="text-slate-500">{{ $label }}</dt><dd class="text-right font-semibold text-slate-900">{{ $value }}</dd></div>
                                @endforeach
                            @endif
                            <div class="flex justify-between gap-3"><dt class="text-slate-500">Font</dt><dd class="text-right font-semibold text-slate-900">{{ $settings['heading_font'] ?? '-' }} / {{ $settings['body_font'] ?? '-' }}</dd></div>
                            <div class="flex items-center justify-between gap-3"><dt class="text-slate-500">Warna</dt><dd class="flex gap-1">
                                <span class="size-5 rounded-full ring-1 ring-slate-200" style="background: {{ \App\Support\Website\Brand::color($settings['primary_color'] ?? null) }}"></span>
                                <span class="size-5 rounded-full ring-1 ring-slate-200" style="background: {{ \App\Support\Website\Brand::color($settings['secondary_color'] ?? null) }}"></span>
                            </dd></div>
                        </dl>
                        <p class="mt-4 text-xs font-semibold tracking-wide text-slate-500 uppercase">Section</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($sections as $label)
                                <span class="rounded-md bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-700">{{ $label }}</span>
                            @endforeach
                        </div>
                    </div>

                    @if ($related->isNotEmpty())
                        <div>
                            <p class="mb-3 text-sm font-bold text-slate-900">Template serupa</p>
                            <div class="space-y-3">
                                @foreach ($related as $item)
                                    <a href="{{ route('templates.show', $item) }}" class="flex items-center gap-3 rounded-xl bg-white p-2 shadow-sm ring-1 ring-slate-200 hover:ring-brand-300">
                                        <x-template-thumb :template="$item" class="w-24 shrink-0 rounded-lg" />
                                        <span class="min-w-0">
                                            <span class="block truncate text-sm font-semibold text-slate-900">{{ $item->name }}</span>
                                            <span class="block truncate text-xs text-slate-500">{{ $item->style }}</span>
                                        </span>
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
