<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceReportExport;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TeacherReportController extends Controller
{
    public function attendance(Request $request)
    {
        $user = Auth::user();
        $classes = $user->homeroomClasses()->with('batch')->orderBy('name')->get();
        $allowedClassIds = $classes->pluck('id')->all();

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $classId = $request->query('class_id');
        $status = $request->query('status');

        if (!empty($classId) && !in_array($classId, $allowedClassIds, true)) {
            $classId = null; // prevent access to other classes
        }

        $studentIdsQuery = Student::query()->whereIn('class_id', $allowedClassIds);
        if (!empty($classId)) {
            $studentIdsQuery->where('class_id', $classId);
        }
        $studentIds = $studentIdsQuery->pluck('id');

        $base = Attendance::with(['student.schoolClass.batch'])
            ->whereIn('student_id', $studentIds);

        if (!empty($dateFrom)) {
            $base->whereDate('attendance_date', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $base->whereDate('attendance_date', '<=', $dateTo);
        }
        if (!empty($status)) {
            $base->where('status', $status);
        }

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

        $studentIdsMap = $aggregates->pluck('student_id')->filter()->unique()->values();
        $students = Student::with(['schoolClass.batch'])
            ->whereIn('id', $studentIdsMap)
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

        return view('teacher.reports.attendance', [
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
        $user = Auth::user();
        $classes = $user->homeroomClasses()->with('batch')->get();
        $allowedClassIds = $classes->pluck('id')->all();

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $classId = $request->query('class_id');
        $status = $request->query('status');

        if (!empty($classId) && !in_array($classId, $allowedClassIds, true)) {
            $classId = null;
        }

        $studentIdsQuery = Student::query()->whereIn('class_id', $allowedClassIds);
        if (!empty($classId)) {
            $studentIdsQuery->where('class_id', $classId);
        }
        $studentIds = $studentIdsQuery->pluck('id');

        $base = Attendance::with(['student.schoolClass.batch'])
            ->whereIn('student_id', $studentIds);
        if (!empty($dateFrom)) $base->whereDate('attendance_date', '>=', $dateFrom);
        if (!empty($dateTo)) $base->whereDate('attendance_date', '<=', $dateTo);
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

        $studentIdsMap = $aggregates->pluck('student_id')->filter()->unique()->values();
        $students = Student::with(['schoolClass.batch','schoolClass.homeroomTeacher'])
            ->whereIn('id', $studentIdsMap)
            ->get()
            ->keyBy('id');

        $rows = [];
        foreach ($aggregates as $agg) {
            $student = $students->get($agg->student_id);
            $kelas = '-';
            if ($student && $student->schoolClass) {
                $kelas = ($student->schoolClass->batch->year ?? '-') . ' - ' . ($student->schoolClass->name ?? '-');
            }
            $percent = $agg->total > 0 ? round(($agg->hadir / $agg->total) * 100, 2) : 0;
            $rows[] = [
                'student_name' => $student->full_name ?? '-',
                'class_label' => $kelas,
                'homeroom'    => $student->schoolClass->homeroomTeacher->name ?? '-',
                'hadir'       => (int) $agg->hadir,
                'sakit'       => (int) $agg->sakit,
                'izin'        => (int) $agg->izin,
                'alpha'       => (int) $agg->alpha,
                'total'       => (int) $agg->total,
                'percent'     => $percent,
            ];
        }

        $filename = 'laporan_kehadiran_guru_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new AttendanceReportExport($rows), $filename);
    }
}
