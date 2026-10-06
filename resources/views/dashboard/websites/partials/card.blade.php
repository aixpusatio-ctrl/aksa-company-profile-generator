{{-- Website card (dashboard + My Websites). --}}
<div class="card group flex flex-col overflow-hidden">
    <a href="{{ route('websites.show', $website) }}" class="relative block border-b border-slate-100">
        <x-template-thumb :src="route('websites.preview.frame', $website)" />
        <span class="absolute top-3 left-3"><x-status-badge :status="$website->status" class="bg-white" /></span>
    </a>
    <div class="flex flex-1 flex-col p-5">
        <h3 class="truncate text-base font-bold text-slate-900">{{ $website->name }}</h3>
        <dl class="mt-3 space-y-1.5 text-xs">
            <div class="flex items-center justify-between gap-2"><dt class="text-slate-500">Template</dt><dd class="truncate font-medium text-slate-700">{{ $website->template?->name ?? '—' }}</dd></div>
            <div class="flex items-center justify-between gap-2"><dt class="text-slate-500">Domain</dt><dd class="truncate font-medium text-slate-700">{{ $website->primaryHost() }}</dd></div>
            <div class="flex items-center justify-between gap-2"><dt class="text-slate-500">Diperbarui</dt><dd class="font-medium text-slate-700">{{ $website->updated_at->diffForHumans() }}</dd></div>
        </dl>
        <div class="mt-5 grid grid-cols-3 gap-2">
            <a href="{{ $website->wizard_step < 11 && ! $website->isPublished() ? route('websites.wizard', $website) : route('websites.edit', [$website, 'info']) }}" class="btn btn-secondary btn-sm">Edit</a>
            <a href="{{ route('websites.preview', $website) }}" class="btn btn-secondary btn-sm">Preview</a>
            <a href="{{ route('websites.show', $website) }}" class="btn btn-primary btn-sm">Manage</a>
        </div>
    </div>
</div>
