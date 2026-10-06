<x-layouts.admin title="Users">
    <x-page-header title="Users" description="Kelola seluruh akun pengguna dan administrator platform.">
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Tambah User</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="card card-body mb-6 grid gap-3 sm:grid-cols-[1fr_auto_auto_auto]">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" />
            <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau email..." class="form-input pl-9">
        </div>
        <select name="role" class="form-input sm:w-40">
            <option value="">Semua role</option>
            <option value="admin" @selected($role === 'admin')>Admin</option>
            <option value="user" @selected($role === 'user')>User</option>
        </select>
        <select name="status" class="form-input sm:w-40">
            <option value="">Semua status</option>
            <option value="active" @selected($status === 'active')>Aktif</option>
            <option value="suspended" @selected($status === 'suspended')>Ditangguhkan</option>
        </select>
        <div class="flex gap-2">
            <button class="btn btn-dark">Filter</button>
            @if ($search || $role || $status)
                <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Reset</a>
            @endif
        </div>
    </form>

    @if ($users->isEmpty())
        <x-empty-state icon="users" title="Tidak ada user" description="Tidak ada user yang cocok dengan filter Anda." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Paket</th>
                            <th>Website</th>
                            <th>Status</th>
                            <th>Terdaftar</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full {{ $user->isAdmin() ? 'bg-slate-900 text-white' : 'bg-brand-100 text-brand-700' }} text-xs font-bold">{{ $user->initials() }}</span>
                                        <div class="min-w-0">
                                            <a href="{{ route('admin.users.show', $user) }}" class="block truncate font-medium text-slate-900 hover:text-brand-600">{{ $user->name }}</a>
                                            <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge {{ $user->isAdmin() ? 'badge-brand' : 'badge-slate' }}">{{ ucfirst($user->role) }}</span></td>
                                <td class="whitespace-nowrap text-slate-600">{{ $user->plan()['name'] }}</td>
                                <td class="text-slate-600">{{ $user->company_profiles_count }}</td>
                                <td>
                                    @if ($user->isSuspended())
                                        <x-status-badge status="suspended" />
                                    @else
                                        <x-status-badge status="active" />
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-slate-500">{{ $user->created_at?->format('d M Y') }}</td>
                                <td>
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.users.show', $user) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Lihat"><x-icon name="eye" class="size-4" /></a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit"><x-icon name="pencil" class="size-4" /></a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>
    @endif
</x-layouts.admin>
