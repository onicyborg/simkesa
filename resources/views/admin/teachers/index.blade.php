@extends('layouts.master')

@section('page_title', 'Guru')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Data Guru</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#teacherModal" id="btnAddTeacher">
            <i class="bi bi-plus-lg me-2"></i>Tambah Guru
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="teachers_table">
                    <thead>
                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                        <th class="w-75px">Foto</th>
                        <th>Nama Guru</th>
                        <th>Kelas (Wali Kelas)</th>
                        <th class="text-end w-150px">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                    @foreach($teachers as $t)
                        @php
                            $photo = $t->photo_url ?? asset('assets/media/avatars/blank.png');
                            $classNames = $t->homeroomClasses->pluck('name')->filter()->implode(', ');
                        @endphp
                        <tr>
                            <td>
                                <div class="symbol symbol-50px">
                                    <img src="{{ $photo }}" alt="avatar" class="rounded"/>
                                </div>
                            </td>
                            <td>{{ $t->name }}</td>
                            <td>{{ $classNames ?: '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-light-primary btn-sm me-2 btn-edit"
                                        data-id="{{ $t->id }}"
                                        data-name="{{ $t->name }}"
                                        data-email="{{ $t->email }}"
                                        data-photo="{{ $t->photo_url ?? '' }}"
                                        data-bs-toggle="modal" data-bs-target="#teacherModal">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-light-danger btn-sm btn-delete"
                                        data-id="{{ $t->id }}"
                                        data-name="{{ $t->name }}"
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
    <div class="modal fade" id="teacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="teacherForm" method="POST" action="{{ route('teachers.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="teacherFormMethod" value="POST">
                    <input type="hidden" name="id" id="teacher_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="teacherModalTitle">Tambah Guru</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-5">
                                <label class="form-label d-block">Foto Profil</label>
                                <div class="image-input image-input-circle" data-kt-image-input="true" style="background-image: url('{{ asset('assets/media/svg/avatars/blank.svg') }}')">
                                    <div id="teacher_photo_wrapper" class="image-input-wrapper w-125px h-125px"></div>
                                    <label class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                           data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Change avatar">
                                        <i class="bi bi-pencil fs-6"></i>
                                        <input type="file" name="photo" id="teacher_photo" accept=".png, .jpg, .jpeg, .webp" />
                                        <input type="hidden" name="photo_remove" />
                                    </label>
                                    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Cancel avatar">
                                        <i class="bi bi-x fs-3"></i>
                                    </span>
                                    <span class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" data-bs-dismiss="click" title="Remove avatar">
                                        <i class="bi bi-x fs-3"></i>
                                    </span>
                                </div>
                                <div class="invalid-feedback d-block" data-field="photo"></div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-5">
                                    <label class="form-label">Nama</label>
                                    <input type="text" class="form-control" name="name" id="teacher_name" required maxlength="100">
                                </div>
                                <div class="mb-5">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="teacher_email" required maxlength="100">
                                </div>
                                <div class="mb-5" id="passwordWrapper">
                                    <label class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="teacher_password" minlength="8">
                                    <small class="text-muted d-block mt-1">Minimal 8 karakter. Kosongkan saat edit jika tidak diubah.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveTeacher">Simpan</button>
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
                        <h5 class="modal-title">Hapus Guru</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus guru <strong id="delete_name">-</strong>?</p>
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
        $('#teachers_table').DataTable({
            pageLength: 10,
            ordering: true,
        });
    });

    const btnAdd = document.getElementById('btnAddTeacher');
    const form = document.getElementById('teacherForm');
    const formMethod = document.getElementById('teacherFormMethod');
    const inputId = document.getElementById('teacher_id');
    const inputName = document.getElementById('teacher_name');
    const inputEmail = document.getElementById('teacher_email');
    const inputPassword = document.getElementById('teacher_password');
    const title = document.getElementById('teacherModalTitle');
    const photoInput = document.getElementById('teacher_photo');
    const photoWrapper = document.getElementById('teacher_photo_wrapper');
    const defaultAvatar = '{{ asset('assets/media/svg/avatars/blank.svg') }}';

    btnAdd?.addEventListener('click', () => {
        form.action = '{{ route('teachers.store') }}';
        formMethod.value = 'POST';
        inputId.value = '';
        inputName.value = '';
        inputEmail.value = '';
        inputPassword.required = true;
        inputPassword.value = '';
        title.textContent = 'Tambah Guru';
        if (photoWrapper) photoWrapper.style.backgroundImage = `url('${defaultAvatar}')`;
        if (photoInput) photoInput.value = '';
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const email = btn.getAttribute('data-email');
            const photo = btn.getAttribute('data-photo');

            form.action = '{{ url('teachers') }}/' + id;
            formMethod.value = 'PUT';
            inputId.value = id;
            inputName.value = name;
            inputEmail.value = email;
            inputPassword.required = false;
            inputPassword.value = '';
            title.textContent = 'Edit Guru';
            if (photoWrapper) photoWrapper.style.backgroundImage = `url('${photo || defaultAvatar}')`;
            if (photoInput) photoInput.value = '';
        });
    });

    photoInput?.addEventListener('change', function(){
        const f = photoInput.files && photoInput.files[0];
        if (!f) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            if (photoWrapper) photoWrapper.style.backgroundImage = `url('${e.target.result}')`;
        };
        reader.readAsDataURL(f);
    });

    const deleteForm = document.getElementById('deleteForm');
    const deleteName = document.getElementById('delete_name');
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            deleteForm.action = '{{ url('teachers') }}/' + id;
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

