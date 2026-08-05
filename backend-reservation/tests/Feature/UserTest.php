<?php

namespace Tests\Feature;

use App\Enums\RoleName;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\WithRoleUsers;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithRoleUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_create_a_technicien(): void
    {
        $this->actingAsRole(RoleName::ADMIN->value);
        $technicienRoleId = Role::where('nom', RoleName::TECHNICIEN->value)->firstOrFail()->id;

        $response = $this->postJson('/api/users', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.dupont@example.com',
            'role_id' => $technicienRoleId,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('users', ['email' => 'jean.dupont@example.com']);
    }

    public function test_admin_cannot_create_another_admin(): void
    {
        $this->actingAsRole(RoleName::ADMIN->value);
        $adminRoleId = Role::where('nom', RoleName::ADMIN->value)->firstOrFail()->id;

        $response = $this->postJson('/api/users', [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'jean.admin@example.com',
            'role_id' => $adminRoleId,
        ]);

        $response->assertStatus(403);
    }

    public function test_updating_a_user_logs_a_precise_diff_description(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $target = $this->makeUserWithRole(RoleName::EMPLOYE->value, [
            'entreprise_id' => $admin->entreprise_id,
            'nom' => 'Ancien',
        ]);

        $response = $this->putJson("/api/users/{$target->id}", [
            'nom' => 'Nouveau',
            'prenom' => $target->prenom,
            'email' => $target->email,
            'role_id' => $target->role_id,
        ]);

        $response->assertOk();

        $log = ActivityLog::where('subject_id', $target->id)
            ->where('action', 'user.updated')
            ->firstOrFail();

        $this->assertStringContainsString('le nom de « Ancien » à « Nouveau »', $log->description);
    }

    public function test_deactivating_a_user_revokes_their_existing_tokens(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $target = $this->makeUserWithRole(RoleName::EMPLOYE->value, ['entreprise_id' => $admin->entreprise_id]);
        $target->createToken('session');

        $this->assertSame(1, $target->tokens()->count());

        $response = $this->patchJson("/api/users/{$target->id}/status", ['actif' => false]);

        $response->assertOk();
        $this->assertSame(0, $target->tokens()->count());
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);

        $response = $this->deleteJson("/api/users/{$admin->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_another_user(): void
    {
        $admin = $this->actingAsRole(RoleName::ADMIN->value);
        $target = $this->makeUserWithRole(RoleName::EMPLOYE->value, ['entreprise_id' => $admin->entreprise_id]);

        $response = $this->deleteJson("/api/users/{$target->id}");

        $response->assertOk();
        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }
}
