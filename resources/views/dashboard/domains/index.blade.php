<x-website-layout :company="$company" title="Domain">
    <div class="space-y-6">
        <div class="card">
            <div class="border-b border-slate-100 px-6 py-4">
                <h2 class="text-base font-semibold text-slate-900">Subdomain platform</h2>
                <p class="text-xs text-slate-500">Gratis dan langsung aktif. Website tetap dapat diakses melalui subdomain meskipun memakai custom domain.</p>
            </div>
            <form method="POST" action="{{ route('websites.domains.subdomain', $company) }}" class="card-body flex flex-col gap-3 sm:flex-row sm:items-start">
                @csrf
                @method('PUT')
                <div class="flex-1">
                    <div class="flex">
                        <input name="slug" value="{{ old('slug', $company->slug) }}" required maxlength="63" class="form-input rounded-r-none font-mono">
                        <span class="inline-flex items-center rounded-r-lg border border-l-0 border-slate-300 bg-slate-50 px-3 font-mono text-sm text-slate-500">.{{ config('platform.domain') }}</span>
                    </div>
                    @error('slug')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <button class="btn btn-secondary">Simpan subdomain</button>
            </form>
        </div>

        <div class="card">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-6 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Custom domain</h2>
                    <p class="text-xs text-slate-500">Gunakan domain Anda sendiri, contoh <span class="font-mono">www.perusahaan.com</span> atau <span class="font-mono">perusahaan.com</span>.</p>
                </div>
            </div>
            <div class="card-body">
                @if ($canUseCustomDomain)
                    <form method="POST" action="{{ route('websites.domains.store', $company) }}" class="flex flex-col gap-3 sm:flex-row sm:items-start">
                        @csrf
                        <div class="flex-1">
                            <input name="domain" value="{{ old('domain') }}" placeholder="www.perusahaan.com" required class="form-input font-mono">
                            @error('domain')<p class="form-error">{{ $message }}</p>@enderror
                        </div>
                        <button class="btn btn-primary"><x-icon name="plus" class="size-4" /> Tambah domain</button>
                    </form>
                    @if (config('platform.custom_domains.verifier') === 'fake')
                        <p class="mt-3 rounded-lg bg-sky-50 px-3 py-2 text-xs text-sky-800"><strong>Mode development:</strong> verifikasi DNS disimulasikan. Domain yang mengandung kata “fail” akan gagal diverifikasi.</p>
                    @endif
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        Custom domain tersedia di paket <strong>Professional</strong> ke atas. <a href="{{ route('settings.edit') }}#plan" class="font-semibold underline">Lihat paket</a>
                    </div>
                @endif
            </div>

            @foreach ($company->domains as $domain)
                <div class="border-t border-slate-100 px-6 py-5" x-data="{ open: {{ $domain->isActive() ? 'false' : 'true' }} }">
                    <div class="flex flex-wrap items-center gap-3">
                        <x-icon name="globe" class="size-5 text-slate-400" />
                        <p class="font-mono text-sm font-semibold text-slate-900">{{ $domain->domain }}</p>
                        <x-status-badge :status="$domain->status" />
                        @if ($domain->is_primary)<span class="badge badge-brand">Primary</span>@endif
                        <div class="ml-auto flex flex-wrap items-center gap-2">
                            @if ($domain->isActive())
                                <a href="{{ $domain->url() }}" target="_blank" class="btn btn-ghost btn-sm">Buka <x-icon name="external" class="size-3.5" /></a>
                                @unless ($domain->is_primary)
                                    <form method="POST" action="{{ route('websites.domains.primary', [$company, $domain]) }}">@csrf<button class="btn btn-secondary btn-sm">Jadikan utama</button></form>
                                @endunless
                            @else
                                <form method="POST" action="{{ route('websites.domains.verify', [$company, $domain]) }}">@csrf<button class="btn btn-primary btn-sm"><x-icon name="refresh" class="size-3.5" /> Verify</button></form>
                            @endif
                            <button type="button" class="btn btn-ghost btn-sm" @click="open = !open">DNS</button>
                            <x-confirm-delete :action="route('websites.domains.destroy', [$company, $domain])" message="Hapus domain ini?" />
                        </div>
                    </div>
                    @if ($domain->failure_reason)
                        <p class="mt-2 text-xs text-rose-600">{{ $domain->failure_reason }}</p>
                    @endif
                    @if ($domain->verified_at)
                        <p class="mt-1 text-xs text-slate-500">Terverifikasi {{ $domain->verified_at->diffForHumans() }}</p>
                    @endif
                    <div x-show="open" x-collapse class="mt-4">
                        <p class="mb-2 text-sm text-slate-600">Tambahkan record berikut di DNS provider domain Anda (Cloudflare, Niagahoster, GoDaddy, dll.), lalu klik <strong>Verify</strong>:</p>
                        <div class="overflow-x-auto rounded-xl border border-slate-200">
                            <table class="table">
                                <thead><tr><th>Type</th><th>Name</th><th>Value</th><th>TTL</th></tr></thead>
                                <tbody>
                                    @foreach ($instructions[$domain->id] as $record)
                                        <tr>
                                            <td><span class="badge badge-slate font-mono">{{ $record['type'] }}</span></td>
                                            <td class="font-mono text-xs">{{ $record['name'] }}</td>
                                            <td class="font-mono text-xs" x-data>
                                                <span class="break-all">{{ $record['value'] }}</span>
                                                <button type="button" class="ml-1 text-brand-600 hover:underline" @click="navigator.clipboard.writeText(@js($record['value']))">copy</button>
                                            </td>
                                            <td class="text-xs">{{ $record['ttl'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Status: Pending → Verifying → Active / Failed. Propagasi DNS bisa memakan waktu hingga 24 jam.</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-website-layout>
