<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AttendanceReportExport implements FromArray, WithHeadings, ShouldAutoSize
{
    /** @var array<int, array<int, mixed>> */
    protected array $rows;

    /**
     * @param array<int, array<string, mixed>> $rows
     */
    public function __construct(array $rows)
    {
        $this->rows = array_map(function ($r) {
            return [
                $r['student_name'] ?? '-',
                $r['class_label'] ?? '-',
                $r['homeroom'] ?? '-',
                (int)($r['hadir'] ?? 0),
                (int)($r['sakit'] ?? 0),
                (int)($r['izin'] ?? 0),
                (int)($r['alpha'] ?? 0),
                (int)($r['total'] ?? 0),
                (float)($r['percent'] ?? 0),
            ];
        }, $rows);
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Nama Siswa','Kelas','Wali Kelas','Hadir','Sakit','Izin','Alpha','Total','% Kehadiran'];
    }
}

