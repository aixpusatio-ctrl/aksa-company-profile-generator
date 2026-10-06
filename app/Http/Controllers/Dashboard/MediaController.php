<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Media library: upload, delete, search and select (picker JSON).
 */
class MediaController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(Request $request): View|JsonResponse
    {
        $collection = $request->string('collection')->toString();
        $search = $request->string('q')->trim()->toString();

        $items = $request->user()->media()
            ->when(in_array($collection, config('platform.media.collections'), true), fn ($q) => $q->where('collection', $collection))
            ->when($request->boolean('images'), fn ($q) => $q->where('mime_type', 'like', 'image/%'))
            ->when($search, fn ($q) => $q->where('filename', 'like', "%{$search}%"))
            ->latest()
            ->paginate($request->wantsJson() ? 24 : 30)
            ->withQueryString();

        if ($request->wantsJson()) {
            return response()->json($items);
        }

        return view('dashboard.media.index', [
            'items' => $items,
            'collection' => $collection,
            'search' => $search,
            'collections' => config('platform.media.collections'),
            'usage' => $request->user()->media()->sum('size'),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'collection' => ['required', Rule::in(config('platform.media.collections'))],
            'files' => ['required', 'array', 'max:10'],
            'files.*' => ['required', $request->input('collection') === 'documents' ? MediaService::documentRule() : MediaService::imageRule()],
        ]);

        $uploaded = collect($request->file('files'))->map(
            fn ($file) => $this->media->upload($file, $request->user(), null, $request->input('collection'))
        );

        if ($request->wantsJson()) {
            return response()->json(['data' => $uploaded->values()]);
        }

        return back()->with('success', $uploaded->count().' file berhasil diunggah.');
    }

    public function destroy(Media $media): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $media);
        $this->media->delete($media);

        return request()->wantsJson()
            ? response()->json(['deleted' => true])
            : back()->with('success', 'File dihapus.');
    }
}
