<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Services\MediaService;
use App\Support\ContentTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Generic CRUD + reorder for repeatable content blocks
 * (services, products, projects, team, testimonials, gallery).
 */
class ContentController extends Controller
{
    public function __construct(private readonly MediaService $media) {}

    public function index(CompanyProfile $company, string $type): View
    {
        $this->authorize('update', $company);
        $definition = ContentTypes::get($type);

        return view('dashboard.websites.content', [
            'company' => $company->load('template'),
            'type' => $definition,
            'items' => $company->{$definition['relation']}()->get(),
        ]);
    }

    public function store(Request $request, CompanyProfile $company, string $type): RedirectResponse
    {
        $this->authorize('update', $company);
        $definition = ContentTypes::get($type);

        $data = $this->validated($request, $type, null);
        $company->{$definition['relation']}()->create($data);

        return back()->with('success', $definition['singular'].' ditambahkan.');
    }

    public function update(Request $request, CompanyProfile $company, string $type, int $id): RedirectResponse
    {
        $this->authorize('update', $company);
        $definition = ContentTypes::get($type);

        $item = $this->find($company, $definition, $id);
        $item->update($this->validated($request, $type, $item));

        return back()->with('success', $definition['singular'].' diperbarui.');
    }

    public function destroy(CompanyProfile $company, string $type, int $id): RedirectResponse
    {
        $this->authorize('update', $company);
        $definition = ContentTypes::get($type);

        $this->find($company, $definition, $id)->delete();

        return back()->with('success', $definition['singular'].' dihapus.');
    }

    public function reorder(Request $request, CompanyProfile $company, string $type): JsonResponse
    {
        $this->authorize('update', $company);
        $definition = ContentTypes::get($type);

        $ids = $request->validate(['ids' => ['required', 'array'], 'ids.*' => ['integer']])['ids'];
        $owned = $company->{$definition['relation']}()->pluck('id')->all();

        DB::transaction(function () use ($ids, $owned, $definition) {
            foreach (array_values(array_intersect($ids, $owned)) as $i => $id) {
                $definition['model']::query()->whereKey($id)->update(['sort_order' => $i + 1]);
            }
        });

        return response()->json(['saved' => true]);
    }

    private function find(CompanyProfile $company, array $definition, int $id): Model
    {
        // Scoped to the company: users can never touch another company's content.
        return $company->{$definition['relation']}()->whereKey($id)->firstOrFail();
    }

    private function validated(Request $request, string $type, ?Model $item): array
    {
        $imageField = ContentTypes::imageField($type);
        $rules = ContentTypes::rules($type);

        if ($imageField) {
            $requireImage = ($definition = ContentTypes::get($type))['image_required'] ?? false;
            $rules[$imageField] = [($requireImage && ! $item && ! $request->filled($imageField.'_media')) ? 'required' : 'nullable', MediaService::imageRule()];
            $rules[$imageField.'_media'] = ['nullable', 'string', 'max:255'];
            $rules[$imageField.'_remove'] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules);
        $data = collect($validated)->only(array_keys(ContentTypes::get($type)['fields']))->all();

        if ($imageField) {
            $company = $request->route('company');
            $collection = $type === 'gallery' ? 'gallery' : 'images';
            $data[$imageField] = $this->media->resolveImageInput($validated, $imageField, $request->user(), $company, $item?->{$imageField}, $collection);

            if ($type === 'gallery' && blank($data[$imageField])) {
                $data[$imageField] = $item?->{$imageField};
            }
        }

        return $data;
    }
}
