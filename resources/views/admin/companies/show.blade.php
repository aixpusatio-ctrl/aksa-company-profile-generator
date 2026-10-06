<x-layouts.admin :title="$company->name">
    <x-page-header :title="$company->name" :description="$company->primaryHost()" :back="route('admin.companies.index')">
        <x-slot:actions>
            <a href="{{ route('websites.preview', $company) }}" target="_blank" class="btn btn-secondary"><x-icon name="eye" class="size-4" /> Preview</a>
            @if ($company->isPublished())
                <a href="{{ $company->publicUrl() }}" target="_blank" rel="noopener" class="btn btn-secondary"><x-icon name="external" class="size-4" /> Buka Website</a>
            @endif
            <form method="POST" action="{{ route('admin.companies.status', $company) }}">
                @csrf
                @if ($company->isPublished())
                    <button class="btn btn-secondary text-amber-700"><x-icon name="x-circle" class="size-4" /> Unpublish</button>
                @else
                    <button class="btn btn-success"><x-icon name="rocket" class="size-4" /> Publish</button>
                @endif
            </form>
            <x-confirm-delete :action="route('admin.companies.destroy', $company)" label="Hapus"
                message="Hapus website ini beserta seluruh kontennya? Tindakan ini tidak dapat dibatalkan." />
        </x-slot:actions>
    </x-page-header>

    @php
        $counts = [
            ['Layanan', $company->services_count, 'briefcase'],
            ['Produk', $company->products_count, 'cube'],
            ['Proyek', $company->projects_count, 'award'],
            ['Tim', $company->team_count, 'users'],
            ['Testimoni', $company->testimonials_count, 'quote'],
            ['Galeri', $company->gallery_count, 'photo'],
            ['Menu', $company->menus_count, 'menu'],
            ['Pesan', $company->contact_messages_count, 'mail'],
            ['Kunjungan', $company->page_views_count, 'chart'],
        ];
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="card">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Detail Website</h2>
                    <x-status-badge :status="$company->status" />
                </div>
                <dl class="grid gap-x-6 gap-y-4 p-6 text-sm sm:grid-cols-2">
                    <div><dt class="text-slate-500">Nama</dt><dd class="mt-0.5 font-medium text-slate-900">{{ $company->name }}</dd></div>
                    <div><dt class="text-slate-500">Tagline</dt><dd class="mt-0.5 font-medium text-slate-900">{{ $company->tagline ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Subdomain</dt><dd class="mt-0.5 font-medium text-slate-900 break-all">{{ $company->subdomainHost() }}</dd></div>
                    <div><dt class="text-slate-500">Template</dt><dd class="mt-0.5 font-medium text-slate-900">
                        @if ($company->template)
                            <a href="{{ route('admin.templates.edit', $company->template) }}" class="hover:text-brand-600">{{ $company->template->name }}</a>
                        @else — @endif
                    </dd></div>
                    <div><dt class="text-slate-500">Email</dt><dd class="mt-0.5 font-medium text-slate-900 break-all">{{ $company->email ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Telepon</dt><dd class="mt-0.5 font-medium text-slate-900">{{ $company->phone ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Kota</dt><dd class="mt-0.5 font-medium text-slate-900">{{ collect([$company->city, $company->province])->filter()->implode(', ') ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Tahun berdiri</dt><dd class="mt-0.5 font-medium text-slate-900">{{ $company->established_year ?: '—' }}</dd></div>
                    <div><dt class="text-slate-500">Dibuat</dt><dd class="mt-0.5 font-medium text-slate-900">{{ $company->created_at?->format('d M Y H:i') }}</dd></div>
                    <div><dt class="text-slate-500">Dipublikasikan</dt><dd class="mt-0.5 font-medium text-slate-900">{{ $company->published_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                    @if ($company->description)
                        <div class="sm:col-span-2"><dt class="text-slate-500">Deskripsi</dt><dd class="mt-0.5 text-slate-700">{{ $company->description }}</dd></div>
                    @endif
                </dl>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach ($counts as [$label, $count, $icon])
                    <div class="card flex items-center gap-3 px-4 py-3">
                        <span class="inline-flex size-9 items-center justify-center rounded-lg bg-slate-100 text-slate-500"><x-icon :name="$icon" class="size-4" /></span>
                        <div>
                            <p class="font-display text-lg leading-tight font-bold text-slate-900">{{ number_format((int) $count) }}</p>
                            <p class="text-xs text-slate-500">{{ $label }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Halaman Kustom</h2>
                </div>
                @if ($company->pages->isEmpty())
                    <p class="px-6 py-8 text-center text-sm text-slate-500">Belum ada halaman kustom.</p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($company->pages as $page)
                            <li class="flex items-center gap-3 px-6 py-3">
                                <x-icon name="document" class="size-4 shrink-0 text-slate-400" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-slate-900">{{ $page->title }}</p>
                                    <p class="truncate text-xs text-slate-500">/{{ $page->slug }}</p>
                                </div>
                                <x-status-badge :status="$page->status" />
                                <form method="POST" action="{{ route('admin.pages.status', $page) }}">
                                    @csrf
                                    <button class="btn btn-ghost btn-sm">{{ $page->isPublished() ? 'Unpublish' : 'Publish' }}</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="card card-body">
                <h2 class="font-display text-base font-semibold text-slate-900">Pemilik</h2>
                @if ($company->user)
                    <a href="{{ route('admin.users.show', $company->user) }}" class="mt-4 flex items-center gap-3 rounded-xl p-2 -m-2 hover:bg-slate-50">
                        <span class="inline-flex size-10 items-center justify-center rounded-full bg-brand-100 text-sm font-bold text-brand-700">{{ $company->user->initials() }}</span>
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $company->user->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $company->user->email }}</p>
                        </div>
                    </a>
                    <p class="mt-4 text-xs text-slate-500">Paket: <span class="font-medium text-slate-700">{{ $company->user->plan()['name'] }}</span></p>
                @else
                    <p class="mt-2 text-sm text-slate-500">Pemilik tidak ditemukan.</p>
                @endif
            </div>

            <div class="card">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h2 class="font-display text-base font-semibold text-slate-900">Domain</h2>
                    <a href="{{ route('admin.domains.index', ['q' => $company->slug]) }}" class="text-xs font-medium text-brand-600">Kelola</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    <li class="flex items-center gap-2 px-6 py-3 text-sm">
                        <x-icon name="link" class="size-4 text-slate-400" />
                        <span class="min-w-0 flex-1 truncate text-slate-700">{{ $company->subdomainHost() }}</span>
                        <span class="badge badge-slate">Subdomain</span>
                    </li>
                    @foreach ($company->domains as $domain)
                        <li class="px-6 py-3 text-sm">
                            <div class="flex items-center gap-2">
                                <x-icon name="globe" class="size-4 text-slate-400" />
                                <span class="min-w-0 flex-1 truncate font-medium text-slate-800">{{ $domain->domain }}</span>
                                <x-status-badge :status="$domain->status" />
                            </div>
                            @if ($domain->is_primary)<p class="mt-1 pl-6 text-xs text-emerald-600">Domain utama</p>@endif
                            @if ($domain->failure_reason)<p class="mt-1 pl-6 text-xs text-rose-600">{{ $domain->failure_reason }}</p>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-layouts.admin>
