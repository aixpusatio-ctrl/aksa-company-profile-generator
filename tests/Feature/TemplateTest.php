<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Support\DemoContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TemplateTest extends TestCase
{
    use RefreshDatabase;

    public static function layouts(): array
    {
        $config = require dirname(__DIR__, 2).'/config/website-templates.php';

        return collect(array_keys($config['layouts']))->mapWithKeys(fn ($key) => [$key => [$key]])->all();
    }

    // ------------------------------------------------------------------ Public gallery

    public function test_public_template_gallery_lists_published_templates(): void
    {
        Template::factory()->create(['name' => 'Template Terbit']);
        Template::factory()->draft()->create(['name' => 'Template Draf']);

        $this->get('/templates')
            ->assertOk()
            ->assertSee('Template Terbit')
            ->assertDontSee('Template Draf');
    }

    public function test_public_template_gallery_filters_by_category_and_search(): void
    {
        $category = TemplateCategory::query()->create(['name' => 'Konstruksi', 'slug' => 'konstruksi']);
        Template::factory()->create(['name' => 'Bangun Jaya', 'template_category_id' => $category->id]);
        Template::factory()->create(['name' => 'Kopi Senja']);

        $this->get('/templates?category=konstruksi')->assertOk()->assertSee('Bangun Jaya')->assertDontSee('Kopi Senja');
        $this->get('/templates?q=Kopi')->assertOk()->assertSee('Kopi Senja')->assertDontSee('Bangun Jaya');
    }

    public function test_public_template_detail_page(): void
    {
        $template = Template::factory()->create(['name' => 'Detail Template']);

        $this->get('/templates/'.$template->slug)->assertOk()->assertSee('Detail Template');
    }

    #[DataProvider('layouts')]
    public function test_template_preview_renders_for_every_layout(string $layout): void
    {
        $template = Template::factory()->layout($layout)->create();
        $demo = DemoContent::forLayout($layout);

        $this->get('/templates/'.$template->slug.'/render')
            ->assertOk()
            ->assertSee(e($demo['company']['name']), false)
            ->assertSee('<meta name="robots" content="noindex,nofollow">', false);
    }

    #[DataProvider('layouts')]
    public function test_template_preview_renders_demo_pages_for_every_layout(string $layout): void
    {
        $template = Template::factory()->layout($layout)->create();
        $page = collect(DemoContent::forLayout($layout)['pages'] ?? [])->first();

        if (! $page) {
            $this->assertTrue(true, "Layout {$layout} has no demo pages.");

            return;
        }

        $this->get('/templates/'.$template->slug.'/render?page='.$page['slug'])
            ->assertOk()
            ->assertSee(e($page['title']), false);
    }

    public function test_template_preview_of_unknown_page_returns_404(): void
    {
        $template = Template::factory()->layout('corporate')->create();

        $this->get('/templates/'.$template->slug.'/render?page=tidak-ada')->assertNotFound();
    }

    public function test_draft_template_is_hidden_from_guests_and_users(): void
    {
        $draft = Template::factory()->draft()->layout('corporate')->create();

        $this->get('/templates/'.$draft->slug)->assertNotFound();
        $this->get('/templates/'.$draft->slug.'/render')->assertNotFound();
        $this->actingAs($this->userWithPlan())->get('/templates/'.$draft->slug.'/render')->assertNotFound();
    }

    public function test_admin_can_preview_draft_template(): void
    {
        $draft = Template::factory()->draft()->layout('corporate')->create();

        $this->actingAs($this->admin())->get('/templates/'.$draft->slug.'/render')->assertOk();
    }

    public function test_templates_api_lists_published_templates(): void
    {
        $published = Template::factory()->create(['name' => 'API Template']);
        Template::factory()->draft()->create(['name' => 'API Draft']);

        $this->getJson('/api/templates')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', $published->slug)
            ->assertJsonPath('data.0.preview_url', route('templates.render', $published));
    }

    // ------------------------------------------------------------------ Admin CRUD

    public function test_admin_can_create_a_template(): void
    {
        Storage::fake('public');
        $category = TemplateCategory::query()->create(['name' => 'Teknologi', 'slug' => 'teknologi']);

        $this->actingAs($this->admin())->post('/admin/templates', [
            'name' => 'Startup Biru',
            'layout' => 'technology',
            'template_category_id' => $category->id,
            'description' => 'Untuk startup',
            'status' => 'published',
            'is_featured' => '1',
            'sort_order' => '',
            'thumbnail' => UploadedFile::fake()->image('thumb.png', 800, 600),
            'settings' => ['primary_color' => '#0000ff', 'heading_font' => '', 'sections' => ['hero', 'services', 'contact']],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $template = Template::query()->where('name', 'Startup Biru')->firstOrFail();
        $this->assertSame('startup-biru', $template->slug);
        $this->assertSame('technology', $template->layout);
        $this->assertTrue($template->is_featured);
        $this->assertSame(0, (int) $template->sort_order);
        $this->assertSame(['primary_color' => '#0000ff', 'sections' => ['hero', 'services', 'contact']], $template->settings);
        $this->assertSame('#0000ff', $template->resolvedSettings()['primary_color']);
        $this->assertSame(config('website-templates.layouts.technology.defaults.heading_font'), $template->resolvedSettings()['heading_font']);
        $this->assertNotNull($template->thumbnail);
        Storage::disk('public')->assertExists($template->thumbnail);
        $this->assertTrue(ActivityLog::query()->where('action', 'template.created')->exists());
    }

    public function test_template_slug_is_unique(): void
    {
        Template::factory()->create(['slug' => 'kembar']);

        $this->actingAs($this->admin())->post('/admin/templates', [
            'name' => 'Kembar', 'layout' => 'corporate', 'status' => 'draft',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('templates', ['slug' => 'kembar-2', 'name' => 'Kembar']);
    }

    public function test_template_validation(): void
    {
        $this->actingAs($this->admin())->post('/admin/templates', [
            'name' => '',
            'layout' => 'not-a-layout',
            'status' => 'archived',
            'slug' => 'Bad Slug!',
            'settings' => ['primary_color' => 'blue', 'sections' => ['hero', 'hacker']],
        ])->assertSessionHasErrors(['name', 'layout', 'status', 'slug', 'settings.primary_color', 'settings.sections.1']);

        $this->assertSame(0, Template::query()->count());
    }

    public function test_admin_can_update_a_template(): void
    {
        $template = Template::factory()->layout('corporate')->create(['name' => 'Lama', 'slug' => 'lama']);

        $this->actingAs($this->admin())->put('/admin/templates/'.$template->slug, [
            'name' => 'Baru', 'slug' => 'lama', 'layout' => 'minimal', 'status' => 'draft',
        ])->assertRedirect(route('admin.templates.edit', 'lama'))->assertSessionHasNoErrors();

        $template->refresh();
        $this->assertSame('Baru', $template->name);
        $this->assertSame('lama', $template->slug);
        $this->assertSame('minimal', $template->layout);
        $this->assertSame('draft', $template->status);
    }

    public function test_admin_can_delete_unused_template(): void
    {
        $template = Template::factory()->create();

        $this->actingAs($this->admin())->delete('/admin/templates/'.$template->slug)
            ->assertRedirect(route('admin.templates.index'));

        $this->assertModelMissing($template);
    }

    public function test_template_in_use_cannot_be_deleted(): void
    {
        $company = $this->companyFor();
        $template = $company->template;

        $this->actingAs($this->admin())->delete('/admin/templates/'.$template->slug)
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertModelExists($template);
        $this->assertSame($template->id, $company->fresh()->template_id);
    }

    public function test_admin_can_duplicate_a_template(): void
    {
        $template = Template::factory()->create(['name' => 'Asli', 'slug' => 'asli', 'is_featured' => true, 'settings' => ['primary_color' => '#111111']]);

        $this->actingAs($this->admin())->post('/admin/templates/asli/duplicate')
            ->assertRedirect(route('admin.templates.edit', 'asli-copy'));

        $copy = Template::query()->where('slug', 'asli-copy')->firstOrFail();
        $this->assertSame('Asli (Copy)', $copy->name);
        $this->assertSame(Template::STATUS_DRAFT, $copy->status);
        $this->assertFalse($copy->is_featured);
        $this->assertSame($template->layout, $copy->layout);
        $this->assertSame(['primary_color' => '#111111'], $copy->settings);

        $this->actingAs($this->admin())->post('/admin/templates/asli/duplicate');
        $this->assertDatabaseHas('templates', ['slug' => 'asli-copy-2']);
    }

    public function test_admin_can_toggle_publish_status(): void
    {
        $template = Template::factory()->create();

        $this->actingAs($this->admin())->post('/admin/templates/'.$template->slug.'/publish')->assertRedirect();
        $this->assertSame(Template::STATUS_DRAFT, $template->fresh()->status);

        $this->actingAs($this->admin())->post('/admin/templates/'.$template->slug.'/publish')->assertRedirect();
        $this->assertSame(Template::STATUS_PUBLISHED, $template->fresh()->status);
    }

    public function test_admin_can_toggle_featured(): void
    {
        $template = Template::factory()->create(['is_featured' => false]);

        $this->actingAs($this->admin())->post('/admin/templates/'.$template->slug.'/feature')->assertRedirect();
        $this->assertTrue($template->fresh()->is_featured);

        $this->actingAs($this->admin())->post('/admin/templates/'.$template->slug.'/feature')->assertRedirect();
        $this->assertFalse($template->fresh()->is_featured);
    }

    public function test_admin_template_pages_render(): void
    {
        $template = Template::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->get('/admin/templates')->assertOk()->assertSee($template->name);
        $this->actingAs($admin)->get('/admin/templates/create')->assertOk();
        $this->actingAs($admin)->get('/admin/templates/'.$template->slug.'/edit')->assertOk();
    }

    // ------------------------------------------------------------------ Categories

    public function test_admin_can_manage_template_categories(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Kuliner & Kafe'])->assertSessionHasNoErrors();
        $category = TemplateCategory::query()->where('slug', 'kuliner-kafe')->firstOrFail();

        $this->actingAs($admin)->put('/admin/categories/'.$category->id, ['name' => 'Kuliner', 'slug' => 'kuliner'])->assertSessionHasNoErrors();
        $this->assertSame('kuliner', $category->fresh()->slug);

        $this->actingAs($admin)->post('/admin/categories', ['name' => 'Kuliner'])->assertSessionHasErrors('slug');

        $template = Template::factory()->create(['template_category_id' => $category->id]);
        $this->actingAs($admin)->delete('/admin/categories/'.$category->id)->assertRedirect();
        $this->assertModelMissing($category);
        $this->assertNull($template->fresh()->template_category_id);
    }

    public function test_category_with_empty_sort_order_can_be_created(): void
    {
        // HTML forms submit an empty string, converted to null by middleware.
        $this->actingAs($this->admin())
            ->post('/admin/categories', ['name' => 'Tanpa Urutan', 'sort_order' => ''])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('template_categories', ['slug' => 'tanpa-urutan']);
    }
}
