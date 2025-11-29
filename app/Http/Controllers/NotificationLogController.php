<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationLogController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $statusFilter = $request->query('status'); // 'success' | 'failed' | null
        $classId = $request->query('class_id');

        $base = NotificationLog::with(['attendance.student.schoolClass.batch'])
            ->orderByDesc('sent_at');

        if (!empty($dateFrom)) {
            $base->whereDate('sent_at', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $base->whereDate('sent_at', '<=', $dateTo);
        }
        if (!empty($statusFilter)) {
            $base->where('status', $statusFilter);
        }
        if (!empty($classId)) {
            $base->whereHas('attendance.student', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $logs = $base->get();

        // Summary
        $total = (clone $base)->reorder()->count();
        $success = (clone $base)->reorder()->where('status', 'success')->count();
        $failed = (clone $base)->reorder()->where('status', 'failed')->count();
        $summary = compact('total', 'success', 'failed');

        // Classes for filter
        $classes = SchoolClass::with('batch')->orderBy('name')->get();

        return view('notification_logs.index', [
            'logs' => $logs,
            'classes' => $classes,
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'status' => $statusFilter,
                'class_id' => $classId,
            ],
            'summary' => $summary,
        ]);
    }

    public function show(string $id)
    {
        $log = NotificationLog::with(['attendance.student.schoolClass.batch'])->findOrFail($id);
        return response()->json([
            'id' => $log->id,
            'sent_at' => optional($log->sent_at)->format('d M Y H:i'),
            'status' => $log->status,
            'recipient_email' => $log->recipient_email,
            'error_message' => $log->error_message,
            'student' => [
                'name' => optional($log->attendance->student)->full_name,
                'class' => ($log->attendance && $log->attendance->student && $log->attendance->student->schoolClass)
                    ? (($log->attendance->student->schoolClass->batch->year ?? '-') . ' - ' . ($log->attendance->student->schoolClass->name ?? '-'))
                    : '-',
            ],
        ]);
    }
}
