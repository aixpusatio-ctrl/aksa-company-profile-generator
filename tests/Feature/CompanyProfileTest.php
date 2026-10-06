<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\CompanyProfile;
use App\Models\Media;
use App\Models\Menu;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createWebsite(User $user, string $name, ?Template $template = null)
    {
        $template ??= $this->corporateTemplate();

        return $this->actingAs($user)->post('/dashboard/websites', [
            'name' => $name,
            'tagline' => 'Tagline perusahaan',
            'template' => $template->slug,
        ]);
    }

    // ------------------------------------------------------------------ Creation

    public function test_user_can_create_a_website_from_a_template(): void
    {
        $user = $this->userWithPlan('pro');
        $template = $this->corporateTemplate();

        $response = $this->createWebsite($user, 'PT Maju Jaya Abadi', $template);

        $company = CompanyProfile::query()->firstOrFail();
        $response->assertRedirect(route('websites.wizard', [$company, 'company']));

        $this->assertSame($user->id, $company->user_id);
        $this->assertSame($template->id, $company->template_id);
        $this->assertSame('pt-maju-jaya-abadi', $company->slug);
        $this->assertSame('Tagline perusahaan', $company->tagline);
        $this->assertSame($user->email, $company->email);
        $this->assertSame(CompanyProfile::STATUS_DRAFT, $company->status);
        $this->assertSame(2, $company->wizard_step);
        $this->assertTrue(ActivityLog::query()->where('action', 'website.created')->exists());
    }

    public function test_creating_a_website_creates_all_sections_in_template_order(): void
    {
        $template = Template::factory()->layout('modern-business')->create();
        $this->createWebsite($this->userWithPlan(), 'PT Sections', $template);

        $company = CompanyProfile::query()->firstOrFail();
        $sections = $company->sections()->get();

        $this->assertCount(count(config('website-templates.sections')), $sections);
        $this->assertTrue($sections->every(fn ($s) => $s->is_enabled));
        $this->assertSame(
            config('website-templates.layouts.modern-business.defaults.sections'),
            $sections->sortBy('sort_order')->pluck('key')->values()->all(),
        );
    }

    public function test_creating_a_website_creates_default_menus(): void
    {
        $this->createWebsite($this->userWithPlan(), 'PT Menu Default');

        $menus = CompanyProfile::query()->firstOrFail()->menus()->get();

        $this->assertSame(['Home', 'About', 'Services', 'Products', 'Projects', 'Team', 'Contact'], $menus->pluck('title')->all());
        $this->assertTrue($menus->every(fn (Menu $m) => $m->type === Menu::TYPE_ANCHOR && $m->parent_id === null && $m->status === 'active'));
        $this->assertSame('hero', $menus->first()->url);
    }

    public function test_slug_is_unique_across_websites(): void
    {
        $user = $this->userWithPlan('business');

        $this->createWebsite($user, 'Nusantara Group');
        $this->createWebsite($user, 'Nusantara Group');
        $this->createWebsite($user, 'Nusantara Group');

        $this->assertSame(
            ['nusantara-group', 'nusantara-group-2', 'nusantara-group-3'],
            CompanyProfile::query()->orderBy('id')->pluck('slug')->all(),
        );
    }

    public function test_reserved_and_too_short_names_get_a_safe_slug(): void
    {
        $user = $this->userWithPlan('business');

        $this->createWebsite($user, 'Admin');
        $this->createWebsite($user, 'AB');
        $this->createWebsite($user, 'www');

        $slugs = CompanyProfile::query()->orderBy('id')->pluck('slug')->all();
        $this->assertSame(['admin-site', 'ab-site', 'www-site'], $slugs);
    }

    public function test_draft_or_unknown_template_cannot_be_selected(): void
    {
        $user = $this->userWithPlan();
        $draft = Template::factory()->draft()->create();

        $this->actingAs($user)->post('/dashboard/websites', ['name' => 'PT X', 'template' => $draft->slug])
            ->assertSessionHasErrors('template');
        $this->actingAs($user)->post('/dashboard/websites', ['name' => 'PT X', 'template' => 'does-not-exist'])
            ->assertSessionHasErrors('template');
        $this->actingAs($user)->post('/dashboard/websites', ['template' => $this->corporateTemplate()->slug])
            ->assertSessionHasErrors('name');

        $this->assertSame(0, CompanyProfile::query()->count());
    }

    // ------------------------------------------------------------------ Plan limits

    public function test_free_plan_user_with_one_website_cannot_create_another(): void
    {
        $user = $this->userWithPlan('free');
        $this->assertSame('free', $user->planKey());

        $this->createWebsite($user, 'PT First')->assertRedirect();
        $this->assertSame(1, $user->companyProfiles()->count());

        $this->createWebsite($user, 'PT Second')->assertForbidden();
        $this->actingAs($user)->get('/dashboard/websites/create')->assertForbidden();
        $this->assertSame(1, $user->companyProfiles()->count());
    }

    public function test_pro_plan_allows_five_websites(): void
    {
        $user = $this->userWithPlan('pro');
        CompanyProfile::factory()->count(5)->create(['user_id' => $user->id]);

        $this->createWebsite($user, 'PT Sixth')->assertForbidden();
        $this->assertSame(5, $user->companyProfiles()->count());
    }

    public function test_business_plan_and_admins_are_unlimited(): void
    {
        $business = $this->userWithPlan('business');
        CompanyProfile::factory()->count(6)->create(['user_id' => $business->id]);
        $this->createWebsite($business, 'PT Seventh')->assertRedirect();
        $this->assertSame(7, $business->companyProfiles()->count());

        $admin = $this->admin();
        CompanyProfile::factory()->count(2)->create(['user_id' => $admin->id]);
        $this->createWebsite($admin, 'PT Admin Site')->assertRedirect();
        $this->assertSame(3, $admin->companyProfiles()->count());
    }

    public function test_expired_trial_falls_back_to_free_plan(): void
    {
        $user = User::factory()->create();
        $user->subscriptions()->create([
            'plan' => 'pro', 'status' => 'trialing', 'price' => 0,
            'starts_at' => now()->subDays(30), 'trial_ends_at' => now()->subDay(),
        ]);

        $this->assertSame('free', $user->planKey());
        $this->assertFalse($user->canUseCustomDomain());
    }

    // ------------------------------------------------------------------ Editor tabs

    public function test_info_tab_updates_company_information_and_filters_social_links(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/info", [
            'name' => 'PT Baru Sekali',
            'tagline' => 'Tagline baru',
            'description' => 'Deskripsi singkat',
            'established_year' => 2001,
            'phone' => '021-555-1234',
            'email' => 'halo@baru.test',
            'whatsapp' => '0812-3456-7890',
            'website' => 'https://baru.test',
            'social_links' => [
                'facebook' => 'https://facebook.com/baru',
                'instagram' => '',
                'myspace' => 'https://myspace.com/baru',
            ],
        ])->assertRedirect()->assertSessionHasNoErrors();

        $company->refresh();
        $this->assertSame('PT Baru Sekali', $company->name);
        $this->assertSame(2001, $company->established_year);
        $this->assertSame('halo@baru.test', $company->email);
        $this->assertSame(['facebook' => 'https://facebook.com/baru'], $company->social_links);
        $this->assertSame('https://wa.me/6281234567890', $company->whatsappUrl());
    }

    public function test_info_tab_validates_input(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/info", [
            'name' => '',
            'email' => 'not-an-email',
            'whatsapp' => 'call me',
            'website' => 'javascript:alert(1)',
            'social_links' => ['facebook' => 'not a url'],
            'established_year' => 1500,
        ])->assertSessionHasErrors(['name', 'email', 'whatsapp', 'website', 'social_links.facebook', 'established_year']);
    }

    public function test_about_tab_sanitizes_rich_text(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/about", [
            'about' => '<p onclick="steal()">Kami <strong>terpercaya</strong></p><script>alert("xss")</script><a href="javascript:alert(1)">klik</a>',
            'vision' => 'Menjadi yang terbaik',
            'mission' => '<ul><li>Satu</li><li>Dua</li></ul><iframe src="https://evil.test"></iframe>',
            'history' => '<img src="x" onerror="alert(1)">Sejarah',
            'company_values' => "Integritas\nInovasi",
        ])->assertRedirect()->assertSessionHasNoErrors();

        $company->refresh();
        $this->assertStringContainsString('<strong>terpercaya</strong>', $company->about);
        $this->assertStringNotContainsString('<script', $company->about);
        $this->assertStringNotContainsString('alert("xss")', $company->about);
        $this->assertStringNotContainsString('onclick', $company->about);
        $this->assertStringNotContainsString('javascript:', $company->about);
        $this->assertStringNotContainsString('<iframe', $company->mission);
        $this->assertStringNotContainsString('onerror', $company->history);
        $this->assertSame(['Satu', 'Dua'], $company->missionItems());
        $this->assertSame(['Integritas', 'Inovasi'], $company->valueItems());
    }

    public function test_contact_tab_updates_and_validates(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);
        $url = "/dashboard/websites/{$company->id}/edit/contact";

        $this->actingAs($user)->put($url, [
            'address' => 'Jl. Sudirman No. 1',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
            'latitude' => '-6.914744',
            'longitude' => '107.609810',
            'working_hours' => 'Senin - Jumat 08.00-17.00',
            'google_maps_url' => 'https://maps.google.com/?q=bandung',
        ])->assertSessionHasNoErrors();

        $company->refresh();
        $this->assertSame('Bandung', $company->city);
        $this->assertEqualsWithDelta(-6.914744, $company->latitude, 0.00001);
        $this->assertStringContainsString('Jl. Sudirman No. 1, Bandung, Jawa Barat', $company->fullAddress());
        $this->assertStringContainsString('-6.914744', $company->mapEmbedUrl());

        $this->actingAs($user)->put($url, ['latitude' => 120, 'longitude' => 500, 'google_maps_url' => 'http://insecure.test'])
            ->assertSessionHasErrors(['latitude', 'longitude', 'google_maps_url']);
    }

    public function test_branding_tab_stores_valid_colors_and_rejects_invalid_ones(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);
        $url = "/dashboard/websites/{$company->id}/edit/branding";

        $this->actingAs($user)->put($url, ['branding' => [
            'primary_color' => '#FF0000',
            'button_style' => 'pill',
            'heading_font' => config('website-templates.fonts')[0],
        ]])->assertSessionHasNoErrors();

        $company->refresh();
        $this->assertSame('#FF0000', $company->branding['primary_color']);
        $this->assertSame('#FF0000', $company->brand()['primary_color']);
        $this->assertSame('pill', $company->brand()['button_style']);

        // Branding values merge with previous overrides.
        $this->actingAs($user)->put($url, ['branding' => ['secondary_color' => '#00ff00']])->assertSessionHasNoErrors();
        $this->assertSame('#FF0000', $company->fresh()->branding['primary_color']);
        $this->assertSame('#00ff00', $company->fresh()->branding['secondary_color']);

        foreach (['red', '#fff', '#12345g', '#1234567', 'url(javascript:alert(1))'] as $invalid) {
            $this->actingAs($user)->put($url, ['branding' => ['primary_color' => $invalid]])
                ->assertSessionHasErrors('branding.primary_color');
        }
        $this->actingAs($user)->put($url, ['branding' => ['heading_font' => 'Comic Sans; } body { display:none', 'border_radius' => 'huge']])
            ->assertSessionHasErrors(['branding.heading_font', 'branding.border_radius']);

        $this->assertSame('#FF0000', $company->fresh()->branding['primary_color']);
    }

    public function test_branding_can_be_reset_to_template_defaults(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['branding' => ['primary_color' => '#123456']]);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/branding", ['reset_branding' => '1'])
            ->assertRedirect();

        $company->refresh();
        $this->assertNull($company->branding);
        $this->assertSame(config('website-templates.layouts.corporate.defaults.primary_color'), $company->brand()['primary_color']);
    }

    public function test_branding_logo_upload_is_stored_in_media_library(): void
    {
        Storage::fake('public');
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/branding", [
            'logo' => UploadedFile::fake()->image('logo.png', 200, 80),
        ])->assertSessionHasNoErrors();

        $company->refresh();
        $this->assertNotNull($company->logo);
        Storage::disk('public')->assertExists($company->logo);
        $this->assertDatabaseHas('media', ['path' => $company->logo, 'user_id' => $user->id, 'collection' => 'logos', 'company_profile_id' => $company->id]);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/branding", ['logo_remove' => '1'])->assertSessionHasNoErrors();
        $this->assertNull($company->fresh()->logo);
    }

    public function test_media_library_pick_must_belong_to_the_user(): void
    {
        $user = $this->userWithPlan();
        $other = $this->userWithPlan();
        $company = $this->companyFor($user);
        Media::query()->create([
            'user_id' => $other->id, 'collection' => 'images', 'disk' => 'public',
            'path' => 'media/other/secret.png', 'filename' => 'secret.png', 'mime_type' => 'image/png', 'size' => 10,
        ]);
        Media::query()->create([
            'user_id' => $user->id, 'collection' => 'images', 'disk' => 'public',
            'path' => 'media/mine/hero.png', 'filename' => 'hero.png', 'mime_type' => 'image/png', 'size' => 10,
        ]);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/branding", ['hero_image_media' => 'media/other/secret.png']);
        $this->assertNull($company->fresh()->hero_image);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/branding", ['hero_image_media' => 'media/mine/hero.png']);
        $this->assertSame('media/mine/hero.png', $company->fresh()->hero_image);
    }

    public function test_seo_tab_updates_meta_fields(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/seo", [
            'seo_title' => 'Judul SEO',
            'seo_description' => 'Deskripsi SEO',
            'seo_keywords' => 'a, b, c',
            'og_title' => 'OG Title',
            'og_description' => 'OG Description',
        ])->assertSessionHasNoErrors();

        $company->refresh();
        $this->assertSame('Judul SEO', $company->seo_title);
        $this->assertSame('OG Title', $company->og_title);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/seo", ['seo_title' => str_repeat('a', 121)])
            ->assertSessionHasErrors('seo_title');
    }

    public function test_unknown_editor_tab_returns_404(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/edit/hacker", ['name' => 'X'])->assertNotFound();
        $this->actingAs($user)->get("/dashboard/websites/{$company->id}/edit/hacker")->assertNotFound();
    }

    // ------------------------------------------------------------------ Autosave

    public function test_autosave_saves_partial_text_fields_as_json(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['name' => 'PT Autosave']);

        $this->actingAs($user)
            ->postJson("/dashboard/websites/{$company->id}/autosave", ['_tab' => 'info', 'tagline' => 'Disimpan otomatis'])
            ->assertOk()
            ->assertJson(['saved' => true])
            ->assertJsonStructure(['saved', 'at']);

        $company->refresh();
        $this->assertSame('Disimpan otomatis', $company->tagline);
        $this->assertSame('PT Autosave', $company->name, 'Fields that were not sent must not be touched.');
    }

    public function test_autosave_sanitizes_rich_text(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)
            ->postJson("/dashboard/websites/{$company->id}/autosave", ['_tab' => 'about', 'about' => '<p>Aman</p><script>alert(1)</script>'])
            ->assertOk();

        $this->assertSame('<p>Aman</p>', $company->fresh()->about);
    }

    public function test_autosave_validates_input_and_tab(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);
        $url = "/dashboard/websites/{$company->id}/autosave";

        $this->actingAs($user)->postJson($url, ['_tab' => 'info', 'email' => 'nope'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
        // Clearing a required field is rejected instead of writing NULL.
        $this->actingAs($user)->postJson($url, ['_tab' => 'info', 'name' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
        $this->assertNotNull($company->fresh()->name);
        $this->actingAs($user)->postJson($url, ['_tab' => 'nope', 'name' => 'X'])
            ->assertUnprocessable();
    }

    // ------------------------------------------------------------------ Wizard

    public function test_wizard_tab_step_saves_and_advances(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['wizard_step' => 2]);

        $this->actingAs($user)->post("/dashboard/websites/{$company->id}/wizard/company", [
            'name' => 'PT Wizard', 'email' => 'wizard@example.test', 'description' => 'Deskripsi',
        ])->assertRedirect(route('websites.wizard', [$company, 'about']));

        $company->refresh();
        $this->assertSame('PT Wizard', $company->name);
        $this->assertSame(3, $company->wizard_step);

        $this->actingAs($user)->post("/dashboard/websites/{$company->id}/wizard/about", ['about' => '<p>Tentang</p>'])
            ->assertRedirect(route('websites.wizard', [$company, 'services']));
        $this->assertSame(4, $company->fresh()->wizard_step);

        // Content steps are managed on the page itself, saving just advances.
        $this->actingAs($user)->post("/dashboard/websites/{$company->id}/wizard/services")
            ->assertRedirect(route('websites.wizard', [$company, 'products']));
        $this->assertSame(5, $company->fresh()->wizard_step);
    }

    public function test_wizard_step_validation_errors_do_not_advance(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['wizard_step' => 2]);

        $this->actingAs($user)->post("/dashboard/websites/{$company->id}/wizard/company", ['name' => ''])
            ->assertSessionHasErrors('name');

        $this->assertSame(2, $company->fresh()->wizard_step);
    }

    public function test_wizard_back_action_goes_to_previous_step_and_never_lowers_progress(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['wizard_step' => 6]);

        $this->actingAs($user)->post("/dashboard/websites/{$company->id}/wizard/about", ['about' => 'x', 'action' => 'back'])
            ->assertRedirect(route('websites.wizard', [$company, 'company']));

        $this->assertSame(6, $company->fresh()->wizard_step);
    }

    public function test_wizard_template_step_changes_template_and_resets_layout(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['branding' => ['primary_color' => '#123456']]);
        $new = Template::factory()->layout('technology')->create();

        $this->actingAs($user)->post("/dashboard/websites/{$company->id}/wizard/template", ['template' => $new->slug])
            ->assertRedirect(route('websites.wizard', [$company, 'company']));

        $company->refresh();
        $this->assertSame($new->id, $company->template_id);
        $this->assertNull($company->branding);
        $this->assertSame(
            config('website-templates.layouts.technology.defaults.sections'),
            $company->sections()->get()->sortBy('sort_order')->pluck('key')->values()->all(),
        );
    }

    public function test_unknown_wizard_step_returns_404(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->post("/dashboard/websites/{$company->id}/wizard/hacking")->assertNotFound();
        $this->actingAs($user)->get("/dashboard/websites/{$company->id}/wizard/hacking")->assertNotFound();
    }

    // ------------------------------------------------------------------ Template change

    public function test_template_can_be_changed_keeping_branding(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['branding' => ['primary_color' => '#123456']]);
        $new = Template::factory()->layout('consulting')->create();

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/template", ['template' => $new->slug])
            ->assertRedirect()->assertSessionHasNoErrors();

        $company->refresh();
        $this->assertSame($new->id, $company->template_id);
        $this->assertSame('consulting', $company->layout());
        $this->assertSame('#123456', $company->branding['primary_color']);
        $this->assertTrue(ActivityLog::query()->where('action', 'website.template_changed')->exists());
    }

    public function test_template_change_with_reset_layout_and_wizard_redirect(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['branding' => ['primary_color' => '#123456']]);
        $new = Template::factory()->layout('construction')->create();

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/template", [
            'template' => $new->slug, 'reset_layout' => '1', 'wizard' => '1',
        ])->assertRedirect(route('websites.wizard', [$company, 'company']));

        $company->refresh();
        $this->assertNull($company->branding);
        $this->assertSame(
            config('website-templates.layouts.construction.defaults.sections'),
            $company->sections()->get()->sortBy('sort_order')->pluck('key')->values()->all(),
        );
    }

    public function test_draft_template_cannot_be_applied(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);
        $draft = Template::factory()->draft()->create();

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/template", ['template' => $draft->slug])
            ->assertSessionHasErrors('template');

        $this->assertNotSame($draft->id, $company->fresh()->template_id);
    }

    // ------------------------------------------------------------------ Sections

    public function test_sections_can_be_reordered_and_toggled_via_json(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->putJson("/dashboard/websites/{$company->id}/sections", ['sections' => [
            ['key' => 'contact', 'is_enabled' => true, 'title' => '<b>Hubungi</b> Kami'],
            ['key' => 'hero', 'is_enabled' => true],
            ['key' => 'services', 'is_enabled' => false],
            ['key' => 'unknown', 'is_enabled' => true],
        ]])->assertOk()->assertJson(['saved' => true]);

        $sections = $company->sections()->get()->keyBy('key');
        $this->assertSame(1, (int) $sections['contact']->sort_order);
        $this->assertSame('Hubungi Kami', $sections['contact']->title);
        $this->assertSame(2, (int) $sections['hero']->sort_order);
        $this->assertFalse($sections['services']->is_enabled);
        $this->assertFalse($sections->has('unknown'));
    }

    public function test_sections_update_validates_payload(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->putJson("/dashboard/websites/{$company->id}/sections", ['sections' => [['key' => 'hero']]])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('sections.0.is_enabled');
    }

    // ------------------------------------------------------------------ Subdomain

    public function test_subdomain_can_be_changed(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);

        $this->actingAs($user)->put("/dashboard/websites/{$company->id}/domains/subdomain", ['slug' => 'Nama-Baru'])
            ->assertSessionHasNoErrors();

        $this->assertSame('nama-baru', $company->fresh()->slug);
    }

    public function test_subdomain_rejects_reserved_invalid_and_taken_slugs(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user);
        $this->companyFor(null, ['slug' => 'sudah-dipakai']);
        $url = "/dashboard/websites/{$company->id}/domains/subdomain";

        foreach (['admin', 'www', 'ab', 'x', 'sudah-dipakai'] as $slug) {
            $this->actingAs($user)->put($url, ['slug' => $slug])->assertSessionHasErrors('slug');
        }

        $this->assertSame($company->slug, $company->fresh()->slug);
    }

    // ------------------------------------------------------------------ Delete

    public function test_website_deletion_requires_typing_its_name(): void
    {
        $user = $this->userWithPlan();
        $company = $this->companyFor($user, ['name' => 'PT Hapus Saya']);

        $this->actingAs($user)->delete("/dashboard/websites/{$company->id}", ['confirm_name' => 'salah'])
            ->assertSessionHasErrors('confirm_name');
        $this->assertModelExists($company);

        $this->actingAs($user)->delete("/dashboard/websites/{$company->id}", ['confirm_name' => 'PT Hapus Saya'])
            ->assertRedirect(route('websites.index'));
        $this->assertModelMissing($company);
        $this->assertSame(0, Menu::query()->where('company_profile_id', $company->id)->count());
        $this->assertTrue(ActivityLog::query()->where('action', 'website.deleted')->exists());
    }

    // ------------------------------------------------------------------ API

    public function test_subdomain_availability_api(): void
    {
        $this->companyFor(null, ['slug' => 'dipakai']);

        $this->getJson('/api/subdomain-availability?slug=Baru Sekali')
            ->assertOk()
            ->assertJson(['slug' => 'baru-sekali', 'valid' => true, 'available' => true, 'host' => 'baru-sekali.'.config('platform.domain')]);
        $this->getJson('/api/subdomain-availability?slug=dipakai')->assertJson(['valid' => true, 'available' => false]);
        $this->getJson('/api/subdomain-availability?slug=admin')->assertJson(['valid' => false, 'available' => false]);
    }
}
