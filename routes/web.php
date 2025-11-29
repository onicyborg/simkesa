<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TeacherReportController;
use App\Http\Controllers\NotificationLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TeacherAttendanceController;
use App\Http\Controllers\TeacherAttendanceHistoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return view('auth.login');
    })->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.attempt');
});

// routes for user with auth
Route::group(['middleware' => 'auth'], function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile routes used by profile.blade.php
    Route::get('/profile', [AuthController::class, 'profileShow'])->name('profile.show');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/photo', [AuthController::class, 'updateProfilePhoto'])->name('profile.photo');
    Route::post('/profile/change-password', [AuthController::class, 'changePassword'])->name('profile.change_password');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin only routes
Route::group(['middleware' => ['auth','role:admin']], function () {
    Route::resource('batches', BatchController::class)->except(['show']);
    Route::resource('classes', SchoolClassController::class)->except(['show']);
    Route::resource('teachers', TeacherController::class)->except(['show']);
    Route::resource('students', StudentController::class)->except(['show']);
    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');

    // Reports
    Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('/reports/attendance/export-excel', [ReportController::class, 'exportExcel'])->name('reports.attendance.export_excel');
    Route::get('/reports/attendance/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.attendance.export_pdf');

    // Notification Logs
    Route::get('/notification-logs', [NotificationLogController::class, 'index'])->name('notification_logs.index');
    Route::get('/notification-logs/{id}', [NotificationLogController::class, 'show'])->name('notification_logs.show');
});

Route::group(['middleware' => ['auth','role:teacher']], function () {
    // CTA dari dashboard guru
    Route::get('/attendances/create', [AttendanceController::class, 'create'])->name('attendances.create');

    // Flow Input Kehadiran Siswa (3 tahap)
    Route::prefix('teacher/attendances')->name('teacher.attendances.')->group(function () {
        Route::get('/', [TeacherAttendanceHistoryController::class, 'index'])->name('index');
        Route::get('/classes', [TeacherAttendanceController::class, 'indexClasses'])->name('classes');
        Route::get('/{class}/calendar', [TeacherAttendanceController::class, 'showCalendar'])->name('calendar');
        Route::get('/{class}/calendar/status', [TeacherAttendanceController::class, 'calendarStatus'])->name('calendar_status');
        Route::get('/{class}/date/{date}', [TeacherAttendanceController::class, 'showByDate'])->name('by_date');
        Route::post('/{class}/date/{date}/store', [TeacherAttendanceController::class, 'storeOrUpdate'])->name('store');
    });

    // Teacher Reports
    Route::get('/teacher/reports/attendance', [TeacherReportController::class, 'attendance'])->name('teacher.reports.attendance');
    Route::get('/teacher/reports/attendance/export-excel', [TeacherReportController::class, 'exportExcel'])->name('teacher.reports.attendance.export_excel');
});

Route::group(['middleware' => ['auth','role:student']], function () {
    // Tambahkan route khusus siswa jika diperlukan
});
