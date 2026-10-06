<?php

namespace Tests\Feature\Auth;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Siti Aminah',
            'email' => 'siti@example.test',
            'password' => 'Rahasia-123',
            'password_confirmation' => 'Rahasia-123',
            'terms' => '1',
        ], $overrides);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/register')->assertOk()->assertSee('name="email"', false);
    }

    public function test_new_users_can_register_and_get_a_pro_trial(): void
    {
        $response = $this->post('/register', $this->payload());

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'siti@example.test')->firstOrFail();
        $this->assertSame(User::ROLE_USER, $user->role);
        $this->assertFalse($user->isAdmin());
        $this->assertAuthenticatedAs($user);

        $subscription = $user->subscription;
        $this->assertNotNull($subscription);
        $this->assertSame('pro', $subscription->plan);
        $this->assertSame('trialing', $subscription->status);
        $this->assertTrue($subscription->trial_ends_at->isFuture());
        $this->assertSame('pro', $user->planKey());
        $this->assertTrue($user->canUseCustomDomain());

        $this->assertTrue(ActivityLog::query()->where('action', 'auth.registered')->where('user_id', $user->id)->exists());
    }

    public function test_registration_cannot_escalate_role(): void
    {
        $this->post('/register', $this->payload(['role' => 'admin']))->assertRedirect(route('dashboard'));

        $this->assertSame(User::ROLE_USER, User::query()->where('email', 'siti@example.test')->value('role'));
    }

    public function test_terms_must_be_accepted(): void
    {
        $this->post('/register', $this->payload(['terms' => null]))->assertSessionHasErrors('terms');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'siti@example.test']);
    }

    public function test_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'siti@example.test']);

        $this->post('/register', $this->payload())->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertSame(1, User::query()->where('email', 'siti@example.test')->count());
    }

    public function test_password_must_be_confirmed(): void
    {
        $this->post('/register', $this->payload(['password_confirmation' => 'different-123']))
            ->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_registration_can_be_disabled_from_settings(): void
    {
        app(SettingService::class)->set(['registration_enabled' => '0']);

        $this->get('/register')->assertNotFound();
        $this->post('/register', $this->payload())->assertNotFound();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'siti@example.test']);
    }

    public function test_authenticated_users_are_redirected_away_from_register(): void
    {
        $this->actingAs(User::factory()->create())->get('/register')->assertRedirect(route('dashboard'));
    }
}
