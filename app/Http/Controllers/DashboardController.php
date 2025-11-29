<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $user = Auth::user();
        $role = $user->role ?? 'admin';

        if ($role === 'teacher') {
            // Teacher-specific dashboard: limit to homeroom classes
            $classIds = $user->homeroomClasses()->pluck('id');

            $studentsCount = Student::whereIn('class_id', $classIds)->count();
            $classesCount = $classIds->count();

            $studentIds = Student::whereIn('class_id', $classIds)->pluck('id');
            $presentToday = Attendance::whereDate('attendance_date', $today)
                ->where('status', 'H')
                ->whereIn('student_id', $studentIds)
                ->count();
            // Absent: any status other than 'H'
            $absentToday = Attendance::whereDate('attendance_date', $today)
                ->whereIn('student_id', $studentIds)
                ->where('status', '!=', 'H')
                ->count();

            $recentAttendances = Attendance::with(['student.schoolClass', 'recorder'])
                ->whereIn('student_id', $studentIds)
                ->latest('attendance_date')
                ->limit(15)
                ->get();

            $dashboard = [
                'total_classes' => $classesCount,
                'total_students' => $studentsCount,
                'present_today' => $presentToday,
                'absent_today' => $absentToday,
            ];

            return view('teacher.dashboard.index', compact('dashboard', 'recentAttendances'));
        }

        // Admin & others: global stats
        $students = Student::query()->count();
        $classes = SchoolClass::query()->count();
        $presentToday = Attendance::query()
            ->whereDate('attendance_date', $today)
            ->where('status', 'H')
            ->count();
        $absentToday = Attendance::query()
            ->whereDate('attendance_date', $today)
            ->where('status', 'A')
            ->count();

        $recentAttendances = Attendance::with(['student.schoolClass', 'recorder'])
            ->latest('attendance_date')
            ->limit(10)
            ->get();

        $dashboard = [
            'students' => $students,
            'classes' => $classes,
            'present_today' => $presentToday,
            'absent_today' => $absentToday,
        ];

        if ($role === 'admin') {
            return view('admin.dashboard.index', compact('dashboard', 'recentAttendances'));
        }
        if ($role === 'student') {
            return view('student.dashboard.index', compact('dashboard', 'recentAttendances'));
        }

        // Default fallback
        return view('admin.dashboard.index', compact('dashboard', 'recentAttendances'));
    }
}
