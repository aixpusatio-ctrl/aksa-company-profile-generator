<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('q')->trim()->toString();
        $collection = $request->string('collection')->toString();

        return view('admin.media.index', [
            'items' => Media::query()
                ->with('user')
                ->when($search, fn ($q) => $q->where('filename', 'like', "%{$search}%"))
                ->when(in_array($collection, config('platform.media.collections'), true), fn ($q) => $q->where('collection', $collection))
                ->latest()
                ->paginate(30)
                ->withQueryString(),
            'search' => $search,
            'collection' => $collection,
            'collections' => config('platform.media.collections'),
            'totalSize' => Media::query()->sum('size'),
        ]);
    }

    public function destroy(Media $media, MediaService $service): RedirectResponse
    {
        $service->delete($media);

        return back()->with('success', 'File dihapus.');
    }
}
