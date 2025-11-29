@extends('layouts.master')

@section('page_title', 'Riwayat Kehadiran Saya')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Riwayat Kehadiran</h3>
        <div class="d-flex align-items-center gap-2">
            @php
                $prev = (clone $start)->subMonth();
                $next = (clone $start)->addMonth();
            @endphp
            <a href="{{ route('student.attendances.index', ['month' => $prev->month, 'year' => $prev->year]) }}" class="btn btn-light">&laquo; Bulan Sebelumnya</a>
            <a href="{{ route('student.attendances.index', ['month' => now()->month, 'year' => now()->year]) }}" class="btn btn-light">Bulan Ini</a>
            <a href="{{ route('student.attendances.index', ['month' => $next->month, 'year' => $next->year]) }}" class="btn btn-light">Bulan Berikutnya &raquo;</a>
        </div>
    </div>

    <div class="card mb-6">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">{{ \Carbon\Carbon::create($year,$month,1)->translatedFormat('F Y') }}</h5>
                <div class="d-flex flex-wrap gap-4">
                    <div class="d-flex align-items-center gap-2"><span class="legend-dot bg-secondary"></span><span>Belum ada data</span></div>
                    <div class="d-flex align-items-center gap-2"><span class="legend-dot bg-success"></span><span>Hadir (H)</span></div>
                    <div class="d-flex align-items-center gap-2"><span class="legend-dot bg-warning"></span><span>Sakit (S)</span></div>
                    <div class="d-flex align-items-center gap-2"><span class="legend-dot" style="background:#6f42c1"></span><span>Izin (I)</span></div>
                    <div class="d-flex align-items-center gap-2"><span class="legend-dot bg-danger"></span><span>Alpha (A)</span></div>
                </div>
            </div>

            @php
                $firstDow = (int)$start->copy()->dayOfWeekIso; // 1..7 (Mon..Sun)
                $daysInMonth = (int)$end->day;
                $cellsBefore = $firstDow - 1; // blanks before day 1
                $totalCells = $cellsBefore + $daysInMonth;
                $rows = (int)ceil($totalCells / 7);
                $todayDate = \Carbon\Carbon::parse($today);
            @endphp

            <div class="calendar-grid">
                <div class="calendar-row calendar-header">
                    @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $d)
                        <div class="calendar-cell text-muted fw-semibold">{{ $d }}</div>
                    @endforeach
                </div>
                @for($r=0; $r<$rows; $r++)
                    <div class="calendar-row">
                        @for($c=0; $c<7; $c++)
                            @php
                                $cellIndex = $r*7 + $c;
                                $dayNum = $cellIndex - $cellsBefore + 1;
                                $inMonth = $dayNum >= 1 && $dayNum <= $daysInMonth;
                                $dateObj = $inMonth ? \Carbon\Carbon::create($year,$month,$dayNum) : null;
                                $dateStr = $inMonth ? $dateObj->toDateString() : null;
                                $isFuture = $inMonth ? $dateObj->gt($todayDate) : true;
                                $status = $inMonth ? ($byDate[$dateStr]['status'] ?? null) : null;
                                $dotClass = 'bg-secondary';
                                if ($status === 'H') $dotClass = 'bg-success';
                                elseif ($status === 'S') $dotClass = 'bg-warning';
                                elseif ($status === 'I') $dotClass = '';
                                elseif ($status === 'A') $dotClass = 'bg-danger';
                                // custom purple for I
                                $dotStyle = $status === 'I' ? 'background:#6f42c1' : '';
                            @endphp
                            <div class="calendar-cell {{ $inMonth ? '' : 'is-out' }}">
                                @if($inMonth)
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="fw-semibold">{{ $dayNum }}</div>
                                        @if(!$isFuture)
                                            <button type="button" class="btn btn-icon btn-sm btn-outline-secondary" data-date="{{ $dateStr }}" data-bs-toggle="modal" data-bs-target="#attDetailModal" onclick="showAttendanceDetail('{{ $dateStr }}')">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="mt-2 d-flex justify-content-center">
                                        @if(!$isFuture)
                                            <span class="dot {{ $dotClass }}" style="{{ $dotStyle }}"></span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endfor
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Modal Detail Kehadiran -->
    <div class="modal fade" id="attDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kehadiran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><span class="text-muted">Tanggal:</span> <span id="att-date" class="fw-semibold"></span></div>
                    <div class="mb-2"><span class="text-muted">Status:</span> <span id="att-status"></span></div>
                    <div class="mb-2"><span class="text-muted">Keterangan:</span> <span id="att-remark" class="fw-semibold"></span></div>
                    <div class=""><span class="text-muted">Dicatat oleh:</span> <span id="att-recorder" class="fw-semibold"></span></div>
                    <div id="att-empty" class="text-muted">Belum ada data absensi pada tanggal ini.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    .calendar-grid { display: grid; gap: 8px; }
    .calendar-header { text-transform: uppercase; color: var(--bs-secondary-color); }
    .calendar-header .calendar-cell { text-align: center; font-size: 16px; }
    .calendar-row { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; }
    .calendar-cell {
        min-height: 90px;
        border: 1px solid var(--bs-border-color);
        border-radius: 8px;
        padding: 8px;
        background: var(--bs-body-bg);
        color: var(--bs-body-color);
    }
    .calendar-cell.is-out { background: var(--bs-secondary-bg); color: var(--bs-secondary-color); }
    .calendar-cell .fw-semibold { color: var(--bs-body-color); }
    .dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; border: 2px solid var(--bs-body-bg); }
    .legend-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; border: 2px solid var(--bs-body-bg); }
</style>
@endpush

@push('scripts')
<script>
    const BY_DATE = @json($byDate);
    function statusBadge(content) {
        const s = content || '';
        let cls = 'badge-light';
        let text = '-';
        if (s === 'H') { cls = 'badge-success'; text = 'Hadir'; }
        else if (s === 'S') { cls = 'badge-warning'; text = 'Sakit'; }
        else if (s === 'I') { cls = 'badge'; text = 'Izin'; } // custom purple below
        else if (s === 'A') { cls = 'badge-danger'; text = 'Alpha'; }
        if (s === 'I') {
            return `<span class="badge" style="background:#6f42c1;color:#fff">${text}</span>`;
        }
        return `<span class="badge ${cls}">${text}</span>`;
    }
    window.showAttendanceDetail = function(dateStr){
        const elDate = document.getElementById('att-date');
        const elStatus = document.getElementById('att-status');
        const elRemark = document.getElementById('att-remark');
        const elRecorder = document.getElementById('att-recorder');
        const elEmpty = document.getElementById('att-empty');

        elDate.textContent = dateStr;
        const data = BY_DATE[dateStr];
        if (data) {
            elStatus.innerHTML = statusBadge(data.status);
            elRemark.textContent = data.remark || '-';
            elRecorder.textContent = data.recorder || '-';
            elEmpty.style.display = 'none';
        } else {
            elStatus.innerHTML = statusBadge(null);
            elRemark.textContent = '-';
            elRecorder.textContent = '-';
            elEmpty.style.display = 'block';
        }
    }
</script>
@endpush
