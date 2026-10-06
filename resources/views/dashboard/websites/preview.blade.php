<x-layouts.app :title="'Preview · '.$company->name">
    <div x-data="{ device: 'desktop', page: '' }">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('websites.show', $company) }}" class="btn btn-secondary btn-sm"><x-icon name="arrow-left" class="size-4" /> Kembali</a>
                <h1 class="text-lg font-bold">Preview: {{ $company->name }}</h1>
                <x-status-badge :status="$company->status" />
            </div>
            <div class="flex items-center gap-2">
                <div class="flex rounded-lg bg-white p-1 shadow-sm ring-1 ring-slate-200">
                    @foreach (['desktop' => 'device-desktop', 'tablet' => 'device-tablet', 'mobile' => 'device-mobile'] as $device => $icon)
                        <button type="button" @click="device = '{{ $device }}'" class="rounded-md p-2" :class="device === '{{ $device }}' ? 'bg-slate-900 text-white' : 'text-slate-500'" aria-label="{{ $device }}"><x-icon :name="$icon" class="size-4" /></button>
                    @endforeach
                </div>
                @unless ($company->isPublished())
                    <form method="POST" action="{{ route('websites.publish', $company) }}">@csrf<button class="btn btn-success btn-sm"><x-icon name="rocket" class="size-4" /> Publish</button></form>
                @endunless
            </div>
        </div>
        <div class="mx-auto overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl transition-all duration-300"
             :class="{ 'max-w-full': device === 'desktop', 'max-w-[820px]': device === 'tablet', 'max-w-[400px]': device === 'mobile' }">
            <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50 px-4 py-2.5">
                <span class="size-2.5 rounded-full bg-rose-400"></span><span class="size-2.5 rounded-full bg-amber-400"></span><span class="size-2.5 rounded-full bg-emerald-400"></span>
                <span class="ml-3 truncate text-xs text-slate-500">{{ $company->primaryHost() }}</span>
            </div>
            <iframe src="{{ route('websites.preview.frame', $company) }}" title="Preview" class="h-[78vh] w-full border-0"></iframe>
        </div>
    </div>
</x-layouts.app>
