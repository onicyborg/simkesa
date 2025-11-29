@extends('layouts.master')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-6">
        <div>
            <h2 class="mb-1">Pilih Tanggal Kehadiran</h2>
            <div class="text-muted">Kelas: <span class="fw-bold">{{ $schoolClass->name }}</span> · Angkatan:
                {{ optional($schoolClass->batch)->year ?? '-' }}</div>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button id="prevMonth" class="btn btn-light">Bulan Sebelumnya</button>
            <div id="monthLabel" class="fw-bold fs-5"></div>
            <button id="nextMonth" class="btn btn-light">Bulan Berikutnya</button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex align-items-center gap-4 mb-5">
                <div class="d-flex align-items-center gap-2"><span class="bullet bullet-dot bg-danger"></span> <span>Belum
                        ada data</span></div>
                <div class="d-flex align-items-center gap-2"><span class="bullet bullet-dot bg-warning"></span>
                    <span>Sebagian terisi</span></div>
                <div class="d-flex align-items-center gap-2"><span class="bullet bullet-dot bg-success"></span> <span>Semua
                        siswa sudah terisi</span></div>
                <div class="d-flex align-items-center gap-2"><span class="badge badge-light">Disabled</span> <span>Tanggal
                        setelah hari ini</span></div>
            </div>

            <div id="calendar" class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead>
                        <tr class="text-center fw-bold">
                            <th>Sen</th>
                            <th>Sel</th>
                            <th>Rab</th>
                            <th>Kam</th>
                            <th>Jum</th>
                            <th>Sab</th>
                            <th>Min</th>
                        </tr>
                    </thead>
                    <tbody id="calendarBody"></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/id.js"></script>
    <script>
        dayjs.locale('id');
        const classId = @json($schoolClass->id);
        let current = dayjs();

        const monthLabel = document.getElementById('monthLabel');
        const body = document.getElementById('calendarBody');

        function buildCalendar() {
            monthLabel.textContent = current.format('MMMM YYYY');
            body.innerHTML = '';

            const startOfMonth = current.startOf('month');
            const endOfMonth = current.endOf('month');
            const startWeekday = (startOfMonth.day() + 6) % 7; // convert Sunday(0) to 6
            const daysInMonth = endOfMonth.date();

            fetch(
                    `{{ route('teacher.attendances.calendar_status', $schoolClass->id) }}?month=${current.month()+1}&year=${current.year()}`)
                .then(r => r.json())
                .then(({
                    status
                }) => {
                    let day = 1;
                    for (let row = 0; row < 6; row++) {
                        const tr = document.createElement('tr');
                        for (let col = 0; col < 7; col++) {
                            const td = document.createElement('td');
                            td.className = 'text-center py-6';
                            if (row === 0 && col < startWeekday) {
                                td.innerHTML = '&nbsp;';
                            } else if (day > daysInMonth) {
                                td.innerHTML = '&nbsp;';
                            } else {
                                const date = dayjs(
                                    `${current.year()}-${String(current.month()+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`
                                    ).format('YYYY-MM-DD');
                                const st = status[date] || 'red';
                                const disabled = st === 'disabled';
                                const dotColor = st === 'green' ? 'bg-success' : (st === 'yellow' ? 'bg-warning' : (
                                    st === 'red' ? 'bg-danger' : ''));

                                td.innerHTML = `
                                <div class="d-flex flex-column align-items-center gap-2">
                                    <div class="fw-bold">${day}</div>
                                    ${dotColor ? `<span class="bullet bullet-dot ${dotColor}"></span>` : ''}
                                </div>`;

                                if (!disabled) {
                                    td.style.cursor = 'pointer';
                                    td.addEventListener('click', () => {
                                        const url = `{{ url('teacher/attendances') }}/${classId}/date/${date}`;
                                        window.location.href = url;
                                    });
                                } else {
                                    td.classList.add('text-muted');
                                }
                                day++;
                            }
                            tr.appendChild(td);
                        }
                        body.appendChild(tr);
                        if (day > daysInMonth) break;
                    }
                });
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            current = current.subtract(1, 'month');
            buildCalendar();
        });
        document.getElementById('nextMonth').addEventListener('click', () => {
            current = current.add(1, 'month');
            buildCalendar();
        });

        buildCalendar();
    </script>
@endpush
