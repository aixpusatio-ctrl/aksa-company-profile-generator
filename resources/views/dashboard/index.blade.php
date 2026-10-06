<x-layouts.app title="Dashboard">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ now()->translatedFormat('l, d F Y') }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">Welcome, {{ auth()->user()->name }} 👋</h1>
        </div>
        <a href="{{ route('websites.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Create New Company Profile</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Websites" :value="$stats['total']" icon="globe" />
        <x-stat-card label="Published Websites" :value="$stats['published']" icon="rocket" color="green" />
        <x-stat-card label="Draft Websites" :value="$stats['draft']" icon="pencil" color="amber" />
        <x-stat-card label="Templates Used" :value="$stats['templates']" icon="template" color="violet" />
    </div>

    <div class="mt-8 grid gap-8 xl:grid-cols-[1fr_340px]">
        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-bold">My Company Profiles</h2>
                <a href="{{ route('websites.index') }}" class="text-sm font-semibold text-brand-600 hover:underline">Lihat semua</a>
            </div>
            @if ($websites->isEmpty())
                <x-empty-state title="Belum ada company profile" description="Buat website pertama Anda — hanya butuh beberapa menit." icon="rocket">
                    <a href="{{ route('websites.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Create New Company Profile</a>
                </x-empty-state>
            @else
                <div class="grid gap-5 sm:grid-cols-2">
                    @foreach ($websites->take(4) as $website)
                        @include('dashboard.websites.partials.card')
                    @endforeach
                    <a href="{{ route('websites.create') }}" class="flex min-h-64 flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-200 text-slate-500 transition hover:border-brand-300 hover:bg-brand-50/50 hover:text-brand-700">
                        <span class="inline-flex size-12 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-200"><x-icon name="plus" class="size-6" /></span>
                        <span class="mt-3 text-sm font-semibold">Create New Company Profile</span>
                    </a>
                </div>
            @endif
        </section>

        <aside class="space-y-6">
            <div class="card card-body">
                <p class="text-sm font-medium text-slate-500">Kunjungan 30 hari</p>
                <p class="mt-1 font-display text-3xl font-bold text-slate-900">{{ number_format($stats['views']) }}</p>
                <p class="mt-1 text-xs text-slate-500">Total dari semua website yang dipublikasikan.</p>
            </div>
            <div class="card">
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <h3 class="text-sm font-semibold text-slate-900">Pesan terbaru</h3>
                    @if ($stats['messages'])<span class="badge badge-red">{{ $stats['messages'] }} baru</span>@endif
                </div>
                <ul class="divide-y divide-slate-100">
                    @forelse ($recentMessages as $message)
                        <li>
                            <a href="{{ route('websites.messages.index', $message->company_profile_id) }}" class="block px-5 py-3 hover:bg-slate-50">
                                <p class="flex items-center gap-2 text-sm font-medium text-slate-900">@unless ($message->read_at)<span class="size-2 rounded-full bg-brand-500"></span>@endunless {{ $message->name }}</p>
                                <p class="truncate text-xs text-slate-500">{{ $message->message }}</p>
                                <p class="mt-0.5 text-[11px] text-slate-400">{{ $message->companyProfile?->name }} · {{ $message->created_at->diffForHumans() }}</p>
                            </a>
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center text-sm text-slate-500">Belum ada pesan masuk.</li>
                    @endforelse
                </ul>
            </div>
            <div class="rounded-2xl bg-slate-900 p-5 text-slate-300">
                <p class="text-sm font-semibold text-white">💡 Tips</p>
                <p class="mt-2 text-sm">Website dengan logo, minimal 3 layanan dan portofolio mendapat kepercayaan pengunjung jauh lebih tinggi.</p>
                <a href="{{ route('dashboard.templates') }}" class="mt-4 inline-flex text-sm font-semibold text-white underline underline-offset-4">Jelajahi template</a>
            </div>
        </aside>
    </div>
</x-layouts.app>
