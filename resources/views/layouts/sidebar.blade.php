@php
    $role = auth()->user()->role ?? null;
@endphp

<div class="app-sidebar-menu overflow-hidden flex-column-fluid">
    <!--begin::Menu wrapper-->
    <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper">
        <!--begin::Scroll wrapper-->
        <div id="kt_app_sidebar_menu_scroll"
             class="scroll-y my-5 mx-3"
             data-kt-scroll="true"
             data-kt-scroll-activate="true"
             data-kt-scroll-height="auto"
             data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer"
             data-kt-scroll-save-state="true">

            <!--begin::Menu-->
            <div class="menu menu-column menu-rounded menu-sub-indention fw-semibold fs-6"
                 id="#kt_app_sidebar_menu"
                 data-kt-menu="true"
                 data-kt-menu-expand="false">

                {{-- ========================= --}}
                {{-- UMUM (SEMUA ROLE)        --}}
                {{-- ========================= --}}
                <div class="menu-item">
                    <a class="menu-link {{ request()->is('/') || request()->routeIs('dashboard') ? 'active' : '' }}"
                       href="{{ route('dashboard') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-element-11 fs-2">
                                <span class="path1"></span><span class="path2"></span>
                                <span class="path3"></span><span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">Dashboard</span>
                    </a>
                </div>

                {{-- ========================= --}}
                {{-- ROLE: ADMIN               --}}
                {{-- ========================= --}}
                @if($role === 'admin')
                    <!-- MASTER DATA SECTION -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Master Data
                            </span>
                        </div>
                    </div>

                    <!-- Angkatan -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('batches.*') ? 'active' : '' }}"
                           href="{{ route('batches.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-calendar-8 fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Angkatan</span>
                        </a>
                    </div>

                    <!-- Kelas -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('classes.*') ? 'active' : '' }}"
                           href="{{ route('classes.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-element-2 fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Kelas</span>
                        </a>
                    </div>

                    <!-- Siswa -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('students.*') ? 'active' : '' }}"
                           href="{{ route('students.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-profile-circle fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Siswa</span>
                        </a>
                    </div>

                    <!-- KEHADIRAN -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Kehadiran
                            </span>
                        </div>
                    </div>

                    <!-- Input Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('attendances.create') ? 'active' : '' }}"
                           href="{{ route('attendances.create') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-notepad-edit fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Input Kehadiran</span>
                        </a>
                    </div>

                    <!-- Riwayat Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('attendances.index') ? 'active' : '' }}"
                           href="{{ route('attendances.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-time fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Riwayat Kehadiran</span>
                        </a>
                    </div>

                    <!-- LAPORAN & NOTIFIKASI -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Laporan & Notifikasi
                            </span>
                        </div>
                    </div>

                    <!-- Laporan Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}"
                           href="{{ route('reports.attendance') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-graph-3 fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">Laporan Kehadiran</span>
                        </a>
                    </div>

                    <!-- Log Notifikasi -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('notification-logs.*') ? 'active' : '' }}"
                           href="{{ route('notification-logs.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-sms fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Log Notifikasi</span>
                        </a>
                    </div>
                @endif

                {{-- ========================= --}}
                {{-- ROLE: GURU (TEACHER)      --}}
                {{-- ========================= --}}
                @if($role === 'teacher')
                    <!-- KEHADIRAN -->
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Kehadiran Siswa
                            </span>
                        </div>
                    </div>

                    <!-- Input Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('attendances.create') ? 'active' : '' }}"
                           href="{{ route('attendances.create') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-notepad-edit fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Input Kehadiran</span>
                        </a>
                    </div>

                    <!-- Riwayat Kehadiran -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('attendances.index') ? 'active' : '' }}"
                           href="{{ route('attendances.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-time fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">Riwayat Kehadiran</span>
                        </a>
                    </div>

                    <!-- Laporan Kehadiran (opsional untuk guru) -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('reports.attendance') ? 'active' : '' }}"
                           href="{{ route('reports.attendance') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-graph-3 fs-2">
                                    <span class="path1"></span><span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">Laporan Kehadiran</span>
                        </a>
                    </div>
                @endif

                {{-- ========================= --}}
                {{-- ROLE: SISWA (STUDENT)     --}}
                {{-- ========================= --}}
                @if($role === 'student')
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-section text-muted text-uppercase fs-8 ls-1">
                                Kehadiran Saya
                            </span>
                        </div>
                    </div>

                    <!-- Riwayat Kehadiran Siswa -->
                    <div class="menu-item">
                        <a class="menu-link {{ request()->routeIs('attendances.index') || request()->routeIs('student.attendance') ? 'active' : '' }}"
                           hre
