<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuBuilderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CompanyProfile $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->userWithPlan();
        $this->company = $this->companyFor($this->user);
        $this->company->menus()->delete();
    }

    private function url(string $suffix = ''): string
    {
        return "/dashboard/websites/{$this->company->id}/menus{$suffix}";
    }

    private function addMenu(array $data)
    {
        return $this->actingAs($this->user)->post($this->url(), $data);
    }

    private function lastMenu(): Menu
    {
        return Menu::query()->where('company_profile_id', $this->company->id)->latest('id')->firstOrFail();
    }

    public function test_anchor_menu_item_can_be_created(): void
    {
        $this->addMenu(['title' => 'Layanan Kami', 'type' => 'anchor', 'url' => 'services'])
            ->assertRedirect()->assertSessionHasNoErrors();

        $menu = $this->lastMenu();
        $this->assertSame('Layanan Kami', $menu->title);
        $this->assertSame('layanan-kami', $menu->slug);
        $this->assertSame(Menu::TYPE_ANCHOR, $menu->type);
        $this->assertSame('services', $menu->url);
        $this->assertNull($menu->parent_id);
        $this->assertSame('active', $menu->status);
        $this->assertFalse($menu->open_in_new_tab);
    }

    public function test_anchor_must_target_a_known_section(): void
    {
        $this->addMenu(['title' => 'X', 'type' => 'anchor', 'url' => 'evil'])->assertSessionHasErrors('url');
        $this->addMenu(['title' => 'X', 'type' => 'anchor'])->assertSessionHasErrors('url');

        $this->assertSame(0, $this->company->menus()->count());
    }

    public function test_page_menu_item_can_be_created(): void
    {
        $page = $this->company->pages()->create(['title' => 'Karir', 'slug' => 'karir', 'status' => 'published']);

        $this->addMenu(['title' => 'Karir', 'type' => 'page', 'company_page_id' => $page->id, 'url' => 'ignored'])
            ->assertSessionHasNoErrors();

        $menu = $this->lastMenu();
        $this->assertSame(Menu::TYPE_PAGE, $menu->type);
        $this->assertSame($page->id, $menu->company_page_id);
        $this->assertNull($menu->url);
    }

    public function test_page_menu_requires_a_page_of_the_same_company(): void
    {
        $foreignPage = $this->companyFor()->pages()->create(['title' => 'Lain', 'slug' => 'lain', 'status' => 'published']);

        $this->addMenu(['title' => 'X', 'type' => 'page'])->assertSessionHasErrors('company_page_id');
        $this->addMenu(['title' => 'X', 'type' => 'page', 'company_page_id' => $foreignPage->id])->assertSessionHasErrors('company_page_id');

        $this->assertSame(0, $this->company->menus()->count());
    }

    public function test_external_url_menu_item_can_be_created(): void
    {
        $this->addMenu(['title' => 'Blog', 'type' => 'url', 'url' => 'https://blog.example.test/news', 'open_in_new_tab' => '1'])
            ->assertSessionHasNoErrors();

        $menu = $this->lastMenu();
        $this->assertSame('https://blog.example.test/news', $menu->url);
        $this->assertTrue($menu->open_in_new_tab);
        $this->assertNull($menu->company_page_id);
    }

    public function test_invalid_external_urls_are_rejected(): void
    {
        foreach (['javascript:alert(1)', 'ftp://files.example.test', 'not a url', 'data:text/html,<script>alert(1)</script>'] as $url) {
            $this->addMenu(['title' => 'Bad', 'type' => 'url', 'url' => $url])->assertSessionHasErrors('url');
        }
        $this->addMenu(['title' => 'Bad', 'type' => 'url'])->assertSessionHasErrors('url');

        $this->assertSame(0, $this->company->menus()->count());
    }

    public function test_unknown_menu_type_is_rejected(): void
    {
        $this->addMenu(['title' => 'X', 'type' => 'script', 'url' => 'x'])->assertSessionHasErrors('type');
    }

    public function test_group_menu_with_submenu(): void
    {
        $this->addMenu(['title' => 'Perusahaan', 'type' => 'group', 'url' => 'https://ignored.test'])->assertSessionHasNoErrors();
        $group = $this->lastMenu();
        $this->assertSame(Menu::TYPE_GROUP, $group->type);
        $this->assertNull($group->url);

        $this->addMenu(['title' => 'Tentang', 'type' => 'anchor', 'url' => 'about', 'parent_id' => $group->id])->assertSessionHasNoErrors();
        $this->addMenu(['title' => 'Tim', 'type' => 'anchor', 'url' => 'team', 'parent_id' => $group->id])->assertSessionHasNoErrors();

        $children = $group->children()->get();
        $this->assertSame(['Tentang', 'Tim'], $children->pluck('title')->all());
        $this->assertSame([1, 2], $children->pluck('sort_order')->map(fn ($v) => (int) $v)->all());
    }

    public function test_menu_depth_is_limited_to_two_levels(): void
    {
        $root = $this->company->menus()->create(['title' => 'Root', 'type' => 'group', 'sort_order' => 1]);
        $child = $this->company->menus()->create(['title' => 'Child', 'type' => 'anchor', 'url' => 'about', 'parent_id' => $root->id]);

        $this->addMenu(['title' => 'Grandchild', 'type' => 'anchor', 'url' => 'team', 'parent_id' => $child->id])
            ->assertSessionHasErrors('parent_id');

        $this->assertSame(0, Menu::query()->where('parent_id', $child->id)->count());
    }

    public function test_parent_from_another_company_is_rejected(): void
    {
        $foreign = $this->companyFor()->menus()->first();

        $this->addMenu(['title' => 'X', 'type' => 'anchor', 'url' => 'about', 'parent_id' => $foreign->id])
            ->assertSessionHasErrors('parent_id');

        $this->assertSame(0, $this->company->menus()->count());
    }

    public function test_menu_with_children_cannot_become_a_submenu(): void
    {
        $a = $this->company->menus()->create(['title' => 'A', 'type' => 'group', 'sort_order' => 1]);
        $this->company->menus()->create(['title' => 'A1', 'type' => 'anchor', 'url' => 'about', 'parent_id' => $a->id]);
        $b = $this->company->menus()->create(['title' => 'B', 'type' => 'group', 'sort_order' => 2]);

        $this->actingAs($this->user)->put($this->url('/'.$a->id), ['title' => 'A', 'type' => 'group', 'parent_id' => $b->id])
            ->assertSessionHasErrors('parent_id');
        $this->actingAs($this->user)->put($this->url('/'.$b->id), ['title' => 'B', 'type' => 'group', 'parent_id' => $b->id])
            ->assertSessionHasErrors('parent_id');

        $this->assertNull($a->fresh()->parent_id);
        $this->assertNull($b->fresh()->parent_id);
    }

    public function test_menu_item_can_be_updated(): void
    {
        $menu = $this->company->menus()->create(['title' => 'Old', 'type' => 'anchor', 'url' => 'about', 'sort_order' => 1]);

        $this->actingAs($this->user)->put($this->url('/'.$menu->id), [
            'title' => 'Website Lama', 'type' => 'url', 'url' => 'https://lama.example.test', 'status' => 'inactive',
        ])->assertSessionHasNoErrors();

        $menu->refresh();
        $this->assertSame('Website Lama', $menu->title);
        $this->assertSame('website-lama', $menu->slug);
        $this->assertSame('url', $menu->type);
        $this->assertSame('inactive', $menu->status);
    }

    public function test_deleting_a_parent_menu_removes_its_children(): void
    {
        $parent = $this->company->menus()->create(['title' => 'P', 'type' => 'group', 'sort_order' => 1]);
        $child = $this->company->menus()->create(['title' => 'C', 'type' => 'anchor', 'url' => 'about', 'parent_id' => $parent->id]);

        $this->actingAs($this->user)->delete($this->url('/'.$parent->id))->assertRedirect();

        $this->assertModelMissing($parent);
        $this->assertModelMissing($child);
    }

    public function test_tree_endpoint_reorders_and_nests_menus(): void
    {
        $a = $this->company->menus()->create(['title' => 'A', 'type' => 'anchor', 'url' => 'about', 'sort_order' => 1]);
        $b = $this->company->menus()->create(['title' => 'B', 'type' => 'anchor', 'url' => 'services', 'sort_order' => 2]);
        $c = $this->company->menus()->create(['title' => 'C', 'type' => 'group', 'sort_order' => 3]);

        $this->actingAs($this->user)->postJson($this->url('/tree'), ['tree' => [
            ['id' => $c->id, 'children' => [['id' => $b->id], ['id' => $a->id]]],
        ]])->assertOk()->assertJson(['saved' => true]);

        $this->assertNull($c->fresh()->parent_id);
        $this->assertSame(1, (int) $c->fresh()->sort_order);
        $this->assertSame($c->id, $b->fresh()->parent_id);
        $this->assertSame(1, (int) $b->fresh()->sort_order);
        $this->assertSame($c->id, $a->fresh()->parent_id);
        $this->assertSame(2, (int) $a->fresh()->sort_order);

        // Move everything back to the root, reversed.
        $this->actingAs($this->user)->postJson($this->url('/tree'), ['tree' => [
            ['id' => $a->id], ['id' => $b->id], ['id' => $c->id, 'children' => []],
        ]])->assertOk();

        $this->assertSame([$a->id, $b->id, $c->id], $this->company->menus()->whereNull('parent_id')->pluck('id')->all());
        $this->assertSame(0, $this->company->menus()->whereNotNull('parent_id')->count());
    }

    public function test_tree_endpoint_flattens_grandchildren_to_two_levels(): void
    {
        $a = $this->company->menus()->create(['title' => 'A', 'type' => 'group', 'sort_order' => 1]);
        $b = $this->company->menus()->create(['title' => 'B', 'type' => 'group', 'sort_order' => 2]);
        $b1 = $this->company->menus()->create(['title' => 'B1', 'type' => 'anchor', 'url' => 'about', 'parent_id' => $b->id]);

        // B (with child B1) is dropped into A.
        $this->actingAs($this->user)->postJson($this->url('/tree'), ['tree' => [
            ['id' => $a->id, 'children' => [['id' => $b->id]]],
        ]])->assertOk();

        $this->assertSame($a->id, $b->fresh()->parent_id);
        $this->assertSame($a->id, $b1->fresh()->parent_id, 'Grandchildren are promoted so the depth never exceeds 2.');
    }

    public function test_tree_endpoint_ignores_foreign_ids(): void
    {
        $mine = $this->company->menus()->create(['title' => 'Mine', 'type' => 'group', 'sort_order' => 1]);
        $foreign = $this->companyFor()->menus()->first();
        $originalSort = $foreign->sort_order;

        $this->actingAs($this->user)->postJson($this->url('/tree'), ['tree' => [
            ['id' => $foreign->id, 'children' => [['id' => $mine->id]]],
            ['id' => $mine->id, 'children' => [['id' => $foreign->id]]],
        ]])->assertOk();

        $foreign->refresh();
        $this->assertNull($foreign->parent_id);
        $this->assertSame($originalSort, $foreign->sort_order);
        $this->assertNull($mine->fresh()->parent_id);
        $this->assertSame(0, Menu::query()->where('parent_id', $mine->id)->count());
    }

    public function test_tree_endpoint_ignores_self_nesting(): void
    {
        $a = $this->company->menus()->create(['title' => 'A', 'type' => 'group', 'sort_order' => 1]);

        $this->actingAs($this->user)->postJson($this->url('/tree'), ['tree' => [
            ['id' => $a->id, 'children' => [['id' => $a->id]]],
        ]])->assertOk();

        $this->assertNull($a->fresh()->parent_id);
    }

    public function test_tree_endpoint_validates_payload(): void
    {
        $this->actingAs($this->user)->postJson($this->url('/tree'), [])->assertUnprocessable()->assertJsonValidationErrors('tree');
        $this->actingAs($this->user)->postJson($this->url('/tree'), ['tree' => [['children' => []]]])
            ->assertUnprocessable()->assertJsonValidationErrors('tree.0.id');
    }
}
