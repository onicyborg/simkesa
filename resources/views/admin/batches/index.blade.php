@extends('layouts.master')

@section('page_title', 'Angkatan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Data Angkatan</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#batchModal" id="btnAddBatch">
            <i class="bi bi-plus-lg me-2"></i>Tambah Angkatan
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="batches_table">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-50px">No</th>
                        <th>Nama Angkatan</th>
                        <th class="w-100px">Tahun</th>
                        <th class="text-end w-150px">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                    @foreach($batches as $i => $batch)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $batch->name }}</td>
                            <td>{{ $batch->year }}</td>
                            <td class="text-end">
                                <button class="btn btn-light-primary btn-sm me-2 btn-edit"
                                        data-id="{{ $batch->id }}"
                                        data-name="{{ $batch->name }}"
                                        data-year="{{ $batch->year }}"
                                        data-bs-toggle="modal" data-bs-target="#batchModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-light-danger btn-sm btn-delete"
                                        data-id="{{ $batch->id }}"
                                        data-name="{{ $batch->name }}"
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
    <div class="modal fade" id="batchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="batchForm" method="POST" action="{{ route('batches.store') }}">
                    @csrf
                    <input type="hidden" name="_method" id="batchFormMethod" value="POST">
                    <input type="hidden" name="id" id="batch_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="batchModalTitle">Tambah Angkatan</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-5">
                            <label class="form-label">Nama Angkatan</label>
                            <input type="text" class="form-control" name="name" id="batch_name" required maxlength="50">
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Tahun</label>
                            <input type="number" class="form-control" name="year" id="batch_year" required min="1900" max="3000">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveBatch">Simpan</button>
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
                        <h5 class="modal-title">Hapus Angkatan</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus angkatan <strong id="delete_name">-</strong>?</p>
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
    // Init DataTable (Metronic style still works with default DataTables init)
    $(document).ready(function() {
        $('#batches_table').DataTable({
            pageLength: 10,
            ordering: true,
            language: {
                url: '' // gunakan default; dapat diisi URL bahasa jika perlu
            }
        });
    });

    const batchModal = document.getElementById('batchModal');
    const btnAdd = document.getElementById('btnAddBatch');
    const form = document.getElementById('batchForm');
    const formMethod = document.getElementById('batchFormMethod');
    const inputId = document.getElementById('batch_id');
    const inputName = document.getElementById('batch_name');
    const inputYear = document.getElementById('batch_year');
    const title = document.getElementById('batchModalTitle');

    // Reset to create mode
    btnAdd?.addEventListener('click', () => {
        form.action = '{{ route('batches.store') }}';
        formMethod.value = 'POST';
        inputId.value = '';
        inputName.value = '';
        inputYear.value = '';
        title.textContent = 'Tambah Angkatan';
    });

    // Edit buttons
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const year = btn.getAttribute('data-year');

            form.action = '{{ url('batches') }}/' + id;
            formMethod.value = 'PUT';
            inputId.value = id;
            inputName.value = name;
            inputYear.value = year;
            title.textContent = 'Edit Angkatan';
        });
    });

    // Delete buttons
    const deleteForm = document.getElementById('deleteForm');
    const deleteName = document.getElementById('delete_name');
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            deleteForm.action = '{{ url('batches') }}/' + id;
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

