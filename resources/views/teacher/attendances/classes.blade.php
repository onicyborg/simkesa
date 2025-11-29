@extends('layouts.master')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-6">
        <h2 class="mb-0">Pilih Kelas - Input Kehadiran</h2>
    </div>

    <div class="row g-6">
        @forelse($classes as $class)
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="fs-6 text-muted">Angkatan</div>
                                <div class="fw-bold">{{ optional($class->batch)->year ?? '-' }}</div>
                            </div>
                            <span class="badge badge-light-primary fs-base">{{ $class->students_count }} Siswa</span>
                        </div>
                        <div class="mb-4">
                            <div class="fs-5 fw-bold">{{ $class->name }}</div>
                        </div>
                        <div class="d-grid">
                            <a href="{{ route('teacher.attendances.calendar', $class->id) }}" class="btn btn-primary">
                                Pilih Kelas / Input Kehadiran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Anda belum terdaftar sebagai wali kelas untuk kelas manapun.</div>
            </div>
        @endforelse
    </div>
@endsection
