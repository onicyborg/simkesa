@extends('layouts.master')

@section('page_title', 'Log Notifikasi')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
    <h3 class="fw-bold mb-0">Log Notifikasi</h3>
    </div>

<div class="card mb-6">
    <div class="card-body">
        <form method="GET" action="{{ route('notification_logs.index') }}" class="row g-4 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua</option>
                    <option value="success" {{ ($filters['status'] ?? '')==='success' ? 'selected' : '' }}>Berhasil</option>
                    <option value="failed" {{ ($filters['status'] ?? '')==='failed' ? 'selected' : '' }}>Gagal</option>
                </select>
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
            <div class="col-md-3 d-flex gap-3">
                <button class="btn btn-primary" type="submit"><i class="bi bi-eye me-2"></i>Tampilkan</button>
                <a href="{{ route('notification_logs.index') }}" class="btn btn-light">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="mb-6 d-flex flex-wrap gap-3">
    @php $t = $summary['total'] ?? 0; $s = $summary['success'] ?? 0; $f = $summary['failed'] ?? 0; @endphp
    <span class="badge bg-secondary">Total: {{ $t }}</span>
    <span class="badge bg-success">Berhasil: {{ $s }}</span>
    <span class="badge bg-danger">Gagal: {{ $f }}</span>
    </div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-row-dashed fs-6 gy-5" id="notification_logs_table">
                <thead>
                <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                    <th class="w-175px">Tanggal & Waktu Kirim</th>
                    <th>Nama Siswa</th>
                    <th>Kelas</th>
                    <th>Email Tujuan</th>
                    <th class="w-100px">Status</th>
                    <th class="w-75px">Aksi</th>
                </tr>
                </thead>
                <tbody class="text-gray-700 fw-semibold">
                @foreach($logs as $log)
                    @php
                        $student = optional($log->attendance)->student;
                        $kelas = $student && $student->schoolClass ? (($student->schoolClass->batch->year ?? '-') . ' - ' . ($student->schoolClass->name ?? '-')) : '-';
                        $isFailed = $log->status === 'failed';
                        $badge = $isFailed ? 'bg-danger' : 'bg-success';
                    @endphp
                    <tr>
                        <td>{{ optional($log->sent_at)->format('d M Y H:i') }}</td>
                        <td>{{ $student->full_name ?? '-' }}</td>
                        <td>{{ $kelas }}</td>
                        <td>{{ $log->recipient_email }}</td>
                        <td><span class="badge {{ $badge }}">{{ $log->status }}</span></td>
                        <td>
                            @if($isFailed)
                            <button type="button" class="btn btn-sm btn-light-danger" data-bs-toggle="modal" data-bs-target="#logDetailModal" data-id="{{ $log->id }}">
                                Detail
                            </button>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Log Notifikasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="log-detail-body">
            <div class="text-muted">Memuat...</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    $(function(){
        const table = $('#notification_logs_table').DataTable({
            pageLength: 10,
            ordering: true,
            language: { emptyTable: 'Tidak ada data' }
        });

        const modal = document.getElementById('logDetailModal');
        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if(!button) return;
            const id = button.getAttribute('data-id');
            const body = document.getElementById('log-detail-body');
            body.innerHTML = '<div class="text-muted">Memuat...</div>';
            fetch("{{ url('/notification-logs') }}/" + id)
                .then(res => res.json())
                .then(data => {
                    body.innerHTML = `
                        <div class="mb-3"><strong>Status:</strong> ${data.status}</div>
                        <div class="mb-3"><strong>Tanggal & Waktu:</strong> ${data.sent_at ?? '-'}</div>
                        <div class="mb-3"><strong>Nama Siswa:</strong> ${data.student?.name ?? '-'}</div>
                        <div class="mb-3"><strong>Kelas:</strong> ${data.student?.class ?? '-'}</div>
                        <div class="mb-3"><strong>Email Tujuan:</strong> ${data.recipient_email ?? '-'}</div>
                        <div class="mb-3"><strong>Pesan Error:</strong><br><pre class="mb-0 bg-light p-3 rounded">${(data.error_message ?? '-').replace(/</g,'&lt;')}</pre></div>
                    `;
                })
                .catch(() => {
                    body.innerHTML = '<div class="text-danger">Gagal memuat detail.</div>';
                });
        });
    });
</script>
@endpush

