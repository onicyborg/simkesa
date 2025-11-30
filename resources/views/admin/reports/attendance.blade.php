@extends('layouts.master')

@section('page_title', 'Laporan Kehadiran')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Laporan Kehadiran</h3>
    </div>

<div class="card mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.attendance') }}" class="row g-4 align-items-end" id="report-filter-form">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Kelas / Angkatan</label>
                <select name="class_id" id="class_id" class="form-select">
                    <option value="">Semua Kelas / Angkatan</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" data-batch="{{ $c->batch_id }}" {{ ($filters['class_id'] ?? '') == $c->id ? 'selected' : '' }}>
                            {{ $c->batch->year ?? '-' }} - {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="H" {{ ($filters['status'] ?? '')==='H' ? 'selected' : '' }}>Hadir (H)</option>
                    <option value="S" {{ ($filters['status'] ?? '')==='S' ? 'selected' : '' }}>Sakit (S)</option>
                    <option value="I" {{ ($filters['status'] ?? '')==='I' ? 'selected' : '' }}>Izin (I)</option>
                    <option value="A" {{ ($filters['status'] ?? '')==='A' ? 'selected' : '' }}>Alpha (A)</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-3">
                <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-eye me-2"></i>Tampilkan Laporan</button>
                <a href="{{ route('reports.attendance') }}" class="btn btn-light btn-sm">Reset</a>
            </div>
        </form>
    </div>
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

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0">Tabel Laporan</h5>
            <div class="d-flex gap-2">
                <a class="btn btn-success" href="{{ route('reports.attendance.export_excel', request()->query()) }}"><i class="bi bi-file-earmark-excel me-2"></i>Export Excel</a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="report_attendance_table">
                <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th class="text-center">Jumlah Hari Hadir</th>
                    <th class="text-center">Jumlah Sakit</th>
                    <th class="text-center">Jumlah Izin</th>
                    <th class="text-center">Jumlah Alpha</th>
                    <th class="text-center">% Kehadiran</th>
                </tr>
                </thead>
                <tbody class="text-gray-700 fw-semibold">
                @foreach($rows as $r)
                    <tr>
                        <td>{{ $r['student_name'] }}</td>
                        <td>{{ $r['class_label'] }}</td>
                        <td class="text-center">{{ $r['hadir'] }}</td>
                        <td class="text-center">{{ $r['sakit'] }}</td>
                        <td class="text-center">{{ $r['izin'] }}</td>
                        <td class="text-center">{{ $r['alpha'] }}</td>
                        <td class="text-center">{{ $r['percent'] }}%</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function(){
        $('#report_attendance_table').DataTable({
            pageLength: 10,
            ordering: true,
            language: {
                emptyTable: 'Tidak ada data'
            }
        });
    });
    </script>
@endpush

