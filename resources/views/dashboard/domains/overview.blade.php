<x-layouts.app title="Domains">
    <x-page-header title="Domains" description="Subdomain dan custom domain dari semua website Anda." />
    @if ($companies->isEmpty())
        <x-empty-state title="Belum ada website" icon="globe"><a href="{{ route('websites.create') }}" class="btn btn-primary">Buat website</a></x-empty-state>
    @else
        <div class="card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table">
                    <thead><tr><th>Website</th><th>Domain</th><th>Tipe</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($companies as $company)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $company->name }}</td>
                                <td class="font-mono text-xs">{{ $company->subdomainHost() }}</td>
                                <td><span class="badge badge-slate">Subdomain</span></td>
                                <td><x-status-badge :status="$company->isPublished() ? 'active' : 'draft'" /></td>
                                <td class="text-right"><a href="{{ route('websites.domains.index', $company) }}" class="btn btn-secondary btn-sm">Kelola</a></td>
                            </tr>
                            @foreach ($company->domains as $domain)
                                <tr>
                                    <td class="text-xs text-slate-400">↳ {{ $company->name }}</td>
                                    <td class="font-mono text-xs">{{ $domain->domain }} @if ($domain->is_primary)<span class="badge badge-brand ml-1">Primary</span>@endif</td>
                                    <td><span class="badge badge-violet">Custom · {{ $domain->type === 'apex' ? 'Root' : 'Subdomain' }}</span></td>
                                    <td><x-status-badge :status="$domain->status" /></td>
                                    <td class="text-right"><a href="{{ route('websites.domains.index', $company) }}" class="btn btn-ghost btn-sm">DNS</a></td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-layouts.app>
