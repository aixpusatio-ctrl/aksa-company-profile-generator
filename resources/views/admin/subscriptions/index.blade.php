<x-layouts.admin title="Subscriptions">
    <x-page-header title="Subscriptions" description="Paket langganan pengguna dan pendapatan berulang bulanan." />

    @php($rp = fn ($v) => 'Rp '.number_format((float) $v, 0, ',', '.'))
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-stat-card label="MRR" :value="$rp($mrr)" icon="banknotes" color="green" hint="Total langganan aktif / bulan" class="xl:col-span-1" />
        <x-stat-card label="Active" :value="number_format($counts['active'] ?? 0)" icon="check-circle" color="green" />
        <x-stat-card label="Trial" :value="number_format($counts['trialing'] ?? 0)" icon="clock" color="violet" />
        <x-stat-card label="Past due" :value="number_format($counts['past_due'] ?? 0)" icon="warning" color="amber" />
        <x-stat-card label="Canceled" :value="number_format($counts['canceled'] ?? 0)" icon="x-circle" color="rose" />
    </div>

    <div class="mt-8 mb-4 flex gap-1 overflow-x-auto border-b border-slate-200">
        <a href="{{ route('admin.subscriptions.index') }}"
           class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap {{ $plan === '' ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">Semua paket</a>
        @foreach ($plans as $key => $p)
            <a href="{{ route('admin.subscriptions.index', ['plan' => $key]) }}"
               class="-mb-px border-b-2 px-4 py-2.5 text-sm font-medium whitespace-nowrap {{ $plan === $key ? 'border-brand-600 text-brand-700' : 'border-transparent text-slate-500 hover:text-slate-800' }}">{{ $p['name'] }}</a>
        @endforeach
    </div>

    @if ($users->isEmpty())
        <x-empty-state icon="credit-card" title="Tidak ada pelanggan" description="Belum ada user pada paket ini." />
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Paket</th>
                            <th>Status</th>
                            <th>Website</th>
                            <th>Berakhir / Trial</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($users as $user)
                            @php($sub = $user->subscription)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-slate-900 hover:text-brand-600">{{ $user->name }}</a>
                                    <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                </td>
                                <td>
                                    <p class="font-medium whitespace-nowrap text-slate-800">{{ $sub?->planName() ?? 'Starter (Free)' }}</p>
                                    @if ($sub && $sub->price)<p class="text-xs text-slate-500">{{ $rp($sub->price) }}/bln</p>@endif
                                </td>
                                <td>
                                    @if ($sub)
                                        <x-status-badge :status="$sub->status" />
                                    @else
                                        <span class="badge badge-slate">Free</span>
                                    @endif
                                </td>
                                <td class="text-slate-600">{{ $user->company_profiles_count }}</td>
                                <td class="whitespace-nowrap text-slate-500">
                                    @if ($sub?->status === 'trialing' && $sub->trial_ends_at)
                                        Trial s/d {{ $sub->trial_ends_at->format('d M Y') }}
                                    @elseif ($sub?->ends_at)
                                        {{ $sub->ends_at->format('d M Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-right">
                                    <button type="button" x-data @click="$dispatch('open-modal', 'sub-{{ $user->id }}')" class="btn btn-secondary btn-sm"><x-icon name="pencil" class="size-3.5" /> Ubah</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-6">{{ $users->links() }}</div>

        @foreach ($users as $user)
            @php($sub = $user->subscription)
            <x-modal name="sub-{{ $user->id }}" title="Ubah Langganan">
                <form method="POST" action="{{ route('admin.subscriptions.update', $user) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <p class="rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-600">{{ $user->name }} · {{ $user->email }}</p>
                    <div>
                        <label class="form-label" for="plan-{{ $user->id }}">Paket</label>
                        <select id="plan-{{ $user->id }}" name="plan" class="form-input">
                            @foreach ($plans as $key => $p)
                                <option value="{{ $key }}" @selected(($sub?->plan ?? 'free') === $key)>{{ $p['name'] }} — {{ $p['price'] ? $rp($p['price']).'/bln' : 'Gratis' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="status-{{ $user->id }}">Status</label>
                        <select id="status-{{ $user->id }}" name="status" class="form-input">
                            @foreach (['trialing' => 'Trial', 'active' => 'Active', 'past_due' => 'Past due', 'canceled' => 'Canceled'] as $value => $label)
                                <option value="{{ $value }}" @selected(($sub?->status ?? 'active') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label" for="ends-{{ $user->id }}">Berakhir pada</label>
                        <input id="ends-{{ $user->id }}" type="date" name="ends_at" value="{{ $sub?->ends_at?->format('Y-m-d') }}" class="form-input">
                        <p class="form-help">Kosongkan untuk langganan tanpa batas waktu.</p>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" class="btn btn-ghost" @click="open = false">Batal</button>
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </x-modal>
        @endforeach
    @endif
</x-layouts.admin>
