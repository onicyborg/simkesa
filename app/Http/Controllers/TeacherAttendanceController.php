<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    public function indexClasses()
    {
        $user = Auth::user();
        $classes = $user->homeroomClasses()
            ->withCount('students')
            ->with(['batch'])
            ->orderByDesc('created_at')
            ->get();

        return view('teacher.attendances.classes', compact('classes'));
    }

    public function showCalendar($classId)
    {
        $user = Auth::user();
        $schoolClass = $user->homeroomClasses()->with('batch')->findOrFail($classId);
        return view('teacher.attendances.calendar', compact('schoolClass'));
    }

    public function calendarStatus(Request $request, $classId)
    {
        $user = Auth::user();
        $schoolClass = $user->homeroomClasses()->findOrFail($classId);

        $month = (int)($request->get('month', now()->month));
        $year = (int)($request->get('year', now()->year));
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = (clone $start)->endOfMonth();
        $today = now()->toDateString();

        $studentIds = Student::where('class_id', $schoolClass->id)->pluck('id');
        $totalStudents = $studentIds->count();

        $attendanceCounts = Attendance::select('attendance_date', DB::raw('COUNT(*) as cnt'))
            ->whereIn('student_id', $studentIds)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->groupBy('attendance_date')
            ->pluck('cnt', 'attendance_date');

        $status = [];
        for ($d = (clone $start); $d->lte($end); $d->addDay()) {
            $date = $d->toDateString();
            if ($date > $today) {
                $status[$date] = 'disabled';
                continue;
            }
            $count = (int)($attendanceCounts[$date] ?? 0);
            if ($count === 0) {
                $status[$date] = 'red';
            } elseif ($count < $totalStudents) {
                $status[$date] = 'yellow';
            } else {
                $status[$date] = 'green';
            }
        }

        return response()->json([
            'status' => $status,
            'total_students' => $totalStudents,
        ]);
    }

    public function showByDate($classId, $date)
    {
        $user = Auth::user();
        $schoolClass = $user->homeroomClasses()->with(['batch'])->findOrFail($classId);

        $date = Carbon::parse($date)->toDateString();

        $students = Student::with(['user'])
            ->where('class_id', $schoolClass->id)
            ->orderBy('full_name')
            ->get();

        $attendances = Attendance::whereIn('student_id', $students->pluck('id'))
            ->whereDate('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        return view('teacher.attendances.date', compact('schoolClass', 'date', 'students', 'attendances'));
    }

    public function storeOrUpdate(Request $request, $classId, $date)
    {
        $user = Auth::user();
        $schoolClass = $user->homeroomClasses()->findOrFail($classId);
        $date = Carbon::parse($date)->toDateString();

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|string|max:2',
            'remark' => 'nullable|string',
        ]);

        $studentId = $request->student_id;
        $belongs = Student::where('id', $studentId)->where('class_id', $schoolClass->id)->exists();
        abort_unless($belongs, 403);

        $attendance = Attendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'attendance_date' => $date,
            ],
            [
                'status' => $validated['status'],
                'remark' => $validated['remark'] ?? null,
                'recorded_by' => $user->id,
            ]
        );

        return back()->with('success', 'Kehadiran disimpan.');
    }
}
