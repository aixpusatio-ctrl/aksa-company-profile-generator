<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public static function protectedGetRoutes(): array
    {
        return [
            'dashboard' => ['/dashboard'],
            'websites index' => ['/dashboard/websites'],
            'website create' => ['/dashboard/websites/create'],
            'settings' => ['/dashboard/settings'],
            'media' => ['/dashboard/media'],
            'admin dashboard' => ['/admin'],
            'admin users' => ['/admin/users'],
            'admin settings' => ['/admin/settings'],
        ];
    }

    public static function adminRoutes(): array
    {
        return [
            'dashboard' => ['get', '/admin'],
            'users' => ['get', '/admin/users'],
            'user create' => ['get', '/admin/users/create'],
            'companies' => ['get', '/admin/companies'],
            'templates' => ['get', '/admin/templates'],
            'template store' => ['post', '/admin/templates'],
            'categories' => ['get', '/admin/categories'],
            'pages' => ['get', '/admin/pages'],
            'domains' => ['get', '/admin/domains'],
            'subscriptions' => ['get', '/admin/subscriptions'],
            'media' => ['get', '/admin/media'],
            'settings' => ['get', '/admin/settings'],
            'settings update' => ['put', '/admin/settings'],
            'logs' => ['get', '/admin/logs'],
        ];
    }

    #[DataProvider('protectedGetRoutes')]
    public function test_guests_are_redirected_to_login(string $uri): void
    {
        $this->get($uri)->assertRedirect(route('login'));
    }

    public function test_guests_cannot_post_to_dashboard_endpoints(): void
    {
        $this->post('/dashboard/websites', ['name' => 'X'])->assertRedirect(route('login'));
        $this->postJson('/dashboard/websites', ['name' => 'X'])->assertUnauthorized();
    }

    #[DataProvider('adminRoutes')]
    public function test_regular_users_get_403_on_admin_routes(string $method, string $uri): void
    {
        $this->actingAs(User::factory()->create())->{$method}($uri)->assertForbidden();
    }

    public function test_regular_users_cannot_run_admin_actions_on_other_users(): void
    {
        $user = User::factory()->create();
        $victim = User::factory()->create();

        $this->actingAs($user)->post("/admin/users/{$victim->id}/suspend")->assertForbidden();
        $this->actingAs($user)->delete("/admin/users/{$victim->id}")->assertForbidden();
        $this->actingAs($user)->put("/admin/subscriptions/{$user->id}", ['plan' => 'business', 'status' => 'active'])->assertForbidden();

        $this->assertNull($victim->fresh()->suspended_at);
        $this->assertDatabaseHas('users', ['id' => $victim->id]);
        $this->assertSame(0, $user->subscriptions()->count());
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->actingAs($this->admin())->get('/admin')->assertOk();
    }

    public function test_admin_can_access_user_dashboard(): void
    {
        $this->actingAs($this->admin())->get('/dashboard')->assertOk();
    }

    public function test_user_can_access_own_dashboard(): void
    {
        $this->actingAs($this->userWithPlan())->get('/dashboard')->assertOk();
    }

    public function test_suspended_user_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->forceFill(['suspended_at' => now()])->save();

        $this->get('/dashboard')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_suspended_admin_loses_admin_access(): void
    {
        $admin = User::factory()->admin()->suspended()->create();

        $this->actingAs($admin)->get('/admin')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_central_routes_are_not_available_on_tenant_hosts(): void
    {
        $user = User::factory()->create();

        // Unknown tenant host: tenant routes answer with "not found", login never shows up.
        $this->get('http://unknown-company.localhost/login')->assertNotFound();
        $this->actingAs($user)->get('http://unknown-company.localhost/dashboard')->assertNotFound();
    }
}
