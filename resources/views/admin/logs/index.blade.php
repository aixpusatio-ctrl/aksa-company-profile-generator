<x-layouts.admin title="System Logs">
    <x-page-header title="System Logs" description="Jejak aktivitas pengguna dan administrator di seluruh platform." />

    @php
        $badge = fn ($a) => match (\Illuminate\Support\Str::before($a, '.')) {
            'auth' => 'badge-blue', 'website' => 'badge-green', 'domain' => 'badge-violet',
            'template' => 'badge-brand', 'admin' => 'badge-amber', default => 'badge-slate',
        };
    @endphp

    <div class="mb-4 flex flex-col gap-3 border-b border-slate-200 sm:flex-row sm:items-end sm:justify-between">
        <div class="flex gap-1 overflow-x-auto">
            <a href="{{ route('admin.logs.index', array_filter(['q' => $search])) }}"
               class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap {{ $action === '' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Semua</a>
            @foreach ($actions as $a)
                <a href="{{ route('admin.logs.index', array_filter(['action' => $a, 'q' => $search])) }}"
                   class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap {{ $action === $a ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">{{ ucfirst($a) }}</a>
            @endforeach
        </div>
        <form method="GET" class="mb-3 flex gap-2">
            @if ($action)<input type="hidden" name="action" value="{{ $action }}">@endif
            <div class="relative flex-1 sm:w-64">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ $search }}" placeholder="Cari deskripsi..." class="form-input pl-9">
            </div>
            <button class="btn btn-dark">Cari</button>
        </form>
    </div>

    @if ($logs->isEmpty())
        <x-empty-state icon="clipboard" title="Belum ada log" description="Tidak ada aktivitas yang cocok dengan filter Anda." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($logs as $log)
                            <tr>
                                <td class="whitespace-nowrap">
                                    <p class="text-slate-800">{{ $log->created_at?->format('d M Y') }}</p>
                                    <p class="text-xs text-slate-500">{{ $log->created_at?->format('H:i:s') }}</p>
                                </td>
                                <td class="whitespace-nowrap">
                                    @if ($log->user)
                                        <a href="{{ route('admin.users.show', $log->user) }}" class="text-slate-700 hover:text-brand-600">{{ $log->user->name }}</a>
                                    @else
                                        <span class="text-slate-400">Sistem</span>
                                    @endif
                                </td>
                                <td><span class="badge {{ $badge($log->action) }} font-mono whitespace-nowrap">{{ $log->action }}</span></td>
                                <td class="min-w-64 text-slate-700">{{ $log->description }}</td>
                                <td class="font-mono text-xs whitespace-nowrap text-slate-500">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $logs->links() }}</div>
    @endif
</x-layouts.admin>
