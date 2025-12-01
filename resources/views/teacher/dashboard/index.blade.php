@extends('layouts.master')

@section('page_title', 'Dashboard Guru')

@section('content')
    <div class="mb-5 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h1 class="fw-bold fs-2qx mb-3">Dashboard Guru</h1>
            <div class="text-gray-600">Ringkasan kehadiran untuk kelas yang Anda ampu.</div>
        </div>
        <a href="{{ route('attendances.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>Input Kehadiran Hari Ini
        </a>
    </div>

    <div class="row g-5 mb-5">
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2">Total Kelas Saya</div>
                    <div class="fs-2 fw-bold">{{ $dashboard['total_classes'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2">Total Siswa</div>
                    <div class="fs-2 fw-bold">{{ $dashboard['total_students'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2">Hadir Hari Ini</div>
                    <div class="fs-2 fw-bold">{{ $dashboard['present_today'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-flush h-100">
                <div class="card-body py-5">
                    <div class="text-gray-500 fw-semibold mb-2">Tidak Hadir Hari Ini</div>
                    <div class="fs-2 fw-bold text-danger">{{ $dashboard['absent_today'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-flush">
        <div class="card-header">
            <div class="card-title">
                <h3 class="fw-bold">Kehadiran Terbaru Kelas Saya</h3>
            </div>
        </div>
        <div class="card-body py-5">
            @if(!empty($recentAttendances ?? []) && count($recentAttendances))
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-6">
                        <thead>
                        <tr class="text-start text-gray-500 fw-semibold text-uppercase gs-0">
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Pencatat</th>
                        </tr>
                        </thead>
                        <tbody class="fw-semibold text-gray-700">
                        @foreach($recentAttendances as $attendance)
                            @php $status = strtoupper($attendance->status); @endphp
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</td>
                                <td>{{ $attendance->student->full_name ?? '-' }}</td>
                                <td>{{ $attendance->student->schoolClass->name ?? '-' }}</td>
                                <td>
                                    <span class="badge
                                        @if($status === 'H') badge-success
                                        @elseif($status === 'A') badge-danger
                                        @elseif($status === 'I') badge-warning
                                        @elseif($status === 'S') badge-info @endif">

                                        @if ($status === 'H')
                                            Hadir
                                        @elseif ($status === 'A')
                                            Alpha
                                        @elseif ($status === 'I')
                                            Izin
                                        @elseif ($status === 'S')
                                            Sakit
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $attendance->remark ?? '-' }}</td>
                                <td>{{ $attendance->recorder->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-muted">Belum ada data kehadiran terbaru.</div>
            @endif
        </div>
    </div>
@endsection

