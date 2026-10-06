<x-layouts.app title="Notifikasi">
    <x-page-header title="Notifikasi">
        <x-slot:actions>
            <form method="POST" action="{{ route('notifications.read') }}">@csrf<button class="btn btn-secondary">Tandai semua dibaca</button></form>
        </x-slot:actions>
    </x-page-header>
    @if ($notifications->isEmpty())
        <x-empty-state title="Belum ada notifikasi" icon="bell" />
    @else
        <div class="card divide-y divide-slate-100">
            @foreach ($notifications as $notification)
                <a href="{{ $notification->data['url'] ?? '#' }}" class="flex gap-4 px-6 py-4 hover:bg-slate-50 {{ $notification->read_at ? '' : 'bg-brand-50/40' }}">
                    <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-600"><x-icon :name="$notification->data['icon'] ?? 'bell'" class="size-5" /></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-semibold text-slate-900">{{ $notification->data['title'] ?? '' }}</span>
                        <span class="block text-sm text-slate-600">{{ $notification->data['message'] ?? '' }}</span>
                    </span>
                    <span class="shrink-0 text-xs text-slate-400">{{ $notification->created_at->diffForHumans() }}</span>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
</x-layouts.app>
