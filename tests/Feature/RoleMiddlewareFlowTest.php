<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleMiddlewareFlowTest extends TestCase
{
    public function test_guest_is_redirected_to_login_on_protected_route(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_admin_can_access_admin_and_admin_tutor_routes(): void
    {
        $admin = new User([
            'id' => 1001,
            'name' => 'Admin Test',
            'email' => 'admin.test@utnay.edu.mx',
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/periods')
            ->assertOk();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_tutor_cannot_access_admin_only_route_but_can_access_admin_tutor_route(): void
    {
        $tutor = new User([
            'id' => 1002,
            'name' => 'Tutor Test',
            'email' => 'tutor.test@utnay.edu.mx',
            'role' => 'tutor',
        ]);

        $this->actingAs($tutor)
            ->get('/periods')
            ->assertForbidden();

        $this->actingAs($tutor)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_student_only_accesses_student_route(): void
    {
        $student = new User([
            'id' => 1003,
            'name' => 'Student Test',
            'email' => 'student.test@utnay.edu.mx',
            'role' => 'estudiante',
        ]);

        $this->actingAs($student)
            ->get('/mi-casillero')
            ->assertOk();

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertForbidden();

        $this->actingAs($student)
            ->get('/notifications')
            ->assertOk();
    }
}
