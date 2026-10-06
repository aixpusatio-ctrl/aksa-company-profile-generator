<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Contact form submissions of a website.
 */
class MessageController extends Controller
{
    public function index(CompanyProfile $company): View
    {
        $this->authorize('view', $company);

        return view('dashboard.messages.index', [
            'company' => $company,
            'messages' => $company->contactMessages()->paginate(15),
        ]);
    }

    public function update(CompanyProfile $company, ContactMessage $message): RedirectResponse
    {
        $this->authorize('update', $company);
        $message->update(['read_at' => $message->read_at ? null : now()]);

        return back();
    }

    public function destroy(CompanyProfile $company, ContactMessage $message): RedirectResponse
    {
        $this->authorize('update', $company);
        $message->delete();

        return back()->with('success', 'Pesan dihapus.');
    }
}
