<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\CompanyPage;
use App\Models\CompanyProfile;
use App\Models\Domain;
use App\Models\Media;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\DomainVerifiedNotification;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = $this->admin(['email' => 'boss@example.test']);
    }

    // ------------------------------------------------------------------ Pages render

    public function test_admin_dashboard_shows_platform_stats(): void
    {
        $this->companyFor(null, ['name' => 'PT Statistik'], published: true);

        $this->actingAs($this->admin)->get('/admin')->assertOk();
    }

    public function test_admin_index_pages_render(): void
    {
        $company = $this->companyFor(null, ['name' => 'PT Daftar Admin']);
        $company->pages()->create(['title' => 'Halaman Admin', 'slug' => 'halaman-admin', 'status' => 'draft']);
        $company->domains()->create(['domain' => 'www.admin-list.test', 'type' => 'subdomain', 'verification_token' => 't', 'status' => 'pending']);

        foreach (['/admin/users', '/admin/companies', '/admin/templates', '/admin/categories', '/admin/pages', '/admin/domains', '/admin/subscriptions', '/admin/media', '/admin/settings', '/admin/logs'] as $uri) {
            $this->actingAs($this->admin)->get($uri)->assertOk();
        }

        $this->actingAs($this->admin)->get('/admin/companies')->assertSee('PT Daftar Admin');
        $this->actingAs($this->admin)->get('/admin/companies/'.$company->id)->assertOk()->assertSee('PT Daftar Admin');
        $this->actingAs($this->admin)->get('/admin/domains')->assertSee('www.admin-list.test');
    }

    // ------------------------------------------------------------------ Users

    public function test_admin_can_create_a_user(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => 'Pengguna Baru',
            'email' => 'baru@example.test',
            'role' => 'user',
            'phone' => '0812',
            'password' => 'Rahasia-123',
            'password_confirmation' => 'Rahasia-123',
        ])->assertSessionHasNoErrors();

        $user = User::query()->where('email', 'baru@example.test')->firstOrFail();
        $this->assertSame('user', $user->role);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('Rahasia-123', $user->password));
        $this->assertTrue(ActivityLog::query()->where('action', 'admin.user_created')->exists());
    }

    public function test_user_creation_validation(): void
    {
        $this->actingAs($this->admin)->post('/admin/users', [
            'name' => '', 'email' => 'boss@example.test', 'role' => 'superadmin', 'password' => 'x',
        ])->assertSessionHasErrors(['name', 'email', 'role', 'password']);
    }

    public function test_admin_can_view_and_edit_a_user(): void
    {
        $user = User::factory()->create(['name' => 'Lihat Saya']);

        $this->actingAs($this->admin)->get('/admin/users/'.$user->id)->assertOk()->assertSee('Lihat Saya');
        $this->actingAs($this->admin)->get('/admin/users/'.$user->id.'/edit')->assertOk();
        $this->actingAs($this->admin)->get('/admin/users/create')->assertOk();
    }

    public function test_admin_can_update_a_user_without_changing_password(): void
    {
        $user = User::factory()->create();
        $hash = $user->password;

        $this->actingAs($this->admin)->put('/admin/users/'.$user->id, [
            'name' => 'Nama Diubah', 'email' => 'diubah@example.test', 'role' => 'admin', 'password' => '',
        ])->assertRedirect(route('admin.users.show', $user))->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Nama Diubah', $user->name);
        $this->assertSame('diubah@example.test', $user->email);
        $this->assertTrue($user->isAdmin());
        $this->assertSame($hash, $user->password);
    }

    public function test_admin_cannot_demote_self(): void
    {
        $this->actingAs($this->admin)->put('/admin/users/'.$this->admin->id, [
            'name' => 'Boss', 'email' => 'boss@example.test', 'role' => 'user',
        ])->assertSessionHasErrors('role');

        $this->assertTrue($this->admin->fresh()->isAdmin());
    }

    public function test_admin_can_delete_a_user_and_their_websites(): void
    {
        $user = User::factory()->create();
        $company = $this->companyFor($user);

        $this->actingAs($this->admin)->delete('/admin/users/'.$user->id)->assertRedirect(route('admin.users.index'));

        $this->assertModelMissing($user);
        $this->assertModelMissing($company);
        $this->assertTrue(ActivityLog::query()->where('action', 'admin.user_deleted')->exists());
    }

    public function test_admin_cannot_delete_self(): void
    {
        $this->actingAs($this->admin)->delete('/admin/users/'.$this->admin->id)->assertStatus(422);

        $this->assertModelExists($this->admin);
    }

    public function test_admin_can_suspend_and_unsuspend_users(): void
    {
        $user = User::factory()->create();
        $company = $this->companyFor($user, [], published: true);

        $this->actingAs($this->admin)->post('/admin/users/'.$user->id.'/suspend')->assertRedirect();
        $this->assertTrue($user->fresh()->isSuspended());
        $this->get($this->tenantUrl($company))->assertNotFound();

        // Suspended user cannot log in.
        $this->post($this->centralUrl('/logout'));
        $this->post($this->centralUrl('/login'), ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->actingAs($this->admin)->post($this->centralUrl('/admin/users/'.$user->id.'/unsuspend'))->assertRedirect();
        $this->assertFalse($user->fresh()->isSuspended());
        $this->get($this->tenantUrl($company))->assertOk();

        $this->assertTrue(ActivityLog::query()->where('action', 'admin.user_suspended')->exists());
        $this->assertTrue(ActivityLog::query()->where('action', 'admin.user_unsuspended')->exists());
    }

    public function test_admin_cannot_suspend_self(): void
    {
        $this->actingAs($this->admin)->post('/admin/users/'.$this->admin->id.'/suspend')->assertStatus(422);

        $this->assertFalse($this->admin->fresh()->isSuspended());
    }

    public function test_admin_can_reset_a_users_password(): void
    {
        $user = User::factory()->create();
        $oldHash = $user->password;

        $response = $this->actingAs($this->admin)->post('/admin/users/'.$user->id.'/reset-password')
            ->assertRedirect()
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertNotSame($oldHash, $user->password);
        $this->assertFalse(Hash::check('password', $user->password));

        preg_match('/: ([A-Za-z0-9]{12}) /', session('success'), $matches);
        $this->assertNotEmpty($matches, 'Flash message contains the generated password.');
        $this->assertTrue(Hash::check($matches[1], $user->password));
    }

    public function test_users_index_filters(): void
    {
        User::factory()->create(['name' => 'Ditangguhkan Orang', 'suspended_at' => now()]);
        User::factory()->create(['name' => 'Aktif Orang']);

        $this->actingAs($this->admin)->get('/admin/users?status=suspended')
            ->assertOk()->assertSee('Ditangguhkan Orang')->assertDontSee('Aktif Orang');
        $this->actingAs($this->admin)->get('/admin/users?q=Aktif')
            ->assertOk()->assertSee('Aktif Orang')->assertDontSee('Ditangguhkan Orang');
    }

    // ------------------------------------------------------------------ Companies

    public function test_admin_can_toggle_company_status(): void
    {
        $company = $this->companyFor();

        $this->actingAs($this->admin)->post('/admin/companies/'.$company->id.'/status')->assertRedirect();
        $this->assertTrue($company->fresh()->isPublished());
        $this->assertSame(1, $company->user->notifications()->count(), 'Owner is notified when admin publishes.');

        $this->actingAs($this->admin)->post('/admin/companies/'.$company->id.'/status')->assertRedirect();
        $this->assertSame(CompanyProfile::STATUS_DRAFT, $company->fresh()->status);
    }

    public function test_admin_can_delete_a_company(): void
    {
        $company = $this->companyFor();
        $company->domains()->create(['domain' => 'www.hapus-admin.test', 'type' => 'subdomain', 'verification_token' => 't', 'status' => 'active']);

        $this->actingAs($this->admin)->delete('/admin/companies/'.$company->id)->assertRedirect(route('admin.companies.index'));

        $this->assertModelMissing($company);
        $this->assertSame(0, Domain::query()->count());
        $this->get('http://www.hapus-admin.test/')->assertNotFound();
    }

    // ------------------------------------------------------------------ Pages & media moderation

    public function test_admin_can_moderate_custom_pages(): void
    {
        $page = $this->companyFor()->pages()->create(['title' => 'Moderasi', 'slug' => 'moderasi', 'status' => 'published']);

        $this->actingAs($this->admin)->post('/admin/pages/'.$page->id.'/status')->assertRedirect();
        $this->assertSame(CompanyPage::STATUS_DRAFT, $page->fresh()->status);

        $this->actingAs($this->admin)->delete('/admin/pages/'.$page->id)->assertRedirect();
        $this->assertModelMissing($page);
    }

    public function test_admin_can_delete_any_media(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('media/1/file.png', 'x');
        $media = Media::query()->create([
            'user_id' => User::factory()->create()->id, 'collection' => 'images', 'disk' => 'public',
            'path' => 'media/1/file.png', 'filename' => 'file.png', 'mime_type' => 'image/png', 'size' => 1,
        ]);

        $this->actingAs($this->admin)->delete('/admin/media/'.$media->id)->assertRedirect();

        $this->assertModelMissing($media);
        Storage::disk('public')->assertMissing('media/1/file.png');
    }

    // ------------------------------------------------------------------ Domains

    public function test_admin_can_activate_a_domain_manually(): void
    {
        $company = $this->companyFor(null, [], published: true);
        $domain = $company->domains()->create([
            'domain' => 'www.manual.test', 'type' => 'subdomain', 'verification_token' => 't',
            'status' => 'failed', 'failure_reason' => 'DNS',
        ]);

        $this->actingAs($this->admin)->post('/admin/domains/'.$domain->id.'/activate')->assertRedirect();

        $domain->refresh();
        $this->assertSame(Domain::STATUS_ACTIVE, $domain->status);
        $this->assertNull($domain->failure_reason);
        $this->assertNotNull($domain->verified_at);
        $this->assertSame(DomainVerifiedNotification::class, $company->user->notifications()->value('type'));
        $this->get('http://www.manual.test/')->assertOk();
    }

    public function test_admin_can_reverify_and_delete_a_domain(): void
    {
        $company = $this->companyFor();
        $domain = $company->domains()->create(['domain' => 'www.ulang.test', 'type' => 'subdomain', 'verification_token' => 't', 'status' => 'pending']);

        $this->actingAs($this->admin)->post('/admin/domains/'.$domain->id.'/verify')->assertRedirect();
        $this->assertSame(Domain::STATUS_ACTIVE, $domain->fresh()->status);

        $this->actingAs($this->admin)->delete('/admin/domains/'.$domain->id)->assertRedirect();
        $this->assertModelMissing($domain);
    }

    // ------------------------------------------------------------------ Subscriptions

    public function test_admin_can_update_a_subscription(): void
    {
        $user = $this->userWithPlan('free');
        $this->assertSame('free', $user->planKey());

        $this->actingAs($this->admin)->put('/admin/subscriptions/'.$user->id, ['plan' => 'business', 'status' => 'active'])
            ->assertRedirect()->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('business', $user->planKey());
        $this->assertNull($user->plan()['max_websites']);
        $this->assertEquals(config('platform.plans.business.price'), $user->subscription->price);
        $this->assertTrue(ActivityLog::query()->where('action', 'admin.subscription')->exists());

        $this->actingAs($this->admin)->put('/admin/subscriptions/'.$user->id, ['plan' => 'pro', 'status' => 'canceled']);
        $this->assertSame('free', $user->fresh()->planKey(), 'Canceled subscription falls back to free.');
    }

    public function test_subscription_with_past_end_date_is_not_running(): void
    {
        $user = $this->userWithPlan('free');

        $this->actingAs($this->admin)->put('/admin/subscriptions/'.$user->id, [
            'plan' => 'pro', 'status' => 'active', 'ends_at' => now()->subDay()->toDateString(),
        ])->assertSessionHasNoErrors();

        $this->assertSame('free', $user->fresh()->planKey());
    }

    public function test_subscription_update_validation(): void
    {
        $user = $this->userWithPlan('free');

        $this->actingAs($this->admin)->put('/admin/subscriptions/'.$user->id, ['plan' => 'platinum', 'status' => 'forever'])
            ->assertSessionHasErrors(['plan', 'status']);
    }

    // ------------------------------------------------------------------ Settings

    public function test_admin_can_update_settings(): void
    {
        $this->actingAs($this->admin)->put('/admin/settings', [
            'app_name' => 'Situs Profil Saya',
            'app_tagline' => 'Tagline baru',
            'support_email' => 'bantuan@example.test',
            'media_disk' => 'public',
            'max_upload_kb' => 2048,
            'registration_enabled' => '1',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame('Situs Profil Saya', Setting::query()->where('key', 'app_name')->value('value'));
        $this->assertSame('Situs Profil Saya', setting('app_name'));
        $this->assertSame('Situs Profil Saya', app_name());
        $this->assertSame('2048', (string) setting('max_upload_kb'));
        $this->assertSame('1', setting('registration_enabled'));

        // A fresh service instance reads the persisted values too.
        app()->forgetInstance(SettingService::class);
        $this->assertSame('Situs Profil Saya', app(SettingService::class)->get('app_name'));

        $this->assertTrue(ActivityLog::query()->where('action', 'admin.settings')->exists());
    }

    public function test_unchecking_registration_disables_registration(): void
    {
        $this->actingAs($this->admin)->put('/admin/settings', [
            'app_name' => 'X', 'media_disk' => 'public', 'max_upload_kb' => 4096,
        ])->assertSessionHasNoErrors();

        $this->assertSame('0', setting('registration_enabled'));

        $this->post('/logout');
        $this->get('/register')->assertNotFound();
    }

    public function test_settings_validation(): void
    {
        $this->actingAs($this->admin)->put('/admin/settings', [
            'app_name' => '', 'media_disk' => 'ftp-nowhere', 'max_upload_kb' => 99999, 'support_email' => 'x', 'default_template' => 'nope',
        ])->assertSessionHasErrors(['app_name', 'media_disk', 'max_upload_kb', 'support_email', 'default_template']);

        $this->assertSame(0, Setting::query()->count());
    }

    // ------------------------------------------------------------------ Activity logs

    public function test_activity_logs_page_lists_and_filters_actions(): void
    {
        $user = User::factory()->create();
        $this->actingAs($this->admin)->post('/admin/users/'.$user->id.'/suspend');

        $this->actingAs($this->admin)->get('/admin/logs')
            ->assertOk()
            ->assertSee("User {$user->email} ditangguhkan");

        $this->actingAs($this->admin)->get('/admin/logs?action=website')
            ->assertOk()
            ->assertDontSee("User {$user->email} ditangguhkan");
    }
}
