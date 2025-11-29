<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'batch_id',
        'homeroom_teacher_id',
    ];

    /**
     * Relasi ke angkatan.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Relasi ke wali kelas (user dengan role=teacher).
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'homeroom_teacher_id');
    }

    /**
     * Relasi ke siswa-siswa dalam kelas ini.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
