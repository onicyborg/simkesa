@extends('layouts.master')

@section('page_title', 'Siswa (Guru)')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-5">
        <h3 class="fw-bold mb-0">Data Siswa (Kelas Saya)</h3>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentModal" id="btnAddStudent">
            <i class="bi bi-plus-lg me-2"></i>Tambah Siswa
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-5" id="students_table">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th class="w-75px">Foto</th>
                            <th>Nama Siswa</th>
                            <th class="w-125px">NIS</th>
                            <th>Kelas</th>
                            <th>Nama Orang Tua</th>
                            <th>Email Orang Tua</th>
                            <th class="text-end w-150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 fw-semibold">
                        @foreach ($students as $s)
                            @php
                                $photo = optional($s->user)->photo_url ?? asset('assets/media/avatars/blank.png');
                                $kelas = $s->schoolClass
                                    ? ($s->schoolClass->batch->year ?? '-') . ' - ' . ($s->schoolClass->name ?? '-')
                                    : '-';
                            @endphp
                            <tr>
                                <td>
                                    <div class="symbol symbol-50px">
                                        <img src="{{ $photo }}" alt="avatar" class="rounded" />
                                    </div>
                                </td>
                                <td>{{ $s->full_name }}</td>
                                <td>{{ $s->nis }}</td>
                                <td>{{ $kelas }}</td>
                                <td>{{ $s->parent_name ?: '-' }}</td>
                                <td>{{ $s->parent_email ?: '-' }}</td>
                                <td class="text-end">
                                    <button class="btn btn-light-primary btn-sm me-2 btn-edit" data-id="{{ $s->id }}"
                                        data-full_name="{{ $s->full_name }}" data-nis="{{ $s->nis }}"
                                        data-class_id="{{ $s->class_id }}" data-parent_name="{{ $s->parent_name }}"
                                        data-parent_email="{{ $s->parent_email }}"
                                        data-email="{{ optional($s->user)->email }}" data-photo="{{ $photo }}"
                                        data-bs-toggle="modal" data-bs-target="#studentModal">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button class="btn btn-light-danger btn-sm btn-delete" data-id="{{ $s->id }}"
                                        data-name="{{ $s->full_name }}" data-bs-toggle="modal"
                                        data-bs-target="#confirmDeleteModal">
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
    <div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <form id="studentForm" method="POST" action="{{ route('teacher.students.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="_method" id="studentFormMethod" value="POST">
                    <input type="hidden" name="id" id="student_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="studentModalTitle">Tambah Siswa</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4 mb-5">
                                <center>
                                    <label class="form-label d-block">Foto Profil</label>
                                    <div class="image-input image-input-circle" data-kt-image-input="true"
                                        style="background-image: url('{{ asset('assets/media/svg/avatars/blank.svg') }}')">
                                        <div id="student_photo_wrapper" class="image-input-wrapper w-125px h-125px"></div>
                                        <label
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="change" data-bs-toggle="tooltip" data-bs-dismiss="click"
                                            title="Change avatar">
                                            <i class="bi bi-pencil fs-6"></i>
                                            <input type="file" name="photo" id="student_photo"
                                                accept=".png, .jpg, .jpeg, .webp" />
                                            <input type="hidden" name="photo_remove" />
                                        </label>
                                        <span
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="cancel" data-bs-toggle="tooltip" data-bs-dismiss="click"
                                            title="Cancel avatar">
                                            <i class="bi bi-x fs-3"></i>
                                        </span>
                                        <span
                                            class="btn btn-icon btn-circle btn-color-muted btn-active-color-primary w-25px h-25px bg-body shadow"
                                            data-kt-image-input-action="remove" data-bs-toggle="tooltip" data-bs-dismiss="click"
                                            title="Remove avatar">
                                            <i class="bi bi-x fs-3"></i>
                                        </span>
                                    </div>
                                    <div class="invalid-feedback d-block" data-field="photo"></div>
                                </center>
                            </div>
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input type="text" class="form-control" name="full_name"
                                                id="student_full_name" required maxlength="150">
                                        </div>
                                        <div class="mb-5">
                                            <label class="form-label">NIS</label>
                                            <input type="text" class="form-control" name="nis" id="student_nis"
                                                required maxlength="100">
                                        </div>
                                        <div class="mb-5">
                                            <label class="form-label">Kelas</label>
                                            <select class="form-select" name="class_id" id="student_class_id" required
                                                style="width: 100%">
                                                <option value="" disabled selected>Pilih Kelas</option>
                                                @foreach ($classes as $c)
                                                    <option value="{{ $c->id }}">{{ $c->batch->year ?? '-' }} -
                                                        {{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-5">
                                            <label class="form-label">Email (Akun Siswa)</label>
                                            <input type="email" class="form-control" name="email" id="student_email"
                                                maxlength="150">
                                        </div>
                                        <div class="mb-5" id="studentPasswordWrapper">
                                            <label class="form-label">Password (Akun Siswa)</label>
                                            <input type="password" class="form-control" name="password"
                                                id="student_password" minlength="8">
                                            <small class="text-muted d-block mt-1">Minimal 8 karakter. Kosongkan saat edit
                                                jika tidak diubah.</small>
                                        </div>
                                        <div class="mb-5">
                                            <label class="form-label">Nama Orang Tua</label>
                                            <input type="text" class="form-control" name="parent_name"
                                                id="student_parent_name" maxlength="150">
                                        </div>
                                        <div class="mb-5">
                                            <label class="form-label">Email Orang Tua</label>
                                            <input type="email" class="form-control" name="parent_email"
                                                id="student_parent_email" maxlength="150">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="btnSaveStudent">Simpan</button>
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
                        <h5 class="modal-title">Hapus Siswa</h5>
                        <button type="button" class="btn btn-sm btn-icon" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Yakin ingin menghapus siswa <strong id="delete_name">-</strong>?</p>
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
            $('#students_table').DataTable({
                pageLength: 10,
                ordering: true,
            });
            // init select2 untuk kelas
            $('#student_class_id').select2({
                dropdownParent: $('#studentModal'),
                width: '100%'
            });
        });

        const btnAdd = document.getElementById('btnAddStudent');
        const form = document.getElementById('studentForm');
        const formMethod = document.getElementById('studentFormMethod');
        const inputId = document.getElementById('student_id');
        const inputFullName = document.getElementById('student_full_name');
        const inputNis = document.getElementById('student_nis');
        const selectClass = document.getElementById('student_class_id');
        const inputParentName = document.getElementById('student_parent_name');
        const inputParentEmail = document.getElementById('student_parent_email');
        const title = document.getElementById('studentModalTitle');
        const photoInput = document.getElementById('student_photo');
        const photoWrapper = document.getElementById('student_photo_wrapper');
        const inputEmail = document.getElementById('student_email');
        const inputPassword = document.getElementById('student_password');
        const defaultAvatar = '{{ asset('assets/media/svg/avatars/blank.svg') }}';

        btnAdd?.addEventListener('click', () => {
            form.action = '{{ route('teacher.students.store') }}';
            formMethod.value = 'POST';
            inputId.value = '';
            inputFullName.value = '';
            inputNis.value = '';
            $(selectClass).val('').trigger('change');
            inputParentName.value = '';
            inputParentEmail.value = '';
            title.textContent = 'Tambah Siswa';
            // account fields
            if (inputEmail) {
                inputEmail.value = '';
                inputEmail.required = true;
            }
            if (inputPassword) {
                inputPassword.value = '';
                inputPassword.required = true;
            }
            // reset photo
            if (photoWrapper) photoWrapper.style.backgroundImage = `url('${defaultAvatar}')`;
            if (photoInput) photoInput.value = '';
        });

        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const full_name = btn.getAttribute('data-full_name');
                const nis = btn.getAttribute('data-nis');
                const class_id = btn.getAttribute('data-class_id');
                const parent_name = btn.getAttribute('data-parent_name');
                const parent_email = btn.getAttribute('data-parent_email');
                const email = btn.getAttribute('data-email');
                const photo = btn.getAttribute('data-photo');

                form.action = '{{ url('teacher/students') }}/' + id;
                formMethod.value = 'PUT';
                inputId.value = id;
                inputFullName.value = full_name;
                inputNis.value = nis;
                $(selectClass).val(class_id).trigger('change');
                inputParentName.value = parent_name || '';
                inputParentEmail.value = parent_email || '';
                title.textContent = 'Edit Siswa';
                // account fields
                if (inputEmail) {
                    inputEmail.value = email || '';
                    inputEmail.required = true;
                }
                if (inputPassword) {
                    inputPassword.value = '';
                    inputPassword.required = false;
                }
                // photo preview
                if (photoWrapper) photoWrapper.style.backgroundImage = `url('${photo || defaultAvatar}')`;
                if (photoInput) photoInput.value = '';
            });
        });

        // Preview selected photo
        photoInput?.addEventListener('change', function() {
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
                deleteForm.action = '{{ url('teacher/students') }}/' + id;
                deleteName.textContent = name;
            });
        });
    </script>

    @if (session('success'))
        <script>
            (function() {
                var msg = @json(session('success'));
                if (window.toastr && toastr.success) {
                    toastr.success(msg);
                } else {
                    console.log('SUCCESS:', msg);
                }
            })();
        </script>
    @endif

    @if (session('error'))
        <script>
            (function() {
                var msg = @json(session('error'));
                if (window.toastr && toastr.error) {
                    toastr.error(msg);
                } else {
                    console.error('ERROR:', msg);
                }
            })();
        </script>
    @endif

    @if ($errors && $errors->any())
        <script>
            (function() {
                var errs = @json($errors->all());
                var msg = errs.join('\n');
                if (window.toastr && toastr.error) {
                    toastr.error(msg);
                } else {
                    console.error('ERRORS:', msg);
                }
            })();
        </script>
    @endif
@endpush
