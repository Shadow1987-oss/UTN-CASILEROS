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

        $data = $this->buildByGroupData($request->input('idcarrera'));

        return view('reports.by_group', compact('data', 'careers'));
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
        $data = $this->buildByGroupData($request->input('idcarrera'));

        if (app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports.by_group_pdf', compact('data'));
            return $pdf->download('reporte_por_grupo.pdf');
        }

        return response()->view('reports.by_group_pdf', compact('data'));
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

    private function buildByGroupData(?string $careerId = null)
    {
        $query = Student::with(['assignments', 'career']);

        if (!empty($careerId)) {
            $query->where('idcarrera', $careerId);
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
