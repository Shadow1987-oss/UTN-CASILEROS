<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Locker;
use App\Models\Student;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function occupancy()
    {
        $data = $this->buildOccupancyData();

        return view('reports.occupancy', compact('data'));
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

    public function exportOccupancy()
    {
        $data = $this->buildOccupancyData();

        if (app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.occupancy_pdf', compact('data'));
            return $pdf->download('reporte_ocupacion.pdf');
        }

        return response()->view('reports.occupancy_pdf', compact('data'));
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

    private function buildOccupancyData(): array
    {
        $totalLockers = Locker::count();
        $damaged = Locker::where('estado', 'dañado')->count();

        $occupied = Assignment::whereNull('released_at')
            ->distinct('idcasillero')
            ->count('idcasillero');

        $available = max(0, ($totalLockers - $damaged) - $occupied);

        return [
            'total_lockers' => $totalLockers,
            'available' => $available,
            'occupied' => $occupied,
            'damaged' => $damaged,
            'active_assignments' => Assignment::whereNull('released_at')->count(),
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
