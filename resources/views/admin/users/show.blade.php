<x-layouts.admin :title="$user->name">
    <x-page-header :title="$user->name" :description="$user->email" :back="route('admin.users.index')">
        <x-slot:actions>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary"><x-icon name="pencil" class="size-4" /> Edit</a>
            @unless ($user->is(auth()->user()))
                @if ($user->isSuspended())
                    <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}">
                        @csrf
                        <button class="btn btn-success"><x-icon name="check-circle" class="size-4" /> Aktifkan</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" onsubmit="return confirm('Tangguhkan user ini? Website miliknya tidak akan dapat diakses publik.')">
                        @csrf
                        <button class="btn btn-secondary text-amber-700"><x-icon name="lock" class="size-4" /> Suspend</button>
                    </form>
                @endif
            @endunless
            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" onsubmit="return confirm('Reset password user ini? Password baru akan ditampilkan sekali.')">
                @csrf
                <button class="btn btn-secondary"><x-icon name="refresh" class="size-4" /> Reset Password</button>
            </form>
            @unless ($user->is(auth()->user()))
                <x-confirm-delete :action="route('admin.users.destroy', $user)" label="Hapus"
                    message="Hapus user ini beserta seluruh website dan datanya? Tindakan ini tidak dapat dibatalkan." />
            @endunless
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Profile card --}}
        <div class="space-y-6">
            <div class="card card-body text-center">
                <span class="mx-auto inline-flex size-20 items-center justify-center rounded-full {{ $user->isAdmin() ? 'bg-slate-900 text-white' : 'bg-brand-100 text-brand-700' }} font-display text-2xl font-bold">{{ $user->initials() }}</span>
                <h2 class="mt-4 font-display text-lg font-semibold text-slate-900">{{ $user->name }}</h2>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <div class="mt-3 flex flex-wrap justify-center gap-2">
                    <span class="badge {{ $user->isAdmin() ? 'badge-brand' : 'badge-slate' }}">{{ ucfirst($user->role) }}</span>
                    <x-status-badge :status="$user->isSuspended() ? 'suspended' : 'active'" />
                </div>
            </div>

            <div class="card">
                <dl class="divide-y divide-slate-100 text-sm">
                    <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">Telepon</dt><dd class="text-right font-medium text-slate-800">{{ $user->phone ?: '—' }}</dd></div>
                    <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">Paket</dt><dd class="text-right font-medium text-slate-800">{{ $user->plan()['name'] }}</dd></div>
                    @if ($user->subscription)
                        <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">Langganan</dt><dd class="text-right"><x-status-badge :status="$user->subscription->status" /></dd></div>
                    @endif
                    <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">Website</dt><dd class="text-right font-medium text-slate-800">{{ $user->companyProfiles->count() }}</dd></div>
                    <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">File media</dt><dd class="text-right font-medium text-slate-800">{{ $user->media_count }}</dd></div>
                    <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">Email terverifikasi</dt><dd class="text-right font-medium text-slate-800">{{ $user->email_verified_at?->format('d M Y') ?? 'Belum' }}</dd></div>
                    <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">Login terakhir</dt><dd class="text-right font-medium text-slate-800">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</dd></div>
                    <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">Terdaftar</dt><dd class="text-right font-medium text-slate-800">{{ $user->created_at?->format('d M Y H:i') }}</dd></div>
                    @if ($user->isSuspended())
                        <div class="flex justify-between gap-4 px-6 py-3"><dt class="text-slate-500">Ditangguhkan</dt><dd class="text-right font-medium text-rose-600">{{ $user->suspended_at->format('d M Y H:i') }}</dd></div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="space-y-6 lg:col-span-2">
            {{-- Websites --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Website</h2>
                    <p class="text-sm text-slate-500">Company profile milik user ini.</p>
                </div>
                @if ($user->companyProfiles->isEmpty())
                    <p class="px-6 py-10 text-center text-sm text-slate-500">User ini belum membuat website.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($user->companyProfiles as $company)
                            <li class="flex flex-col gap-3 px-6 py-4 sm:flex-row sm:items-center">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.companies.show', $company) }}" class="truncate font-medium text-slate-900 hover:text-brand-600">{{ $company->name }}</a>
                                        <x-status-badge :status="$company->status" />
                                    </div>
                                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $company->primaryHost() }} · {{ $company->template?->name ?? 'Tanpa template' }}</p>
                                </div>
                                <div class="flex shrink-0 gap-2">
                                    <a href="{{ route('admin.companies.show', $company) }}" class="btn btn-secondary btn-sm"><x-icon name="eye" class="size-3.5" /> Detail</a>
                                    @if ($company->isPublished())
                                        <a href="{{ $company->publicUrl() }}" target="_blank" rel="noopener" class="btn btn-ghost btn-sm"><x-icon name="external" class="size-3.5" /> Buka</a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            {{-- Activity --}}
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Aktivitas Terbaru</h2>
                </div>
                @if ($activity->isEmpty())
                    <p class="px-6 py-10 text-center text-sm text-slate-500">Belum ada aktivitas tercatat.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($activity as $log)
                            <li class="flex items-start gap-3 px-6 py-3">
                                <span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-400"></span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-slate-800">{{ $log->description }}</p>
                                    <p class="mt-0.5 font-mono text-xs text-slate-500">{{ $log->action }} @if ($log->ip_address)· {{ $log->ip_address }}@endif</p>
                                </div>
                                <span class="text-xs whitespace-nowrap text-slate-400" title="{{ $log->created_at?->format('d M Y H:i') }}">{{ $log->created_at?->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
