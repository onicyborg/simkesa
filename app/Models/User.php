<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UsesUuid;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke data siswa (kalau user ini role=student).
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Relasi ke kelas yang diawali oleh guru ini (wali kelas).
     */
    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    /**
     * Relasi ke absensi yang dicatat oleh user ini (guru/admin).
     */
    public function recordedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'recorded_by');
    }
}
