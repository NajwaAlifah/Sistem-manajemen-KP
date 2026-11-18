@extends('layouts.app')

@section('title', 'Dashboard Admin - Manajemen KP')

@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Kelola pengguna, kategori, dan sistem')

@section('content')
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon total-users">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Total Pengguna</p>
                    <p class="stat-value" id="totalUsers">0</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon total-docs">
                    <i class="bi bi-files"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Total Dokumen</p>
                    <p class="stat-value" id="totalDocs">0</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon pending-docs">
                    <i class="bi bi-hourglass"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Pending Review</p>
                    <p class="stat-value" id="pendingDocs">0</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon approved-docs">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-content">
                    <p class="stat-label">Disetujui</p>
                    <p class="stat-value" id="approvedDocs">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#users">
                <i class="bi bi-people"></i> Pengguna
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#categories">
                <i class="bi bi-tags"></i> Kategori
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#archive">
                <i class="bi bi-archive"></i> Arsip
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Users Tab -->
        <div id="users" class="tab-pane fade show active">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Manajemen Pengguna</h5>
                    <button class="btn btn-primary btn-sm" onclick="openUserModal()">
                        <i class="bi bi-plus-circle"></i> Tambah Pengguna
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Bergabung</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="usersBody">
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Tab -->
        <div id="categories" class="tab-pane fade">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Manajemen Kategori</h5>
                    <button class="btn btn-primary btn-sm" onclick="openCategoryModal()">
                        <i class="bi bi-plus-circle"></i> Tambah Kategori
                    </button>
                </div>
                <div class="card-body">
                    <div class="row" id="categoriesGrid">
                        <!-- Filled by JS -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Archive Tab -->
        <div id="archive" class="tab-pane fade">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Dokumen Terarsip</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Dokumen</th>
                                    <th>Mahasiswa</th>
                                    <th>Diarsipkan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="archiveBody">
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')
    @include('modals.user-form')
    @include('modals.category-form')
@endsection

@section('extra-css')
    <link rel="stylesheet" href="{{ asset('css/dashboard-admin.css') }}">
@endsection

@section('extra-js')
    <script src="{{ asset('js/dashboard-admin.js') }}"></script>
@endsection
