@php($editing = $user->exists)
<x-layouts.admin :title="$editing ? 'Edit User' : 'Tambah User'">
    <x-page-header :title="$editing ? 'Edit User' : 'Tambah User'"
        :description="$editing ? 'Perbarui data akun '.$user->email.'.' : 'Buat akun baru. Email langsung dianggap terverifikasi.'"
        :back="$editing ? route('admin.users.show', $user) : route('admin.users.index')" />

    <form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="max-w-3xl">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="card">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="font-display text-base font-semibold text-slate-900">Informasi Akun</h2>
            </div>
            <div class="card-body grid gap-5 sm:grid-cols-2">
                <x-form.input name="name" label="Nama lengkap" :value="$user->name" required class="sm:col-span-2" />
                <x-form.input name="email" type="email" label="Email" :value="$user->email" required />
                <x-form.input name="phone" label="Telepon" :value="$user->phone" placeholder="08xxxxxxxxxx" />
                <x-form.select name="role" label="Role" :value="$user->role" :options="['user' => 'User', 'admin' => 'Admin']"
                    help="Admin memiliki akses penuh ke panel ini." />
            </div>
        </div>

        <div class="card mt-6">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="font-display text-base font-semibold text-slate-900">Password</h2>
                <p class="text-sm text-slate-500">{{ $editing ? 'Kosongkan jika tidak ingin mengubah password.' : 'Minimal 8 karakter.' }}</p>
            </div>
            <div class="card-body grid gap-5 sm:grid-cols-2">
                @if ($editing)
                    <x-form.input name="password" type="password" label="Password baru" autocomplete="new-password" />
                    <x-form.input name="password_confirmation" type="password" label="Konfirmasi password" autocomplete="new-password" />
                @else
                    <x-form.input name="password" type="password" label="Password" autocomplete="new-password" required />
                    <x-form.input name="password_confirmation" type="password" label="Konfirmasi password" autocomplete="new-password" required />
                @endif
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-2">
            <a href="{{ $editing ? route('admin.users.show', $user) : route('admin.users.index') }}" class="btn btn-ghost">Batal</a>
            <button class="btn btn-primary"><x-icon name="check" class="size-4" /> {{ $editing ? 'Simpan Perubahan' : 'Buat User' }}</button>
        </div>
    </form>
</x-layouts.admin>
