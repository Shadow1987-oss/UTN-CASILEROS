<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoutesAuditTest extends TestCase
{
    public function test_critical_named_routes_exist(): void
    {
        $requiredRoutes = [
            'dashboard',
            'students.index',
            'students.import',
            'lockers.index',
            'periods.index',
            'assignments.index',
            'assignments.release',
            'reportes.index',
            'sanciones.index',
            'recibe.index',
            'reports.index',
            'reports.occupancy',
            'reports.by_group',
            'reports.occupancy.export',
            'reports.by_group.export',
            'student.request_locker',
            'locker_requests.index',
            'locker_requests.approve',
            'locker_requests.reject',
            'notifications.index',
            'notifications.read',
            'notifications.read_all',
        ];

        foreach ($requiredRoutes as $routeName) {
            $this->assertTrue(Route::has($routeName), "Missing route: {$routeName}");
        }
    }

    public function test_root_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/dashboard');
    }
}
