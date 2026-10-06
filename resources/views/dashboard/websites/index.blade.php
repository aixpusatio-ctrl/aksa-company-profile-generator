<x-layouts.app title="My Websites">
    <x-page-header title="My Websites" description="Semua company profile milik Anda.">
        <x-slot:actions>
            <a href="{{ route('websites.create') }}" class="btn btn-primary"><x-icon name="plus" class="size-4" /> Create New Company Profile</a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="mb-6 flex flex-wrap items-center gap-2">
        <input type="search" name="q" value="{{ $search }}" placeholder="Cari nama atau subdomain..." class="form-input max-w-xs">
        @foreach (['' => 'Semua', 'published' => 'Published', 'draft' => 'Draft'] as $value => $label)
            <button name="status" value="{{ $value }}" class="rounded-full px-4 py-2 text-sm font-semibold {{ $status === $value ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">{{ $label }}</button>
        @endforeach
    </form>

    @if ($websites->isEmpty())
        <x-empty-state :title="$search ? 'Tidak ada hasil untuk “'.$search.'”' : 'Belum ada company profile'" icon="globe">
            <a href="{{ route('websites.create') }}" class="btn btn-primary">Buat website</a>
        </x-empty-state>
    @else
        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($websites as $website)
                @include('dashboard.websites.partials.card')
            @endforeach
        </div>
        <div class="mt-8">{{ $websites->links() }}</div>
    @endif
</x-layouts.app>
