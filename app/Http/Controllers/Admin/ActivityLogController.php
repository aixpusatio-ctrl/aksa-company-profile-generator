<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $action = $request->string('action')->toString();
        $search = $request->string('q')->trim()->toString();

        return view('admin.logs.index', [
            'logs' => ActivityLog::query()
                ->with('user')
                ->when($action, fn ($q) => $q->where('action', 'like', $action.'%'))
                ->when($search, fn ($q) => $q->where('description', 'like', "%{$search}%"))
                ->latest('created_at')
                ->paginate(30)
                ->withQueryString(),
            'actions' => ['auth', 'website', 'domain', 'template', 'admin'],
            'action' => $action,
            'search' => $search,
        ]);
    }
}
