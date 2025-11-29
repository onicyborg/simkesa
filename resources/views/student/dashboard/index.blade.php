@extends('layouts.master')

@section('page_title', 'Dashboard Siswa')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Dashboard Siswa</h3>
    </div>

    <div class="row g-4 mb-6">
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fs-7 text-muted">Total Record</div>
                    <div class="fs-2 fw-bold">{{ $summary['total'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fs-7 text-muted">Hadir (H)</div>
                    <div class="fs-2 fw-bold text-success">{{ $summary['H'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fs-7 text-muted">Sakit (S)</div>
                    <div class="fs-2 fw-bold text-warning">{{ $summary['S'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fs-7 text-muted">Izin (I)</div>
                    <div class="fs-2 fw-bold text-warning">{{ $summary['I'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fs-7 text-muted">Alpha (A)</div>
                    <div class="fs-2 fw-bold text-danger">{{ $summary['A'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card h-100">
                <div class="card-body">
                    <div class="fs-7 text-muted">% Kehadiran</div>
                    <div class="fs-2 fw-bold">{{ $summary['percent'] ?? 0 }}%</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-6">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-4">Informasi Siswa</h5>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Nama</div>
                        <div class="col-8 fw-semibold">{{ $student->full_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">NIS</div>
                        <div class="col-8 fw-semibold">{{ $student->nis }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-4 text-muted">Kelas</div>
                        <div class="col-8 fw-semibold">{{ $classLabel }}</div>
                    </div>
                    <div class="row">
                        <div class="col-4 text-muted">Wali Kelas</div>
                        <div class="col-8 fw-semibold">{{ $homeroom }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Kehadiran Terbaru</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-4">
                            <thead>
                                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700 fw-semibold">
                                @forelse($recentAttendances as $a)
                                    <tr>
                                        <td>{{ $a->attendance_date->format('d-m-Y') }}</td>
                                        <td>
                                            @php
                                                $badge = 'badge-light';
                                                if ($a->status === 'H') $badge = 'badge-success';
                                                elseif ($a->status === 'S') $badge = 'badge-warning';
                                                elseif ($a->status === 'I') $badge = 'badge-info';
                                                elseif ($a->status === 'A') $badge = 'badge-danger';
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $a->status }}</span>
                                        </td>
                                        <td>{{ $a->remark ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
