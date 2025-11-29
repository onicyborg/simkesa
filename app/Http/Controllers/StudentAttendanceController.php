<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::with(['schoolClass.batch','schoolClass.homeroomTeacher'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $month = (int)($request->get('month', now()->month));
        $year  = (int)($request->get('year', now()->year));
        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end   = (clone $start)->endOfMonth();

        $attendances = Attendance::with(['recorder'])
            ->where('student_id', $student->id)
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('attendance_date')
            ->get();

        $byDate = [];
        foreach ($attendances as $a) {
            $d = $a->attendance_date->toDateString();
            $byDate[$d] = [
                'status' => $a->status,
                'remark' => $a->remark,
                'recorder' => $a->recorder->name ?? null,
            ];
        }

        return view('student.attendances.index', [
            'student' => $student,
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'byDate' => $byDate,
            'today' => now()->toDateString(),
        ]);
    }
}
