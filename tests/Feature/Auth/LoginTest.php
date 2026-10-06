<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_shows_demo_credentials_outside_production(): void
    {
        config(['platform.show_demo_credentials' => true]);

        $this->get('/login')
            ->assertOk()
            ->assertSee('admin@example.com')
            ->assertSee('user@example.com');
    }

    public function test_login_screen_hides_demo_credentials_when_disabled(): void
    {
        config(['platform.show_demo_credentials' => false]);

        $this->get('/login')
            ->assertOk()
            ->assertDontSee('admin@example.com')
            ->assertDontSee('user@example.com');
    }

    public function test_users_are_redirected_to_dashboard_after_login(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_admins_are_redirected_to_admin_panel_after_login(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->from('/login')
            ->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password'])
                ->assertSessionHasErrors('email');
        }

        // Even the correct password is rejected while locked out.
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertStringContainsString('Too many login attempts', session('errors')->first('email'));

        $this->assertGuest();
    }

    public function test_suspended_users_can_not_login(): void
    {
        $user = User::factory()->suspended()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/logout')->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_forgot_password_screen_can_be_rendered(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email])
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_requesting_reset_for_unknown_email_does_not_leak(): void
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => 'nobody@example.test'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get('/reset-password/'.$token.'?email='.urlencode($user->email))->assertOk();
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'Baru-Sekali-123',
                'password_confirmation' => 'Baru-Sekali-123',
            ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

            return true;
        });

        $this->assertTrue(Hash::check('Baru-Sekali-123', $user->fresh()->password));
    }

    public function test_password_reset_fails_with_invalid_token(): void
    {
        $user = User::factory()->create();

        $this->post('/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'Baru-Sekali-123',
            'password_confirmation' => 'Baru-Sekali-123',
        ])->assertSessionHasErrors('email');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
