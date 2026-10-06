<x-layouts.admin title="Domains">
    <x-page-header title="Domains" description="Pantau dan kelola custom domain yang dihubungkan pengguna." />

    @php($statuses = \App\Models\Domain::STATUSES)
    <div class="mb-4 flex gap-1 overflow-x-auto border-b border-slate-200">
        <a href="{{ route('admin.domains.index', array_filter(['q' => $search])) }}"
           class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap {{ $status === '' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Semua</a>
        @foreach ($statuses as $s)
            <a href="{{ route('admin.domains.index', array_filter(['status' => $s, 'q' => $search])) }}"
               class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap {{ $status === $s ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">{{ ucfirst($s) }}</a>
        @endforeach
    </div>

    <form method="GET" class="mb-6 flex gap-2">
        @if ($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <div class="relative flex-1 sm:max-w-sm">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari domain..." class="form-input pl-9">
        </div>
        <button class="btn btn-dark">Cari</button>
    </form>

    @if ($domains->isEmpty())
        <x-empty-state icon="globe" title="Tidak ada domain" description="Belum ada custom domain yang cocok dengan filter Anda." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Website</th>
                            <th>Tipe</th>
                            <th>Status</th>
                            <th>Terverifikasi</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($domains as $domain)
                            @php($company = $domain->companyProfile)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-slate-900">{{ $domain->domain }}</span>
                                        @if ($domain->is_primary)<span class="badge badge-brand">Utama</span>@endif
                                    </div>
                                    @if ($domain->failure_reason)
                                        <p class="mt-0.5 max-w-xs text-xs text-rose-600">{{ $domain->failure_reason }}</p>
                                    @elseif ($domain->last_checked_at)
                                        <p class="mt-0.5 text-xs text-slate-400">Dicek {{ $domain->last_checked_at->diffForHumans() }}</p>
                                    @endif
                                </td>
                                <td>
                                    @if ($company)
                                        <a href="{{ route('admin.companies.show', $company) }}" class="text-slate-700 hover:text-brand-600">{{ $company->name }}</a>
                                        <p class="text-xs text-slate-500">{{ $company->user?->name ?? '—' }}</p>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td><span class="badge badge-slate">{{ $domain->type === 'apex' ? 'Apex (A)' : 'Subdomain (CNAME)' }}</span></td>
                                <td><x-status-badge :status="$domain->status" /></td>
                                <td class="whitespace-nowrap text-slate-500">{{ $domain->verified_at?->format('d M Y H:i') ?? '—' }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($domain->isActive())
                                            <a href="{{ $domain->url() }}" target="_blank" rel="noopener" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Buka"><x-icon name="external" class="size-4" /></a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.domains.verify', $domain) }}">
                                            @csrf
                                            <button class="btn btn-secondary btn-sm" title="Jalankan verifikasi DNS"><x-icon name="refresh" class="size-3.5" /> Verifikasi</button>
                                        </form>
                                        @unless ($domain->isActive())
                                            <form method="POST" action="{{ route('admin.domains.activate', $domain) }}" onsubmit="return confirm('Aktifkan domain ini secara manual tanpa verifikasi DNS?')">
                                                @csrf
                                                <button class="btn btn-success btn-sm"><x-icon name="check" class="size-3.5" /> Aktifkan</button>
                                            </form>
                                        @endunless
                                        <x-confirm-delete :action="route('admin.domains.destroy', $domain)" message="Hapus domain ini?" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $domains->links() }}</div>
    @endif
</x-layouts.admin>
