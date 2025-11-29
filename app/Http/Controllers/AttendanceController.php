<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance history for admin with filters.
     */
    public function index(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $classId = $request->query('class_id');
        $status = $request->query('status');
        $recorderId = $request->query('recorder_id');

        // Default to today if no date filter provided
        if (empty($dateFrom) && empty($dateTo)) {
            $dateFrom = now()->toDateString();
            $dateTo = now()->toDateString();
        }

        $base = Attendance::with(['student.schoolClass.batch', 'recorder'])
            ->orderByDesc('attendance_date');

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
        if (!empty($recorderId)) {
            $base->where('recorded_by', $recorderId);
        }

        $attendances = $base->get(); // Client-side DataTables

        // Summary counts by status
        $summary = (clone $base)
            ->reorder() // remove orderBy(attendance_date) from base query
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $classes = SchoolClass::with('batch')->orderBy('name')->get();
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();

        return view('attendances.index', [
            'attendances' => $attendances,
            'classes' => $classes,
            'teachers' => $teachers,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'class_id' => $classId,
                'status' => $status,
                'recorder_id' => $recorderId,
            ],
            'summary' => $summary,
        ]);
    }
}
