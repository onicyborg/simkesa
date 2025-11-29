<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportExport;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $classId = $request->query('class_id');
        $status = $request->query('status');

        $base = Attendance::query();

        if (!empty($dateFrom)) {
            $base->whereDate('attendance_date', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $base->whereDate('attendance_date', '<=', $dateTo);
        }
        if (!empty($classId)) {
            $base->whereHas('student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        if (!empty($status)) {
            $base->where('status', $status);
        }

        // Summary stats
        $summaryCounts = (clone $base)
            ->reorder()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');
        $totalRecords = (clone $base)->reorder()->count();
        $totalHadir = (int) ($summaryCounts['H'] ?? 0);
        $summary = [
            'total' => $totalRecords,
            'H' => $totalHadir,
            'S' => (int) ($summaryCounts['S'] ?? 0),
            'I' => (int) ($summaryCounts['I'] ?? 0),
            'A' => (int) ($summaryCounts['A'] ?? 0),
            'percent' => $totalRecords > 0 ? round($totalHadir / $totalRecords * 100, 2) : 0,
        ];

        // Aggregation per student
        $aggregates = (clone $base)
            ->reorder()
            ->select([
                'student_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alpha"),
            ])
            ->groupBy('student_id')
            ->get();

        $studentIds = $aggregates->pluck('student_id')->filter()->unique()->values();
        $students = Student::with(['schoolClass.batch','schoolClass.homeroomTeacher'])
            ->whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        $rows = $aggregates->map(function ($agg) use ($students) {
            $student = $students->get($agg->student_id);
            $kelas = '-';
            if ($student && $student->schoolClass) {
                $kelas = ($student->schoolClass->batch->year ?? '-') . ' - ' . ($student->schoolClass->name ?? '-');
            }
            $percent = $agg->total > 0 ? round(($agg->hadir / $agg->total) * 100, 2) : 0;
            return [
                'student_name' => $student->full_name ?? '-',
                'class_label' => $kelas,
                'hadir' => (int) $agg->hadir,
                'sakit' => (int) $agg->sakit,
                'izin' => (int) $agg->izin,
                'alpha' => (int) $agg->alpha,
                'total' => (int) $agg->total,
                'percent' => $percent,
            ];
        });
        $classes = SchoolClass::with('batch')->orderBy('name')->get();

        return view('admin.reports.attendance', [
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'class_id' => $classId,
                'status' => $status,
            ],
            'classes' => $classes,
            'summary' => $summary,
            'rows' => $rows,
        ]);
    }

    public function exportExcel(Request $request)
    {
        // Reuse the same filter logic
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $classId = $request->query('class_id');
        $status = $request->query('status');

        $base = Attendance::query();
        if (!empty($dateFrom)) $base->whereDate('attendance_date', '>=', $dateFrom);
        if (!empty($dateTo)) $base->whereDate('attendance_date', '<=', $dateTo);
        if (!empty($classId)) {
            $base->whereHas('student', fn($q)=>$q->where('class_id',$classId));
        }
        if (!empty($status)) $base->where('status', $status);

        $aggregates = (clone $base)
            ->reorder()
            ->select([
                'student_id',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'H' THEN 1 ELSE 0 END) as hadir"),
                DB::raw("SUM(CASE WHEN status = 'S' THEN 1 ELSE 0 END) as sakit"),
                DB::raw("SUM(CASE WHEN status = 'I' THEN 1 ELSE 0 END) as izin"),
                DB::raw("SUM(CASE WHEN status = 'A' THEN 1 ELSE 0 END) as alpha"),
            ])
            ->groupBy('student_id')
            ->get();

        $studentIds = $aggregates->pluck('student_id')->filter()->unique()->values();
        $students = Student::with(['schoolClass.batch','schoolClass.homeroomTeacher'])
            ->whereIn('id', $studentIds)
            ->get()
            ->keyBy('id');

        // Build rows for export (with Wali Kelas)
        $rows = [];
        foreach ($aggregates as $agg) {
            $student = $students->get($agg->student_id);
            $kelas = '-';
            if ($student && $student->schoolClass) {
                $kelas = ($student->schoolClass->batch->year ?? '-') . ' - ' . ($student->schoolClass->name ?? '-');
            }
            $wali = '-';
            if ($student && $student->schoolClass && $student->schoolClass->homeroomTeacher) {
                $wali = $student->schoolClass->homeroomTeacher->name ?? '-';
            }
            $percent = $agg->total > 0 ? round(($agg->hadir / $agg->total) * 100, 2) : 0;
            $rows[] = [
                'student_name' => $student->full_name ?? '-',
                'class_label' => $kelas,
                'homeroom'    => $wali,
                'hadir'       => (int) $agg->hadir,
                'sakit'       => (int) $agg->sakit,
                'izin'        => (int) $agg->izin,
                'alpha'       => (int) $agg->alpha,
                'total'       => (int) $agg->total,
                'percent'     => $percent,
            ];
        }

        $filename = 'laporan_kehadiran_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new AttendanceReportExport($rows), $filename);
    }

    public function exportPdf(Request $request)
    {
        return response('Export PDF belum diimplementasikan', 501);
    }
}
