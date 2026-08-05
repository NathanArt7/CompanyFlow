<?php

namespace Tests\Feature\Auth;

use App\Enums\RoleName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_active_user_can_login_with_correct_credentials(): void
    {
        $user = $this->makeUserWithRole(RoleName::EMPLOYE->value, [
            'email' => 'login-test@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login-test@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'nom', 'prenom', 'email']]);

        $this->assertNotNull($user->fresh());
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $this->makeUserWithRole(RoleName::EMPLOYE->value, [
            'email' => 'wrong-pass@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'wrong-pass@example.com',
            'password' => 'nope',
        ]);

        $response->assertStatus(401);
    }

    public function test_deactivated_account_cannot_login(): void
    {
        $this->makeUserWithRole(RoleName::EMPLOYE->value, [
            'email' => 'inactive@example.com',
            'password' => bcrypt('Password123!'),
            'actif' => false,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'inactive@example.com',
            'password' => 'Password123!',
        ]);

        $response->assertStatus(403)
            ->assertJson(['code' => 'ACCOUNT_DISABLED']);

        $response->assertJsonMissingPath('token');
    }

    public function test_login_is_throttled_after_five_attempts(): void
    {
        $this->makeUserWithRole(RoleName::EMPLOYE->value, [
            'email' => 'throttle-test@example.com',
            'password' => bcrypt('Password123!'),
        ]);

        for ($i = 0; $i < 5; $i++) {

            $this->postJson('/api/login', [
                'email' => 'throttle-test@example.com',
                'password' => 'wrong',
            ])->assertStatus(401);

        }

        $this->postJson('/api/login', [
            'email' => 'throttle-test@example.com',
            'password' => 'wrong',
        ])->assertStatus(429);
    }

    public function test_authenticated_user_can_fetch_me(): void
    {
        $user = $this->actingAsRole(RoleName::ADMIN->value);

        $response = $this->getJson('/api/me');

        $response->assertOk()
            ->assertJson(['id' => $user->id, 'role' => RoleName::ADMIN->value]);
    }
}
