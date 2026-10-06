<?php

namespace App\Services;

use App\Models\CompanyProfile;
use App\Models\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class MenuService
{
    public const MAX_DEPTH = 2;

    /**
     * Default navigation created for every new company profile.
     */
    public function createDefaults(CompanyProfile $company): void
    {
        $defaults = [
            ['Home', 'hero'], ['About', 'about'], ['Services', 'services'], ['Products', 'products'],
            ['Projects', 'projects'], ['Team', 'team'], ['Contact', 'contact'],
        ];

        foreach ($defaults as $i => [$title, $anchor]) {
            $company->menus()->create([
                'title' => $title,
                'slug' => Str::slug($title),
                'type' => Menu::TYPE_ANCHOR,
                'url' => $anchor,
                'sort_order' => $i + 1,
                'status' => 'active',
            ]);
        }
    }

    public function create(CompanyProfile $company, array $data): Menu
    {
        $data = $this->normalize($company, $data);
        $data['sort_order'] = (int) $company->menus()->where('parent_id', $data['parent_id'] ?? null)->max('sort_order') + 1;

        return $company->menus()->create($data);
    }

    public function update(Menu $menu, array $data): Menu
    {
        $data = $this->normalize($menu->companyProfile, $data, $menu);
        $menu->update($data);

        return $menu;
    }

    private function normalize(CompanyProfile $company, array $data, ?Menu $menu = null): array
    {
        $data['slug'] = Str::slug($data['title']);
        $data['open_in_new_tab'] = (bool) ($data['open_in_new_tab'] ?? false);
        $data['status'] = ($data['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

        if (! empty($data['parent_id'])) {
            $parent = $company->menus()->whereKey($data['parent_id'])->first();

            if (! $parent || $parent->parent_id !== null || ($menu && $parent->is($menu))) {
                throw ValidationException::withMessages(['parent_id' => 'Parent menu tidak valid (maksimal 2 level).']);
            }

            if ($menu && $menu->children()->exists()) {
                throw ValidationException::withMessages(['parent_id' => 'Menu yang memiliki submenu tidak dapat dijadikan submenu.']);
            }
        } else {
            $data['parent_id'] = null;
        }

        switch ($data['type']) {
            case Menu::TYPE_PAGE:
                $page = $company->pages()->whereKey($data['company_page_id'] ?? 0)->first();
                if (! $page) {
                    throw ValidationException::withMessages(['company_page_id' => 'Pilih halaman yang valid.']);
                }
                $data['url'] = null;
                break;
            case Menu::TYPE_ANCHOR:
                $data['company_page_id'] = null;
                $data['url'] = Str::slug($data['url'] ?? '') ?: 'hero';
                break;
            case Menu::TYPE_URL:
                $data['company_page_id'] = null;
                break;
            default:
                $data['company_page_id'] = null;
                $data['url'] = null;
        }

        return $data;
    }

    /**
     * Persist a drag & drop tree: [{id, children: [{id}, ...]}, ...]
     */
    public function saveTree(CompanyProfile $company, array $tree): void
    {
        $ids = $company->menus()->pluck('id')->all();

        DB::transaction(function () use ($tree, $ids, $company) {
            foreach (array_values($tree) as $i => $node) {
                $id = (int) ($node['id'] ?? 0);
                if (! in_array($id, $ids, true)) {
                    continue;
                }

                Menu::query()->whereKey($id)->update(['parent_id' => null, 'sort_order' => $i + 1]);

                foreach (array_values($node['children'] ?? []) as $j => $child) {
                    $childId = (int) ($child['id'] ?? 0);
                    if (! in_array($childId, $ids, true) || $childId === $id) {
                        continue;
                    }

                    // Only two levels are supported: promote grandchildren.
                    Menu::query()->where('parent_id', $childId)->update(['parent_id' => $id]);
                    Menu::query()->whereKey($childId)->update(['parent_id' => $id, 'sort_order' => $j + 1]);
                }
            }

            $company->touch();
        });
    }

    /**
     * Nested tree of menus from a flat collection (parent_id based).
     */
    public function tree(Collection $menus, bool $activeOnly = false): Collection
    {
        $menus = $activeOnly ? $menus->where('status', 'active') : $menus;
        $grouped = $menus->sortBy([['sort_order', 'asc'], ['id', 'asc']])->groupBy(fn ($m) => $m->parent_id ?? 0);

        return ($grouped[0] ?? collect())->map(function (Menu $menu) use ($grouped) {
            $menu->setRelation('children', ($grouped[$menu->id] ?? collect())->values());

            return $menu;
        })->values();
    }
}
