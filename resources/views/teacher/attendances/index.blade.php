@extends('layouts.master')

@section('page_title', 'Riwayat Kehadiran Saya (Guru)')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Riwayat Kehadiran</h3>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" action="{{ route('teacher.attendances.index') }}" class="row g-4 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kelas</label>
                    <select name="class_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ ($filters['class_id'] ?? '') == $c->id ? 'selected' : '' }}>
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
                        <option value="A" {{ ($filters['status'] ?? '')==='A' ? 'selected' : '' }}>Alpa (A)</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-3">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search me-2"></i>Filter</button>
                    <a href="{{ route('teacher.attendances.index') }}" class="btn btn-light">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-6 d-flex flex-wrap gap-3">
        @php
            $h = $summary['H'] ?? 0; $s = $summary['S'] ?? 0; $i = $summary['I'] ?? 0; $a = $summary['A'] ?? 0;
        @endphp
        <span class="badge bg-success">Hadir: {{ $h }}</span>
        <span class="badge bg-warning text-dark">Sakit: {{ $s }}</span>
        <span class="badge bg-warning text-dark">Izin: {{ $i }}</span>
        <span class="badge bg-danger">Alpa: {{ $a }}</span>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="attendances_table">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-125px">Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th class="w-100px">Status</th>
                        <th>Keterangan</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                    @foreach($attendances as $a)
                        @php
                            $kelas = $a->student && $a->student->schoolClass ? (($a->student->schoolClass->batch->year ?? '-') . ' - ' . ($a->student->schoolClass->name ?? '-')) : '-';
                            $badgeClass = match($a->status) { 'H' => 'bg-success', 'A' => 'bg-danger', 'S' => 'bg-warning text-dark', 'I' => 'bg-warning text-dark', default => 'bg-secondary' };
                        @endphp
                        <tr>
                            <td>{{ optional($a->attendance_date)->format('Y-m-d') }}</td>
                            <td>{{ $a->student->full_name ?? '-' }}</td>
                            <td>{{ $kelas }}</td>
                            <td><span class="badge {{ $badgeClass }}">{{ $a->status }}</span></td>
                            <td>{{ $a->remark ?? '-' }}</td>
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
    $(document).ready(function() {
        $('#attendances_table').DataTable({
            pageLength: 10,
            ordering: true,
        });
    });
</script>
@endpush
