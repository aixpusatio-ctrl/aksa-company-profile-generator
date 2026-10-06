<x-layouts.admin title="Pages">
    <x-page-header title="Pages" description="Moderasi halaman kustom dari seluruh website pengguna." />

    @php($status = request('status'))
    <form method="GET" class="card card-body mb-6 grid gap-3 sm:grid-cols-[1fr_auto_auto]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari judul halaman..." class="form-input pl-9">
        </div>
        <select name="status" class="form-input sm:w-40">
            <option value="">Semua status</option>
            <option value="published" @selected($status === 'published')>Published</option>
            <option value="draft" @selected($status === 'draft')>Draft</option>
        </select>
        <div class="flex gap-2">
            <button class="btn btn-dark">Filter</button>
            @if ($search || $status)
                <a href="{{ route('admin.pages.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($pages->isEmpty())
        <x-empty-state icon="document" title="Tidak ada halaman" description="Belum ada halaman kustom yang cocok dengan filter Anda." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Halaman</th>
                            <th>Website</th>
                            <th>Status</th>
                            <th>Diperbarui</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($pages as $page)
                            @php($company = $page->companyProfile)
                            <tr>
                                <td>
                                    <p class="font-medium text-slate-900">{{ $page->title }}</p>
                                    <p class="font-mono text-xs text-slate-500">/{{ $page->slug }}</p>
                                </td>
                                <td>
                                    @if ($company)
                                        <a href="{{ route('admin.companies.show', $company) }}" class="text-slate-700 hover:text-brand-600">{{ $company->name }}</a>
                                        <p class="text-xs text-slate-500">{{ $company->user?->name ?? '—' }}</p>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td><x-status-badge :status="$page->status" /></td>
                                <td class="whitespace-nowrap text-slate-500" title="{{ $page->updated_at?->format('d M Y H:i') }}">{{ $page->updated_at?->diffForHumans() }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        @if ($company && $company->isPublished() && $page->isPublished())
                                            <a href="{{ rtrim($company->publicUrl(), '/') }}/{{ $page->slug }}" target="_blank" rel="noopener" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Buka"><x-icon name="external" class="size-4" /></a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.pages.status', $page) }}">
                                            @csrf
                                            <button class="btn btn-secondary btn-sm">{{ $page->isPublished() ? 'Unpublish' : 'Publish' }}</button>
                                        </form>
                                        <x-confirm-delete :action="route('admin.pages.destroy', $page)" message="Hapus halaman ini secara permanen?" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $pages->links() }}</div>
    @endif
</x-layouts.admin>
