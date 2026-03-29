<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Building;
use App\Models\Locker;
use App\Models\Period;
use App\Models\Student;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function occupancy(Request $request)
    {
        $filters = [
            'idedificio' => $request->input('idedificio'),
            'area' => $request->input('area'),
            'planta' => $request->input('planta'),
            'idperiodo' => $request->input('idperiodo'),
        ];

        $data = $this->buildOccupancyData($filters);
        $buildings = Building::orderBy('num_edific')->get();
        $periods = Period::orderBy('idperiodo', 'desc')->get();
        $areas = Locker::whereNotNull('area')->distinct()->orderBy('area')->pluck('area');

        return view('reports.occupancy', compact('data', 'buildings', 'periods', 'areas', 'filters'));
    }

    public function byGroup(Request $request)
    {
        $careers = \App\Models\Career::all();
        $groups = Student::whereNotNull('grupo')->distinct()->orderBy('grupo')->pluck('grupo');
        $buildings = \App\Models\Building::orderBy('num_edific')->get();

        $filters = [
            'idcarrera' => $request->input('idcarrera'),
            'cuatrimestre' => $request->input('cuatrimestre'),
            'grupo' => $request->input('grupo'),
            'idedificio' => $request->input('idedificio'),
        ];

        $data = $this->buildByGroupData($filters);

        return view('reports.by_group', compact('data', 'careers', 'groups', 'buildings', 'filters'));
    }

    public function exportOccupancy(Request $request)
    {
        $filters = [
            'idedificio' => $request->input('idedificio'),
            'area' => $request->input('area'),
            'planta' => $request->input('planta'),
            'idperiodo' => $request->input('idperiodo'),
        ];

        $data = $this->buildOccupancyData($filters);

        $pdfFilters = [
            'edificio' => !empty($filters['idedificio'])
                ? ('Edif. ' . optional(Building::find($filters['idedificio']))->num_edific)
                : 'Todos',
            'area' => $filters['area'] ?: 'Todas',
            'planta' => $filters['planta'] ?: 'Todas',
            'periodo' => !empty($filters['idperiodo'])
                ? (optional(Period::find($filters['idperiodo']))->nombrePerio ?: 'Todos')
                : 'Todos',
        ];

        if (app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.occupancy_pdf', compact('data', 'pdfFilters'));
            return $pdf->download('reporte_ocupacion.pdf');
        }

        return response()->view('reports.occupancy_pdf', compact('data', 'pdfFilters'));
    }

    public function exportByGroup(Request $request)
    {
        $filters = [
            'idcarrera' => $request->input('idcarrera'),
            'cuatrimestre' => $request->input('cuatrimestre'),
            'grupo' => $request->input('grupo'),
            'idedificio' => $request->input('idedificio'),
        ];

        $data = $this->buildByGroupData($filters);

        $careerName = null;
        if (!empty($filters['idcarrera'])) {
            $careerName = optional(\App\Models\Career::find($filters['idcarrera']))->nombre_carre;
        }

        $pdfFilters = [
            'carrera' => $careerName ?: 'Todas',
            'cuatrimestre' => $filters['cuatrimestre'] ?: 'Todos',
            'grupo' => $filters['grupo'] ?: 'Todos',
            'edificio' => !empty($filters['idedificio'])
                ? ('Edif. ' . optional(\App\Models\Building::find($filters['idedificio']))->num_edific)
                : 'Todos',
        ];

        if (app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.by_group_pdf', compact('data', 'pdfFilters'));
            return $pdf->download('reporte_por_grupo.pdf');
        }

        return response()->view('reports.by_group_pdf', compact('data', 'pdfFilters'));
    }

    private function buildOccupancyData(array $filters = []): array
    {
        $lockerQuery = Locker::query();

        if (!empty($filters['idedificio'])) {
            $lockerQuery->where('idedificio', (int) $filters['idedificio']);
        }
        if (!empty($filters['area'])) {
            $lockerQuery->where('area', (string) $filters['area']);
        }
        if (!empty($filters['planta'])) {
            $lockerQuery->where('planta', (string) $filters['planta']);
        }

        $lockers = (clone $lockerQuery)->with('building')->get();
        $lockerIds = $lockers->pluck('idcasillero')->map(fn($id) => (int) $id)->all();

        $assignmentBase = Assignment::query();
        if (!empty($filters['idperiodo'])) {
            $assignmentBase->where('idPeriodo', (int) $filters['idperiodo']);
        }
        if (!empty($lockerIds)) {
            $assignmentBase->whereIn('idcasillero', $lockerIds);
        } else {
            $assignmentBase->whereRaw('1 = 0');
        }

        $activeAssignmentsQuery = (clone $assignmentBase)->whereNull('released_at');

        $totalLockers = $lockers->count();
        $damaged = $lockers->where('estado', 'dañado')->count();

        $occupied = (clone $activeAssignmentsQuery)
            ->distinct('idcasillero')
            ->count('idcasillero');

        $available = max(0, ($totalLockers - $damaged) - $occupied);

        $averageOccupancyDays = (float) ((clone $assignmentBase)
            ->selectRaw('AVG(DATEDIFF(COALESCE(released_at, NOW()), fechaAsignacion)) AS avg_days')
            ->value('avg_days') ?? 0);

        $occupiedLockerIds = (clone $activeAssignmentsQuery)->pluck('idcasillero')->unique();

        $byBuilding = $lockers->groupBy('idedificio')->map(function ($group, $buildingId) use ($occupiedLockerIds) {
            $buildingName = optional($group->first()->building)->num_edific ?? (string) $buildingId;
            $total = $group->count();
            $damaged = $group->where('estado', 'dañado')->count();
            $occupied = $group->filter(function ($locker) use ($occupiedLockerIds) {
                return $occupiedLockerIds->contains($locker->idcasillero);
            })->count();

            return [
                'building' => $buildingName,
                'total' => $total,
                'damaged' => $damaged,
                'occupied' => $occupied,
                'available' => max(0, ($total - $damaged) - $occupied),
            ];
        })->sortBy('building')->values();

        return [
            'total_lockers' => $totalLockers,
            'available' => $available,
            'occupied' => $occupied,
            'damaged' => $damaged,
            'active_assignments' => (clone $activeAssignmentsQuery)->count(),
            'average_occupancy_days' => round($averageOccupancyDays, 2),
            'by_building' => $byBuilding,
        ];
    }

    private function buildByGroupData(array $filters = [])
    {
        $query = Student::with(['assignments', 'career']);

        if (!empty($filters['idcarrera'])) {
            $query->where('idcarrera', (int) $filters['idcarrera']);
        }

        if (!empty($filters['cuatrimestre'])) {
            $query->where('cuatrimestre', (int) $filters['cuatrimestre']);
        }

        if (!empty($filters['grupo'])) {
            $query->where('grupo', (string) $filters['grupo']);
        }

        if (!empty($filters['idedificio'])) {
            $buildingId = (int) $filters['idedificio'];
            $query->whereHas('assignments', function ($assignmentQuery) use ($buildingId) {
                $assignmentQuery->whereNull('released_at')
                    ->whereHas('locker', function ($lockerQuery) use ($buildingId) {
                        $lockerQuery->where('idedificio', $buildingId);
                    });
            });
        }

        $students = $query->get();

        return $students->groupBy('idcarrera')->map(function ($group) {
            return [
                'career' => $group->first()->career->nombre_carre ?? (string) $group->first()->idcarrera,
                'total_students' => $group->count(),
                'with_lockers' => $group->filter(function ($student) {
                    return $student->assignments->whereNull('released_at')->isNotEmpty();
                })->count(),
                'without_lockers' => $group->filter(function ($student) {
                    return $student->assignments->whereNull('released_at')->isEmpty();
                })->count(),
            ];
        })->sortBy('career')->values();
    }
}
