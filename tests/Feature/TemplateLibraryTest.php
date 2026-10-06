<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use App\Support\DemoContent;
use App\Support\Website\ComponentRegistry;
use App\Support\Website\DesignSystem;
use App\Support\Website\TemplateLibrary;
use Database\Seeders\TemplateCategorySeeder;
use Database\Seeders\TemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TemplateLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_library_defines_fifty_unique_templates_in_ten_plus_categories(): void
    {
        $all = TemplateLibrary::all();

        $this->assertCount(50, $all);
        $this->assertCount(50, array_unique(array_column($all, 'slug')));
        $this->assertGreaterThanOrEqual(10, count(array_unique(array_column($all, 'category'))));

        foreach ($all as $template) {
            $this->assertArrayHasKey($template['category'], TemplateLibrary::CATEGORIES);
            $this->assertArrayHasKey($template['layout'], config('website-templates.layouts'), $template['slug']);
            $this->assertNotEmpty($template['style']);
        }
    }

    public function test_every_composed_template_references_existing_components_and_tokens(): void
    {
        foreach (TemplateLibrary::all() as $template) {
            if ($template['layout'] !== 'composer') {
                continue;
            }

            foreach ($template['config']['components'] as $slot => $variant) {
                $this->assertTrue(ComponentRegistry::exists($slot, $variant), "{$template['slug']}: missing component {$slot}/{$variant}");
            }

            foreach ($template['config']['design'] as $token => $value) {
                $this->assertContains($value, DesignSystem::OPTIONS[$token] ?? [], "{$template['slug']}: invalid design token {$token}={$value}");
            }
        }
    }

    public function test_composed_templates_are_genuinely_different(): void
    {
        $signatures = [];

        foreach (TemplateLibrary::all() as $template) {
            if ($template['layout'] !== 'composer') {
                continue;
            }

            $c = $template['config']['components'];
            $d = $template['config']['design'];
            $signature = implode('|', [$c['navbar'], $c['hero'], $c['footer'], $d['theme']]);

            $this->assertNotContains($signature, $signatures, "{$template['slug']} repeats navbar+hero+footer+theme of another template");
            $signatures[] = $signature;
        }

        $this->assertCount(40, $signatures);
        $this->assertGreaterThanOrEqual(15, count(array_unique(array_map(fn ($t) => $t['config']['components']['hero'] ?? 'crafted-'.$t['slug'], TemplateLibrary::all()))));
    }

    public function test_component_library_is_large_enough(): void
    {
        $this->assertGreaterThanOrEqual(100, ComponentRegistry::count());

        foreach (DesignSystem::SLOTS as $slot => $default) {
            $this->assertTrue(ComponentRegistry::exists($slot, $default), "default variant {$slot}/{$default} missing");
        }
    }

    public function test_every_composed_template_has_its_own_demo_company(): void
    {
        $names = [];

        foreach (TemplateLibrary::all() as $template) {
            $demo = DemoContent::for($template['demo']);
            $this->assertNotEmpty($demo['services'], $template['slug']);
            $this->assertNotEmpty($demo['projects'], $template['slug']);
            $names[] = $demo['company']['name'];
        }

        $this->assertCount(50, array_unique($names), 'Each template should showcase a different demo company.');
    }

    public function test_all_fifty_templates_render_home_and_custom_page(): void
    {
        $this->seed([TemplateCategorySeeder::class, TemplateSeeder::class]);

        foreach (Template::query()->get() as $template) {
            $demo = DemoContent::for($template->demoKey());

            $this->get('/templates/'.$template->slug.'/render')
                ->assertOk()
                ->assertSee(e($demo['company']['name']), false);

            $this->get('/templates/'.$template->slug.'/render?page=karir')->assertOk();
        }
    }

    public function test_invalid_design_tokens_fall_back_to_defaults(): void
    {
        $ds = new DesignSystem(['theme' => 'neon', 'card' => 'bordered'], ['hero' => 'does-not-exist']);

        $this->assertSame('light', $ds->get('theme'));
        $this->assertSame('bordered', $ds->get('card'));
        $this->assertSame('split', $ds->variant('hero'));
    }

    public function test_admin_can_change_composition_of_a_composed_template(): void
    {
        $admin = User::factory()->admin()->create();
        $template = Template::factory()->create(['layout' => 'composer', 'config' => ['components' => ['hero' => 'split'], 'design' => []]]);

        $this->actingAs($admin)->put(route('admin.templates.update', $template), [
            'name' => $template->name,
            'slug' => $template->slug,
            'layout' => 'composer',
            'status' => 'published',
            'config' => [
                'components' => ['hero' => 'editorial', 'navbar' => 'not-a-component'],
                'design' => ['theme' => 'dark', 'card' => 'hacked'],
            ],
        ])->assertRedirect();

        $template->refresh();
        $this->assertSame('editorial', $template->config['components']['hero']);
        $this->assertArrayNotHasKey('navbar', $template->config['components']);
        $this->assertSame(['theme' => 'dark'], $template->config['design']);
        $this->assertSame('editorial', $template->designSystem()->variant('hero'));
    }

    public function test_preview_overrides_are_only_for_admins_outside_local(): void
    {
        $this->seed([TemplateCategorySeeder::class, TemplateSeeder::class]);
        $url = '/templates/business-grid/render?c[navbar]=sidebar';

        // Testing env is not "local": guests get the stored composition.
        $this->get($url)->assertOk()->assertDontSee('padding-left: 16rem', false);

        $this->actingAs(User::factory()->admin()->create())->get($url)->assertOk()->assertSee('padding-left: 16rem', false);
    }
}
