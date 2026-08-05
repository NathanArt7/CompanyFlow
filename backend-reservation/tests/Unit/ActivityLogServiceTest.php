<?php

namespace Tests\Unit;

use App\Services\ActivityLogService;
use Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    public function test_describe_changes_ignores_identical_fields(): void
    {
        $service = new ActivityLogService();

        $fragments = $service->describeChanges(
            ['le nom' => 'Salle A', 'le statut' => 'Disponible'],
            ['le nom' => 'Salle A', 'le statut' => 'Disponible'],
        );

        $this->assertSame([], $fragments);
    }

    public function test_describe_changes_formats_a_fragment_per_changed_field(): void
    {
        $service = new ActivityLogService();

        $fragments = $service->describeChanges(
            ['le nom' => 'Salle A', 'le statut' => 'Disponible'],
            ['le nom' => 'Salle A', 'le statut' => 'En maintenance'],
        );

        $this->assertSame(['le statut de « Disponible » à « En maintenance »'], $fragments);
    }

    public function test_describe_changes_handles_multiple_changed_fields(): void
    {
        $service = new ActivityLogService();

        $fragments = $service->describeChanges(
            ['le nom' => 'Ancien', 'le rôle' => 'Employé'],
            ['le nom' => 'Nouveau', 'le rôle' => 'Super Employé'],
        );

        $this->assertCount(2, $fragments);
        $this->assertContains('le nom de « Ancien » à « Nouveau »', $fragments);
        $this->assertContains('le rôle de « Employé » à « Super Employé »', $fragments);
    }
}
