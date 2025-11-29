@extends('layouts.master')

@section('page_title', 'Kelas')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Data Kelas</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#classModal" id="btnAddClass">
            <i class="bi bi-plus-lg me-2"></i>Tambah Kelas
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="classes_table">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th>Nama Kelas</th>
                        <th>Angkatan</th>
                        <th>Wali Kelas</th>
                        <th class="text-end w-150px">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                    @foreach($classes as $i => $class)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $class->name }}</td>
                            <td>{{ $class->batch->name ?? '-' }}</td>
                            <td>{{ $class->homeroomTeacher->name ?? '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-light-primary btn-sm me-2 btn-edit"
                                        data-id="{{ $class->id }}"
                                        data-name="{{ $class->name }}"
                                        data-batch_id="{{ $class->batch_id }}"
                                        data-homeroom_teacher_id="{{ $class->homeroom_teacher_id }}"
                                        data-bs-toggle="modal" data-bs-target="#classModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-light-danger btn-sm btn-delete"
                                        data-id="{{ $class->id }}"
                                        data-name="{{ $class->name }}"
                                        data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div class="modal fade" id="classModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="classForm" method="POST" action="{{ route('classes.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="classFormMethod" value="POST">
                    <input type="hidden" name="id" id="class_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="classModalTitle">Tambah Kelas</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label">Nama Kelas</label>
                            <input type="text" class="form-control" name="name" id="class_name" required maxlength="50">
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Angkatan</label>
                            <select class="form-select" name="batch_id" id="class_batch_id" required>
                                <option value="" disabled selected>Pilih Angkatan</option>
                                @foreach($batches as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->year }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Wali Kelas</label>
                            <select class="form-select" name="homeroom_teacher_id" id="class_homeroom_teacher_id">
                                <option value="" selected>Tidak ada</option>
                                @foreach($teachers as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveClass">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Hapus Kelas</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus kelas <strong id="delete_name">-</strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#classes_table').DataTable({
            pageLength: 10,
            ordering: true,
        });
    });

    const classModal = document.getElementById('classModal');
    const btnAdd = document.getElementById('btnAddClass');
    const form = document.getElementById('classForm');
    const formMethod = document.getElementById('classFormMethod');
    const inputId = document.getElementById('class_id');
    const inputName = document.getElementById('class_name');
    const selectBatch = document.getElementById('class_batch_id');
    const selectTeacher = document.getElementById('class_homeroom_teacher_id');
    const title = document.getElementById('classModalTitle');

    btnAdd?.addEventListener('click', () => {
        form.action = '{{ route('classes.store') }}';
        formMethod.value = 'POST';
        inputId.value = '';
        inputName.value = '';
        selectBatch.value = '';
        selectTeacher.value = '';
        title.textContent = 'Tambah Kelas';
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const batch_id = btn.getAttribute('data-batch_id');
            const homeroom_teacher_id = btn.getAttribute('data-homeroom_teacher_id');

            form.action = '{{ url('classes') }}/' + id;
            formMethod.value = 'PUT';
            inputId.value = id;
            inputName.value = name;
            selectBatch.value = batch_id;
            selectTeacher.value = homeroom_teacher_id || '';
            title.textContent = 'Edit Kelas';
        });
    });

    const deleteForm = document.getElementById('deleteForm');
    const deleteName = document.getElementById('delete_name');
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            deleteForm.action = '{{ url('classes') }}/' + id;
            deleteName.textContent = name;
        });
    });
</script>

@if(session('success'))
<script>
    (function(){
        var msg = @json(session('success'));
        if (window.toastr && toastr.success) { toastr.success(msg); }
        else { console.log('SUCCESS:', msg); }
    })();
</script>
@endif

@if(session('error'))
<script>
    (function(){
        var msg = @json(session('error'));
        if (window.toastr && toastr.error) { toastr.error(msg); }
        else { console.error('ERROR:', msg); }
    })();
</script>
@endif

@if($errors && $errors->any())
<script>
    (function(){
        var errs = @json($errors->all());
        var msg = errs.join('\n');
        if (window.toastr && toastr.error) { toastr.error(msg); }
        else { console.error('ERRORS:', msg); }
    })();
 </script>
@endif
@endpush
