<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceHistoryController extends Controller
{
    /**
     * Display teacher-specific attendance history with filters.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $classId = $request->query('class_id');
        $status = $request->query('status');

        // Default to today if no date filter provided
        if (empty($dateFrom) && empty($dateTo)) {
            $dateFrom = now()->toDateString();
            $dateTo = now()->toDateString();
        }

        // Classes taught by the logged-in teacher (homeroom)
        $classes = $user->homeroomClasses()->with('batch')->orderBy('name')->get();
        $allowedClassIds = $classes->pluck('id')->all();

        // Student IDs in teacher's classes
        $studentIds = \App\Models\Student::whereIn('class_id', $allowedClassIds)->pluck('id');

        $base = Attendance::with(['student.schoolClass.batch'])
            ->whereIn('student_id', $studentIds)
            ->orderByDesc('attendance_date');

        if (!empty($dateFrom)) {
            $base->whereDate('attendance_date', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $base->whereDate('attendance_date', '<=', $dateTo);
        }
        if (!empty($classId)) {
            // Ensure class filter is within teacher's classes
            if (in_array($classId, $allowedClassIds)) {
                $base->whereHas('student', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                });
            } else {
                // If invalid class provided, return empty by forcing impossible condition
                $base->whereRaw('1 = 0');
            }
        }
        if (!empty($status)) {
            $base->where('status', $status);
        }

        $attendances = $base->get();

        // Summary counts by status
        $summary = (clone $base)
            ->reorder()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('teacher.attendances.index', [
            'attendances' => $attendances,
            'classes' => $classes,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'class_id' => $classId,
                'status' => $status,
            ],
            'summary' => $summary,
        ]);
    }
}
