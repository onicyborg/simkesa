<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $students = Student::query()->count();
        $classes = SchoolClass::query()->count();
        $presentToday = Attendance::query()
            ->whereDate('attendance_date', $today)
            ->where('status', 'H')
            ->count();
        // Tidak hadir: status 'A' (Alpha). Jika ingin semua non-H, ganti ke where('status', '!=', 'H')
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

        return view('welcome', compact('dashboard', 'recentAttendances'));
    }
}
