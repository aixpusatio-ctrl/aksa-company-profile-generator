<x-layouts.app title="Settings">
    <x-page-header title="Settings" description="Kelola profil, keamanan dan paket langganan Anda." />

    <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
        <div class="space-y-6">
            <form method="POST" action="{{ route('settings.profile') }}" enctype="multipart/form-data" class="card">
                @csrf
                @method('PUT')
                <div class="border-b border-slate-100 px-6 py-4"><h2 class="text-base font-semibold text-slate-900">Profile</h2></div>
                <div class="card-body grid gap-5 sm:grid-cols-2">
                    <x-form.input name="name" label="Nama" :value="$user->name" required />
                    <x-form.input name="email" type="email" label="Email" :value="$user->email" required />
                    <x-form.input name="phone" label="Telepon" :value="$user->phone" />
                    <div>
                        <label class="form-label">Foto profil</label>
                        <input type="file" name="avatar" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-brand-700">
                        @error('avatar')<p class="form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="flex justify-end border-t border-slate-100 bg-slate-50/60 px-6 py-4"><button class="btn btn-primary">Simpan profil</button></div>
            </form>

            <form method="POST" action="{{ route('settings.password') }}" class="card">
                @csrf
                @method('PUT')
                <div class="border-b border-slate-100 px-6 py-4"><h2 class="text-base font-semibold text-slate-900">Password</h2></div>
                <div class="card-body grid gap-5 sm:grid-cols-3">
                    @foreach (['current_password' => 'Password saat ini', 'password' => 'Password baru', 'password_confirmation' => 'Konfirmasi password'] as $field => $label)
                        <div>
                            <label class="form-label" for="{{ $field }}">{{ $label }}</label>
                            <input id="{{ $field }}" type="password" name="{{ $field }}" class="form-input" autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}">
                            @error($field, 'password')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-end border-t border-slate-100 bg-slate-50/60 px-6 py-4"><button class="btn btn-primary">Ubah password</button></div>
            </form>

            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Company, Domain, SEO & Social Media</h2>
                    <p class="text-xs text-slate-500">Pengaturan ini berlaku per website.</p>
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($websites as $website)
                        <li class="flex flex-wrap items-center gap-2 px-6 py-3">
                            <span class="flex-1 text-sm font-semibold text-slate-900">{{ $website->name }}</span>
                            <a href="{{ route('websites.edit', [$website, 'info']) }}" class="btn btn-ghost btn-sm">Company</a>
                            <a href="{{ route('websites.domains.index', $website) }}" class="btn btn-ghost btn-sm">Domain</a>
                            <a href="{{ route('websites.edit', [$website, 'seo']) }}" class="btn btn-ghost btn-sm">SEO</a>
                            <a href="{{ route('websites.edit', [$website, 'info']) }}#f-social_links-facebook" class="btn btn-ghost btn-sm">Social Media</a>
                        </li>
                    @empty
                        <li class="px-6 py-6 text-sm text-slate-500">Belum ada website.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div id="plan" class="space-y-4">
            <div class="card card-body">
                <p class="text-sm text-slate-500">Paket aktif</p>
                <p class="font-display text-2xl font-bold text-slate-900">{{ $user->plan()['name'] }}</p>
                @if ($user->subscription)
                    <p class="mt-1 text-xs text-slate-500">
                        Status: <x-status-badge :status="$user->subscription->status" />
                        @if ($user->subscription->status === 'trialing' && $user->subscription->trial_ends_at) · berakhir {{ $user->subscription->trial_ends_at->format('d M Y') }} @endif
                    </p>
                @endif
                <ul class="mt-4 space-y-2 text-sm">
                    @foreach ($user->plan()['features'] as $feature)
                        <li class="flex gap-2"><x-icon name="check" class="size-4 text-emerald-600" /> {{ $feature }}</li>
                    @endforeach
                </ul>
            </div>
            @foreach ($plans as $key => $plan)
                @continue($key === $user->planKey())
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-slate-900">{{ $plan['name'] }}</p>
                        <p class="text-sm font-bold text-slate-900">{{ $plan['price'] ? 'Rp '.number_format($plan['price'], 0, ',', '.').'/bln' : 'Gratis' }}</p>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">{{ $plan['description'] }}</p>
                    <a href="mailto:{{ setting('support_email') }}?subject={{ rawurlencode('Upgrade ke '.$plan['name']) }}" class="btn btn-secondary btn-sm mt-3 w-full">Hubungi sales</a>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
