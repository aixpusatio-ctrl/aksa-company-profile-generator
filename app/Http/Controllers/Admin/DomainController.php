<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Services\DomainService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DomainController extends Controller
{
    public function __construct(private readonly DomainService $domains) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $search = $request->string('q')->trim()->toString();

        $domains = Domain::query()
            ->with('companyProfile.user')
            ->when(in_array($status, Domain::STATUSES, true), fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where('domain', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.domains.index', compact('domains', 'status', 'search'));
    }

    /** Re-run DNS verification. */
    public function verify(Domain $domain): RedirectResponse
    {
        $this->domains->requestVerification($domain);

        return back()->with('success', "Verifikasi {$domain->domain} dijalankan.");
    }

    /** Manually activate a domain (support / override). */
    public function activate(Domain $domain): RedirectResponse
    {
        $this->domains->activate($domain);

        return back()->with('success', "{$domain->domain} diaktifkan secara manual.");
    }

    public function destroy(Domain $domain): RedirectResponse
    {
        $this->domains->delete($domain);

        return back()->with('success', 'Domain dihapus.');
    }
}
