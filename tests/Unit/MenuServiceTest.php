<?php

namespace Tests\Unit;

use App\Models\Menu;
use App\Services\MenuService;
use App\Support\Website\MenuLink;
use Illuminate\Support\Collection;
use Tests\TestCase;

class MenuServiceTest extends TestCase
{
    private function menu(int $id, ?int $parent, int $sort, string $status = 'active'): Menu
    {
        $menu = new Menu(['title' => 'M'.$id, 'parent_id' => $parent, 'sort_order' => $sort, 'status' => $status, 'type' => 'anchor']);
        $menu->id = $id;

        return $menu;
    }

    public function test_tree_nests_children_and_sorts(): void
    {
        $menus = new Collection([
            $this->menu(1, null, 2),
            $this->menu(2, null, 1),
            $this->menu(3, 1, 2),
            $this->menu(4, 1, 1),
            $this->menu(5, 2, 1),
            $this->menu(6, null, 2), // same sort as #1 → ordered by id
        ]);

        $tree = app(MenuService::class)->tree($menus);

        $this->assertSame([2, 1, 6], $tree->pluck('id')->all());
        $this->assertSame([4, 3], $tree[1]->children->pluck('id')->all());
        $this->assertSame([5], $tree[0]->children->pluck('id')->all());
        $this->assertTrue($tree[2]->children->isEmpty());
    }

    public function test_tree_can_skip_inactive_menus(): void
    {
        $menus = new Collection([
            $this->menu(1, null, 1),
            $this->menu(2, null, 2, 'inactive'),
            $this->menu(3, 1, 1, 'inactive'),
            $this->menu(4, 1, 2),
            $this->menu(5, 2, 1), // child of an inactive parent
        ]);

        $all = app(MenuService::class)->tree($menus);
        $active = app(MenuService::class)->tree($menus, activeOnly: true);

        $this->assertSame([1, 2], $all->pluck('id')->all());
        $this->assertSame([1], $active->pluck('id')->all());
        $this->assertSame([4], $active[0]->children->pluck('id')->all());
    }

    public function test_tree_of_empty_collection(): void
    {
        $this->assertTrue(app(MenuService::class)->tree(new Collection)->isEmpty());
    }

    public function test_menu_link_attributes_are_escaped(): void
    {
        $link = new MenuLink('X', 'https://example.test/?a=1&b="2"', true);

        $this->assertSame('href="https://example.test/?a=1&amp;b=&quot;2&quot;" target="_blank" rel="noopener noreferrer"', $link->attributes());
        $this->assertSame('href="#"', (new MenuLink('Group', null))->attributes());
        $this->assertFalse((new MenuLink('Leaf', '#a'))->hasChildren());
    }
}
