<?php

namespace App\Models;

use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    use HasFactory, UsesUuid;

    protected $table = 'batches';

    protected $fillable = [
        'name',
        'year',
    ];

    /**
     * Relasi ke kelas-kelas pada angkatan ini.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'batch_id');
    }
}
