<x-website-layout :company="$company" title="Overview">
    @if (session('published'))
        <div class="mb-6 overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-6 text-white shadow-lg">
            <p class="text-2xl font-bold">🎉 Selamat! Website Anda sudah live.</p>
            <p class="mt-1 text-emerald-50">Bagikan link berikut ke pelanggan dan partner Anda.</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ $company->publicUrl() }}" target="_blank" class="btn bg-white text-emerald-700 hover:bg-emerald-50">{{ $company->primaryHost() }} <x-icon name="external" class="size-4" /></a>
                <a href="{{ route('websites.domains.index', $company) }}" class="btn border border-white/40 text-white hover:bg-white/10">Hubungkan custom domain</a>
            </div>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <div class="card overflow-hidden">
                <div class="relative">
                    <x-template-thumb :src="route('websites.preview.frame', $company)" aspect="aspect-[16/8]" />
                    <a href="{{ route('websites.preview', $company) }}" class="absolute inset-0 flex items-center justify-center bg-slate-900/0 opacity-0 transition hover:bg-slate-900/40 hover:opacity-100"><span class="btn btn-secondary"><x-icon name="eye" class="size-4" /> Buka preview</span></a>
                </div>
                <div class="grid grid-cols-2 divide-x divide-slate-100 border-t border-slate-100 sm:grid-cols-4">
                    @foreach ([['Template', $company->template?->name ?? '—'], ['Subdomain', $company->subdomainHost()], ['Dibuat', $company->created_at->format('d M Y')], ['Dipublikasikan', $company->published_at?->format('d M Y') ?? '—']] as [$label, $value])
                        <div class="px-5 py-4"><p class="text-xs text-slate-500">{{ $label }}</p><p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $value }}</p></div>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h2 class="text-base font-semibold text-slate-900">Analytics — 14 hari</h2>
                    <div class="flex gap-6 text-right">
                        <div><p class="text-xs text-slate-500">Views</p><p class="font-display text-lg font-bold">{{ number_format($analytics['total_views']) }}</p></div>
                        <div><p class="text-xs text-slate-500">Visitors</p><p class="font-display text-lg font-bold">{{ number_format($analytics['total_visitors']) }}</p></div>
                    </div>
                </div>
                <div class="card-body">
                    @php($max = max(1, $analytics['series']->max('views')))
                    <div class="flex h-40 items-end gap-1.5">
                        @foreach ($analytics['series'] as $day)
                            <div class="group relative flex flex-1 flex-col items-center justify-end">
                                <div class="w-full rounded-t-md bg-brand-500/80 transition group-hover:bg-brand-600" style="height: {{ max(2, $day['views'] / $max * 100) }}%"></div>
                                <span class="pointer-events-none absolute -top-8 hidden rounded bg-slate-900 px-2 py-1 text-[10px] whitespace-nowrap text-white group-hover:block">{{ \Illuminate\Support\Carbon::parse($day['date'])->format('d M') }}: {{ $day['views'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-2 flex justify-between text-[11px] text-slate-400"><span>{{ \Illuminate\Support\Carbon::parse($analytics['series']->first()['date'])->format('d M') }}</span><span>Hari ini</span></div>
                    @if ($analytics['top_pages']->isNotEmpty())
                        <div class="mt-6 border-t border-slate-100 pt-4">
                            <p class="text-xs font-semibold tracking-wide text-slate-400 uppercase">Halaman teratas</p>
                            <ul class="mt-2 space-y-1.5 text-sm">
                                @foreach ($analytics['top_pages'] as $row)
                                    <li class="flex justify-between"><span class="font-mono text-slate-600">{{ $row->path }}</span><span class="font-semibold text-slate-900">{{ $row->views }}</span></li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach ([
                    ['services', 'Services', 'briefcase'], ['products', 'Products', 'cube'], ['projects', 'Projects', 'folder'], ['team', 'Team', 'users'],
                    ['testimonials', 'Testimonials', 'chat'], ['gallery', 'Gallery', 'photo'],
                ] as [$type, $label, $icon])
                    <a href="{{ route('websites.content.index', [$company, $type]) }}" class="card card-body transition hover:ring-2 hover:ring-brand-200">
                        <x-icon :name="$icon" class="size-5 text-brand-600" />
                        <p class="mt-3 font-display text-2xl font-bold text-slate-900">{{ $company->{$type.'_count'} }}</p>
                        <p class="text-xs text-slate-500">{{ $label }}</p>
                    </a>
                @endforeach
                <a href="{{ route('websites.pages.index', $company) }}" class="card card-body transition hover:ring-2 hover:ring-brand-200">
                    <x-icon name="document" class="size-5 text-brand-600" />
                    <p class="mt-3 font-display text-2xl font-bold text-slate-900">{{ $company->pages_count }}</p>
                    <p class="text-xs text-slate-500">Pages</p>
                </a>
                <a href="{{ route('websites.messages.index', $company) }}" class="card card-body transition hover:ring-2 hover:ring-brand-200">
                    <x-icon name="mail" class="size-5 text-brand-600" />
                    <p class="mt-3 font-display text-2xl font-bold text-slate-900">{{ $company->contact_messages_count }}</p>
                    <p class="text-xs text-slate-500">Messages @if ($unreadMessages)<span class="badge badge-red ml-1">{{ $unreadMessages }} baru</span>@endif</p>
                </a>
            </div>
        </div>

        <div class="space-y-6">
            @php($done = collect($checklist)->where('done', true)->count())
            <div class="card">
                <div class="border-b border-slate-100 px-6 py-4">
                    <div class="flex items-center justify-between"><h2 class="text-base font-semibold text-slate-900">Checklist website</h2><span class="text-sm font-semibold text-brand-600">{{ $done }}/{{ count($checklist) }}</span></div>
                    <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-brand-500 to-violet-500" style="width: {{ $done / count($checklist) * 100 }}%"></div></div>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($checklist as $item)
                        <li>
                            <a href="{{ $item['url'] }}" class="flex items-center gap-3 px-6 py-3 text-sm hover:bg-slate-50">
                                @if ($item['done'])
                                    <span class="inline-flex size-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-600"><x-icon name="check" class="size-3.5" /></span>
                                    <span class="flex-1 text-slate-500 line-through">{{ $item['label'] }}</span>
                                @else
                                    <span class="size-6 rounded-full border-2 border-slate-200"></span>
                                    <span class="flex-1 font-medium text-slate-800">{{ $item['label'] }}</span>
                                    <x-icon name="chevron-right" class="size-4 text-slate-400" />
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card card-body">
                <h2 class="text-base font-semibold text-slate-900">Domain</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    <li class="flex items-center justify-between gap-2"><span class="truncate font-medium">{{ $company->subdomainHost() }}</span><span class="badge badge-green">Subdomain</span></li>
                    @foreach ($company->domains as $domain)
                        <li class="flex items-center justify-between gap-2"><span class="truncate font-medium">{{ $domain->domain }}</span><x-status-badge :status="$domain->status" /></li>
                    @endforeach
                </ul>
                <a href="{{ route('websites.domains.index', $company) }}" class="btn btn-secondary btn-sm mt-4 w-full">Kelola domain</a>
            </div>

            <div class="card card-body border-rose-200" x-data>
                <h2 class="text-base font-semibold text-rose-700">Danger zone</h2>
                <p class="mt-1 text-sm text-slate-500">Menghapus website akan menghapus seluruh konten, halaman, menu dan domain.</p>
                <button class="btn btn-secondary btn-sm mt-4 text-rose-600" @click="$dispatch('open-modal', 'delete-website')">Hapus website</button>
            </div>
        </div>
    </div>

    <x-modal name="delete-website" title="Hapus website">
        <form method="POST" action="{{ route('websites.destroy', $company) }}" class="space-y-4">
            @csrf
            @method('DELETE')
            <p class="text-sm text-slate-600">Tindakan ini tidak dapat dibatalkan. Ketik <strong>{{ $company->name }}</strong> untuk konfirmasi.</p>
            <x-form.input name="confirm_name" required />
            <div class="flex justify-end gap-2"><button type="button" class="btn btn-secondary" @click="open = false">Batal</button><button class="btn btn-danger">Hapus permanen</button></div>
        </form>
    </x-modal>
</x-website-layout>
