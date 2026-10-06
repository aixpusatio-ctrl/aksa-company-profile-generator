<x-layouts.admin title="Company Profiles">
    <x-page-header title="Company Profiles" description="Semua website company profile yang dibuat pengguna." />

    <form method="GET" class="card card-body mb-6 grid gap-3 sm:grid-cols-[1fr_auto_auto_auto]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau slug..." class="form-input pl-9">
        </div>
        <select name="status" class="form-input sm:w-40">
            <option value="">Semua status</option>
            <option value="published" @selected($status === 'published')>Published</option>
            <option value="draft" @selected($status === 'draft')>Draft</option>
        </select>
        <select name="template" class="form-input sm:w-48">
            <option value="">Semua template</option>
            @foreach ($templates as $tpl)
                <option value="{{ $tpl->id }}" @selected($template === $tpl->id)>{{ $tpl->name }}</option>
            @endforeach
        </select>
        <div class="flex gap-2">
            <button class="btn btn-dark">Filter</button>
            @if ($search || $status || $template)
                <a href="{{ route('admin.companies.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($companies->isEmpty())
        <x-empty-state icon="building" title="Tidak ada website" description="Belum ada website yang cocok dengan filter Anda." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Website</th>
                            <th>Pemilik</th>
                            <th>Template</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($companies as $company)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.companies.show', $company) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $company->name }}</a>
                                    <p class="text-xs text-slate-500">{{ $company->subdomainHost() }}</p>
                                    @if ($company->primaryDomain)
                                        <p class="text-xs text-emerald-600">{{ $company->primaryDomain->domain }}</p>
                                    @endif
                                </td>
                                <td>
                                    @if ($company->user)
                                        <a href="{{ route('admin.users.show', $company->user) }}" class="text-slate-700 hover:text-brand-600">{{ $company->user->name }}</a>
                                        <p class="text-xs text-slate-500">{{ $company->user->email }}</p>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-slate-600">{{ $company->template?->name ?? '—' }}</td>
                                <td><x-status-badge :status="$company->status" /></td>
                                <td class="whitespace-nowrap text-slate-500">{{ $company->created_at?->format('d M Y') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.companies.show', $company) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Detail"><x-icon name="eye" class="size-4" /></a>
                                        @if ($company->isPublished())
                                            <a href="{{ $company->publicUrl() }}" target="_blank" rel="noopener" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Buka website"><x-icon name="external" class="size-4" /></a>
                                        @endif
                                        <form method="POST" action="{{ route('admin.companies.status', $company) }}">
                                            @csrf
                                            <button class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 {{ $company->isPublished() ? 'hover:text-amber-600' : 'hover:text-emerald-600' }}"
                                                title="{{ $company->isPublished() ? 'Unpublish' : 'Publish' }}">
                                                <x-icon :name="$company->isPublished() ? 'x-circle' : 'rocket'" class="size-4" />
                                            </button>
                                        </form>
                                        <x-confirm-delete :action="route('admin.companies.destroy', $company)" message="Hapus website ini beserta seluruh kontennya? Tindakan ini tidak dapat dibatalkan." />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $companies->links() }}</div>
    @endif
</x-layouts.admin>
