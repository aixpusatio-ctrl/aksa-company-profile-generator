<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Smoke tests for the dashboard screens (Blade views) and user settings.
 */
class DashboardPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private CompanyProfile $company;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->userWithPlan();
        $this->company = $this->companyFor($this->user, ['name' => 'PT Dasbor Uji']);
        $this->company->services()->create(['title' => 'Layanan Dasbor']);
        $this->company->pages()->create(['title' => 'Halaman Dasbor', 'slug' => 'halaman-dasbor', 'status' => 'draft']);
        $this->company->domains()->create(['domain' => 'www.dasbor.test', 'type' => 'subdomain', 'verification_token' => 'cpg-x', 'status' => 'pending']);
        $this->company->contactMessages()->create(['name' => 'Pengirim Pesan', 'email' => 'p@example.test', 'message' => 'Halo dari pengunjung']);
    }

    public function test_global_dashboard_pages_render(): void
    {
        foreach (['/dashboard', '/dashboard/websites', '/dashboard/websites/create', '/dashboard/templates', '/dashboard/domains', '/dashboard/media', '/dashboard/notifications', '/dashboard/settings'] as $uri) {
            $this->actingAs($this->user)->get($uri)->assertOk();
        }

        $this->actingAs($this->user)->get('/dashboard/websites')->assertSee('PT Dasbor Uji');
    }

    public function test_website_management_pages_render(): void
    {
        $base = '/dashboard/websites/'.$this->company->id;

        $this->actingAs($this->user)->get($base)->assertOk()->assertSee('PT Dasbor Uji');

        foreach (['/preview', '/template', '/sections', '/pages', '/pages/create', '/menus', '/domains', '/messages'] as $suffix) {
            $this->actingAs($this->user)->get($base.$suffix)->assertOk();
        }

        $this->actingAs($this->user)->get($base.'/content/services')->assertOk()->assertSee('Layanan Dasbor');
        $this->actingAs($this->user)->get($base.'/messages')->assertSee('Pengirim Pesan');
        $this->actingAs($this->user)->get($base.'/domains')->assertSee('www.dasbor.test')->assertSee($this->company->domains()->value('verification_token'));
        $page = $this->company->pages()->first();
        $this->actingAs($this->user)->get($base.'/pages/'.$page->id.'/edit')->assertOk()->assertSee('Halaman Dasbor');
    }

    public function test_every_editor_tab_renders(): void
    {
        foreach (['info', 'about', 'contact', 'branding', 'seo'] as $tab) {
            $this->actingAs($this->user)->get("/dashboard/websites/{$this->company->id}/edit/{$tab}")->assertOk();
        }
    }

    public function test_every_content_type_page_renders(): void
    {
        foreach (['services', 'products', 'projects', 'team', 'testimonials', 'gallery'] as $type) {
            $this->actingAs($this->user)->get("/dashboard/websites/{$this->company->id}/content/{$type}")->assertOk();
        }
    }

    public function test_every_wizard_step_renders(): void
    {
        foreach (['template', 'company', 'about', 'services', 'products', 'team', 'projects', 'contact', 'seo', 'preview', 'publish'] as $step) {
            $this->actingAs($this->user)->get("/dashboard/websites/{$this->company->id}/wizard/{$step}")->assertOk();
        }

        // Without a step the wizard resumes where the user left off.
        $this->actingAs($this->user)->get("/dashboard/websites/{$this->company->id}/wizard")->assertOk();
    }

    public function test_user_can_update_profile(): void
    {
        $this->actingAs($this->user)->put('/dashboard/settings/profile', [
            'name' => 'Nama Profil Baru', 'email' => 'profil-baru@example.test', 'phone' => '0812',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame('Nama Profil Baru', $this->user->fresh()->name);
        $this->assertSame('profil-baru@example.test', $this->user->fresh()->email);

        $other = User::factory()->create();
        $this->actingAs($this->user)->put('/dashboard/settings/profile', ['name' => 'X', 'email' => $other->email])
            ->assertSessionHasErrors('email');
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $this->actingAs($this->user)->put('/dashboard/settings/password', [
            'current_password' => 'wrong', 'password' => 'Baru-12345', 'password_confirmation' => 'Baru-12345',
        ])->assertSessionHasErrorsIn('password', 'current_password');
        $this->assertTrue(Hash::check('password', $this->user->fresh()->password));

        $this->actingAs($this->user)->put('/dashboard/settings/password', [
            'current_password' => 'password', 'password' => 'Baru-12345', 'password_confirmation' => 'Baru-12345',
        ])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('Baru-12345', $this->user->fresh()->password));
    }
}
