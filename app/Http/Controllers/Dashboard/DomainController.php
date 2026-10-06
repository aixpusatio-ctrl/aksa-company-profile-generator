<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\Domain;
use App\Services\CompanyProfileService;
use App\Services\DomainService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Settings → Domain: platform sub domain + custom domains.
 */
class DomainController extends Controller
{
    public function __construct(private readonly DomainService $domains) {}

    public function overview(Request $request): View
    {
        $companies = $request->user()->companyProfiles()->with('domains')->orderBy('name')->get();

        return view('dashboard.domains.overview', compact('companies'));
    }

    public function index(CompanyProfile $company): View
    {
        $this->authorize('update', $company);
        $company->load('domains');

        return view('dashboard.domains.index', [
            'company' => $company,
            'instructions' => $company->domains->mapWithKeys(fn (Domain $domain) => [$domain->id => $this->domains->dnsInstructions($domain)]),
            'canUseCustomDomain' => request()->user()->canUseCustomDomain(),
        ]);
    }

    public function updateSubdomain(Request $request, CompanyProfile $company, CompanyProfileService $companies): RedirectResponse
    {
        $this->authorize('update', $company);

        $data = $request->validate(['slug' => ['required', 'string', 'max:63']]);
        $companies->update($company, ['slug' => strtolower($data['slug'])]);

        return back()->with('success', 'Subdomain diperbarui: '.$company->subdomainHost());
    }

    public function store(Request $request, CompanyProfile $company): RedirectResponse
    {
        $this->authorize('manageDomains', $company);

        $data = $request->validate(['domain' => ['required', 'string', 'max:253']]);
        $this->domains->add($company, $data['domain']);

        return back()->with('success', 'Domain ditambahkan. Tambahkan DNS record lalu klik "Verify".');
    }

    public function verify(CompanyProfile $company, Domain $domain): RedirectResponse
    {
        $this->authorize('update', $domain);
        $this->domains->requestVerification($domain);

        $domain->refresh();

        return back()->with(
            $domain->status === Domain::STATUS_FAILED ? 'error' : 'success',
            match ($domain->status) {
                Domain::STATUS_ACTIVE => "Domain {$domain->domain} aktif!",
                Domain::STATUS_FAILED => "Verifikasi gagal: {$domain->failure_reason}",
                default => 'Verifikasi sedang diproses. Status akan diperbarui otomatis.',
            }
        );
    }

    public function primary(CompanyProfile $company, Domain $domain): RedirectResponse
    {
        $this->authorize('update', $domain);
        $this->domains->makePrimary($domain);

        return back()->with('success', "{$domain->domain} dijadikan domain utama.");
    }

    public function destroy(CompanyProfile $company, Domain $domain): RedirectResponse
    {
        $this->authorize('delete', $domain);
        $this->domains->delete($domain);

        return back()->with('success', 'Domain dihapus.');
    }
}
