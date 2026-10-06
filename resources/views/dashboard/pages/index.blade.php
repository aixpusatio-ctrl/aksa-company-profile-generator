<x-website-layout :company="$company" title="Pages">
    <div class="card">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Custom Pages</h2>
                <p class="text-xs text-slate-500">Halaman tambahan seperti Karir, Berita, Kebijakan Privasi. Tambahkan ke menu melalui Menu Builder.</p>
            </div>
            <a href="{{ route('websites.pages.create', $company) }}" class="btn btn-primary btn-sm"><x-icon name="plus" class="size-4" /> Halaman baru</a>
        </div>
        @if ($pages->isEmpty())
            <div class="p-6"><x-empty-state title="Belum ada halaman" description="Contoh: About Us, Career, Sustainability, News, Privacy Policy, Terms & Conditions." icon="document">
                <a href="{{ route('websites.pages.create', $company) }}" class="btn btn-primary">Buat halaman</a>
            </x-empty-state></div>
        @else
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Judul</th><th>URL</th><th>Status</th><th>Diperbarui</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr>
                                <td class="font-semibold text-slate-900"><a href="{{ route('websites.pages.edit', [$company, $page]) }}" class="hover:text-brand-600">{{ $page->title }}</a></td>
                                <td class="font-mono text-xs text-slate-500">/{{ $page->slug }}</td>
                                <td><x-status-badge :status="$page->status" /></td>
                                <td class="text-xs text-slate-500">{{ $page->updated_at->diffForHumans() }}</td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('websites.preview.frame', [$company, 'page' => $page->slug]) }}" target="_blank" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Preview"><x-icon name="eye" class="size-4" /></a>
                                        <a href="{{ route('websites.pages.edit', [$company, $page]) }}" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Edit"><x-icon name="pencil" class="size-4" /></a>
                                        <x-confirm-delete :action="route('websites.pages.destroy', [$company, $page])" message="Hapus halaman ini? Menu yang mengarah ke halaman ini akan disembunyikan." />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-website-layout>
