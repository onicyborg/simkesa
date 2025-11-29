<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'attendances';

    protected $fillable = [
        'student_id',
        'attendance_date',
        'status',
        'remark',
        'recorded_by',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    /**
     * Siswa yang absensinya direkam.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * User (guru/admin) yang mencatat absensi ini.
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Log notifikasi email yang terkait dengan absensi ini.
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'attendance_id');
    }
}
