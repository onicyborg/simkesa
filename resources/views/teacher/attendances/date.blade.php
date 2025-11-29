@extends('layouts.master')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-6">
        <div>
            <h2 class="mb-1">Input Kehadiran</h2>
            <div class="text-muted">
                Kelas: <span class="fw-bold">{{ $schoolClass->name }}</span>
                · Angkatan: {{ optional($schoolClass->batch)->year ?? '-' }}
                · Tanggal: <span class="fw-bold">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('teacher.attendances.calendar', $schoolClass->id) }}" class="btn btn-light">Kembali ke
                Kalender</a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-row-dashed align-middle" id="attendanceTable">
                    <thead>
                        <tr class="fw-bold text-muted">
                            <th>Foto</th>
                            <th>NIS</th>
                            <th>Nama Siswa</th>
                            <th>Nama Orang Tua</th>
                            <th>Status Absen</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $student)
                            @php $att = $attendances[$student->id] ?? null; @endphp
                            <tr>
                                <td>
                                    <div class="symbol symbol-45px symbol-circle">
                                        <img alt="Pic"
                                            src="{{ $student->photo_url ?? asset('assets/media/avatars/blank.png') }}" />
                                    </div>
                                </td>
                                <td>{{ $student->nis ?? '-' }}</td>
                                <td class="fw-bold">{{ $student->full_name }}</td>
                                <td>{{ $student->parent_name ?? '-' }}</td>
                                <td>
                                    @if ($att)
                                        @php
                                            $map = ['H' => 'success', 'S' => 'warning', 'I' => 'info', 'A' => 'danger'];
                                            $badge = $map[$att->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge badge-light-{{ $badge }}">
                                            @if ($att->status == 'H')
                                                Hadir
                                            @elseif ($att->status == 'S')
                                                Sakit
                                            @elseif ($att->status == 'I')
                                                Izin
                                            @elseif ($att->status == 'A')
                                                Alpha
                                            @endif
                                        </span>
                                    @else
                                        <span class="badge badge-light">Belum Diisi</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($att)
                                        {{ $att->remark ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-light" data-kt-action="detail"
                                            data-student='{{ json_encode(['id' => $student->id, 'nis' => $student->nis, 'name' => $student->full_name, 'parent_name' => $student->parent_name, 'parent_email' => $student->parent_email, 'photo' => $student->photo_url ?? asset('assets/media/avatars/blank.png')]) }}'>Detail</button>
                                        <button class="btn btn-sm btn-primary" data-kt-action="absen"
                                            data-student-id="{{ $student->id }}"
                                            data-current-status="{{ $att->status ?? '' }}"
                                            data-remark="{{ $att->remark ?? '' }}">Absen</button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <div class="symbol symbol-75px symbol-circle flex-shrink-0">
                            <img id="dPhoto" alt="Foto" src="{{ asset('assets/media/avatars/blank.png') }}" />
                        </div>
                        <div>
                            <div class="fw-bold fs-5" id="dName">-</div>
                            <div class="text-muted">NIS: <span id="dNis">-</span></div>
                            <div class="text-muted">Orang Tua: <span id="dParent">-</span></div>
                        </div>
                    </div>
                    <dl class="row mb-0">
                        <dt class="col-sm-4">NIS</dt>
                        <dd class="col-sm-8" id="dNisDup">-</dd>
                        <dt class="col-sm-4">Nama</dt>
                        <dd class="col-sm-8" id="dNameDup">-</dd>
                        <dt class="col-sm-4">Orang Tua</dt>
                        <dd class="col-sm-8" id="dParentDup">-</dd>
                        <dt class="col-sm-4">Email Orang Tua</dt>
                        <dd class="col-sm-8" id="dParentEmail">-</dd>
                    </dl>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Absen -->
    <div class="modal fade" id="absenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('teacher.attendances.store', [$schoolClass->id, $date]) }}"
                    id="absenForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Input Kehadiran</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="student_id" id="fStudentId">
                        <div class="mb-5">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="fStatus" required>
                                <option value="H">H - Hadir</option>
                                <option value="S">S - Sakit</option>
                                <option value="I">I - Izin</option>
                                <option value="A">A - Alpha</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="remark" id="fRemark" rows="3" placeholder="Opsional"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
        const absenModal = new bootstrap.Modal(document.getElementById('absenModal'));

        document.querySelectorAll('[data-kt-action="detail"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const s = JSON.parse(btn.getAttribute('data-student'));
                document.getElementById('dNis').textContent = s.nis || '-';
                document.getElementById('dName').textContent = s.name || '-';
                document.getElementById('dParent').textContent = s.parent_name || '-';
                const photo = s.photo || "{{ asset('assets/media/avatars/blank.png') }}";
                const img = document.getElementById('dPhoto');
                if (img) img.src = photo;
                // duplicate rows for detailed list
                const dNisDup = document.getElementById('dNisDup');
                const dNameDup = document.getElementById('dNameDup');
                const dParentDup = document.getElementById('dParentDup');
                const dParentEmail = document.getElementById('dParentEmail');
                if (dNisDup) dNisDup.textContent = s.nis || '-';
                if (dNameDup) dNameDup.textContent = s.name || '-';
                if (dParentDup) dParentDup.textContent = s.parent_name || '-';
                if (dParentEmail) dParentEmail.textContent = s.parent_email || '-';
                detailModal.show();
            });
        });

        document.querySelectorAll('[data-kt-action="absen"]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('fStudentId').value = btn.getAttribute('data-student-id');
                const cur = btn.getAttribute('data-current-status') || 'H';
                const remark = btn.getAttribute('data-remark') || '';
                document.getElementById('fStatus').value = cur;
                document.getElementById('fRemark').value = remark;
                absenModal.show();
            });
        });
    </script>
@endpush
