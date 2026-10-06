<x-layouts.admin title="Admin Dashboard">
    <x-page-header title="Admin Dashboard" description="Ringkasan aktivitas platform: pengguna, website, domain dan template.">
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="btn btn-secondary"><x-icon name="plus" class="size-4" /> Tambah User</a>
            <a href="{{ route('admin.templates.create') }}" class="btn btn-primary"><x-icon name="template" class="size-4" /> Template Baru</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Users" :value="number_format($stats['users'])" icon="users" color="brand" />
        <x-stat-card label="Total Websites" :value="number_format($stats['websites'])" icon="building" color="violet" />
        <x-stat-card label="Published Websites" :value="number_format($stats['published'])" icon="rocket" color="green"
            :hint="$stats['websites'] ? round($stats['published'] / $stats['websites'] * 100).'% dari seluruh website' : null" />
        <x-stat-card label="Active Domains" :value="number_format($stats['domains'])" icon="globe" color="sky" />
        <x-stat-card label="Templates" :value="number_format($stats['templates'])" icon="template" color="slate" />
        <x-stat-card label="New Users (30d)" :value="number_format($stats['new_users'])" icon="user" color="brand" hint="Pendaftaran 30 hari terakhir" />
        <x-stat-card label="New Websites (30d)" :value="number_format($stats['new_websites'])" icon="sparkles" color="violet" hint="Website dibuat 30 hari terakhir" />
        <x-stat-card label="Pending Domains" :value="number_format($stats['pending_domains'])" icon="clock" color="amber" hint="Menunggu / sedang diverifikasi" />
    </div>

    {{-- Online shop overview --}}
    @isset($shopStats)
        <div class="mt-8 flex items-center justify-between">
            <h2 class="font-display text-base font-semibold text-slate-900">E-Commerce</h2>
            <a href="{{ route('admin.shop.shops') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Lihat semua toko</a>
        </div>
        <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Toko Aktif" :value="number_format($shopStats['shops'])" icon="briefcase" color="brand" />
            <x-stat-card label="Total Pesanan" :value="number_format($shopStats['orders'])" icon="list" color="sky" />
            <x-stat-card label="Pesanan Berjalan" :value="number_format($shopStats['open_orders'])" icon="clock" color="amber" hint="Belum selesai / dibatalkan" />
            <x-stat-card label="GMV" :value="\App\Support\Shop\Money::format($shopStats['gmv'])" icon="banknotes" color="green" hint="Pesanan lunas di semua toko" />
        </div>
    @endisset

    {{-- 14-day chart --}}
    @php($max = max(1, collect($chart)->max(fn ($d) => max($d['users'], $d['websites']))))
    <div class="card mt-8">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-display text-base font-semibold text-slate-900">Pertumbuhan 14 Hari Terakhir</h2>
                <p class="text-sm text-slate-500">User baru dan website baru per hari.</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium text-slate-600">
                <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-brand-500"></span> Users ({{ collect($chart)->sum('users') }})</span>
                <span class="inline-flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-violet-400"></span> Websites ({{ collect($chart)->sum('websites') }})</span>
            </div>
        </div>
        <div class="overflow-x-auto px-6 pt-6 pb-4">
            <div class="relative min-w-[560px]">
                <div class="pointer-events-none absolute inset-x-0 top-0 h-48">
                    @foreach ([0, 50, 100] as $line)
                        <div class="absolute inset-x-0 border-t border-dashed border-slate-100" style="bottom: {{ $line }}%"></div>
                    @endforeach
                </div>
                <div class="relative grid h-48 grid-cols-14 items-end gap-2" style="grid-template-columns: repeat({{ count($chart) }}, minmax(0, 1fr))">
                    @foreach ($chart as $day)
                        <div class="group relative flex h-full items-end justify-center gap-1">
                            <div class="w-full max-w-4 rounded-t bg-brand-500 transition group-hover:bg-brand-600" style="height: {{ $day['users'] ? max(3, $day['users'] / $max * 100) : 0 }}%"></div>
                            <div class="w-full max-w-4 rounded-t bg-violet-400 transition group-hover:bg-violet-500" style="height: {{ $day['websites'] ? max(3, $day['websites'] / $max * 100) : 0 }}%"></div>
                            <div class="pointer-events-none absolute -top-2 left-1/2 z-10 hidden -translate-x-1/2 -translate-y-full rounded-lg bg-slate-900 px-2.5 py-1.5 text-[11px] whitespace-nowrap text-white shadow-lg group-hover:block">
                                <p class="font-semibold">{{ \Illuminate\Support\Carbon::parse($day['date'])->translatedFormat('d M Y') }}</p>
                                <p>{{ $day['users'] }} user · {{ $day['websites'] }} website</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-2 grid gap-2 border-t border-slate-200 pt-2" style="grid-template-columns: repeat({{ count($chart) }}, minmax(0, 1fr))">
                    @foreach ($chart as $day)
                        <span class="text-center text-[11px] text-slate-400">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d/m') }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        {{-- Latest users --}}
        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="font-display text-base font-semibold text-slate-900">User Terbaru</h2>
                <a href="{{ route('admin.users.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Lihat semua</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($latestUsers as $u)
                    <li>
                        <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50">
                            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">{{ $u->initials() }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-900">{{ $u->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $u->email }}</p>
                            </div>
                            @if ($u->isAdmin())<span class="badge badge-brand">Admin</span>@endif
                            <span class="text-xs whitespace-nowrap text-slate-400">{{ $u->created_at?->diffForHumans() }}</span>
                        </a>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-sm text-slate-500">Belum ada user.</li>
                @endforelse
            </ul>
        </div>

        {{-- Latest websites --}}
        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="font-display text-base font-semibold text-slate-900">Website Terbaru</h2>
                <a href="{{ route('admin.companies.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Lihat semua</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($latestWebsites as $site)
                    <li>
                        <a href="{{ route('admin.companies.show', $site) }}" class="flex items-center gap-3 px-6 py-3 hover:bg-slate-50">
                            <span class="inline-flex size-9 shrink-0 items-center justify-center rounded-lg bg-violet-50 text-violet-600"><x-icon name="building" class="size-5" /></span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-slate-900">{{ $site->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $site->user?->name ?? '—' }} · {{ $site->template?->name ?? 'Tanpa template' }}</p>
                            </div>
                            <x-status-badge :status="$site->status" />
                        </a>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-sm text-slate-500">Belum ada website.</li>
                @endforelse
            </ul>
        </div>

        {{-- Popular templates --}}
        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="font-display text-base font-semibold text-slate-900">Template Terpopuler</h2>
                <a href="{{ route('admin.templates.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Kelola</a>
            </div>
            @php($topUsage = max(1, (int) $popularTemplates->max('company_profiles_count')))
            <ul class="space-y-4 px-6 py-5">
                @forelse ($popularTemplates as $tpl)
                    <li>
                        <div class="mb-1.5 flex items-center justify-between gap-3 text-sm">
                            <a href="{{ route('admin.templates.edit', $tpl) }}" class="truncate font-medium text-slate-800 hover:text-brand-600">{{ $tpl->name }}</a>
                            <span class="shrink-0 text-xs text-slate-500">{{ $tpl->company_profiles_count }} website</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500" style="width: {{ $tpl->company_profiles_count / $topUsage * 100 }}%"></div>
                        </div>
                    </li>
                @empty
                    <li class="py-4 text-center text-sm text-slate-500">Belum ada template.</li>
                @endforelse
            </ul>
        </div>

        {{-- Activity --}}
        <div class="card">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h2 class="font-display text-base font-semibold text-slate-900">Aktivitas Terbaru</h2>
                <a href="{{ route('admin.logs.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Semua log</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($activity as $log)
                    <li class="flex items-start gap-3 px-6 py-3">
                        <span class="mt-1.5 size-2 shrink-0 rounded-full bg-brand-400"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-slate-800">{{ $log->description }}</p>
                            <p class="mt-0.5 text-xs text-slate-500">{{ $log->user?->name ?? 'Sistem' }} · <span class="font-mono">{{ $log->action }}</span></p>
                        </div>
                        <span class="text-xs whitespace-nowrap text-slate-400">{{ $log->created_at?->diffForHumans() }}</span>
                    </li>
                @empty
                    <li class="px-6 py-8 text-center text-sm text-slate-500">Belum ada aktivitas.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-layouts.admin>
