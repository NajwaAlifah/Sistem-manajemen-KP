<?php

use App\Http\Controllers\{AdminController, AuthController, CompanyController, DocumentController, GradeController, GuidanceController, InfoController, KpFlowController, LogbookController, PageController};
use Illuminate\Support\Facades\Route;

// Diagnosa aset (hanya saat APP_DEBUG=true): buka /_cek
Route::get('/_cek', function () {
    abort_unless(config('app.debug'), 404);
    $files = ['css/bootstrap.min.css', 'css/bootstrap-icons.min.css', 'js/bootstrap.bundle.min.js', 'css/siakad.css', 'js/app.js', 'js/kp-pages.js', 'js/admin-pages.js', 'logo.png'];
    return response()->json(['laravel' => app()->version(), 'php' => PHP_VERSION, 'url' => url('/'), 'public_path' => public_path(),
        'files' => collect($files)->mapWithKeys(fn ($f) => [$f => file_exists(public_path($f)) ? filesize(public_path($f)) . ' bytes' : 'TIDAK ADA'])]);
});

// Publik
Route::get('/login', fn () => view('pages.login'))->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest')->name('login.attempt');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn () => redirect('/dashboard/' . auth()->user()->role))->name('dashboard');
    Route::get('/dashboard/mahasiswa', fn () => view('pages.dashboard-mahasiswa'))->middleware('admin:mahasiswa')->name('dashboard.mahasiswa');
    Route::get('/dashboard/dosen', fn () => view('pages.dashboard-dosen'))->middleware('admin:dosen')->name('dashboard.dosen');
    Route::get('/dashboard/lapangan', [PageController::class, 'lapangan'])->middleware('admin:lapangan')->name('dashboard.lapangan');
    Route::get('/dashboard/admin', [PageController::class, 'admin'])->middleware('admin')->name('dashboard.admin');

    // Dokumen (lapangan tidak mengakses dokumen)
    Route::middleware('admin:mahasiswa,dosen,admin')->group(function () {
        Route::get('/documents', fn () => redirect()->route('dashboard'))->name('documents.index');
        Route::get('/documents/{id}', [PageController::class, 'show'])->whereNumber('id')->name('documents.show');
        Route::get('/documents/{id}/history', fn ($id) => redirect()->route('documents.show', ['id' => $id, 'tab' => 'versions']))->whereNumber('id')->name('documents.history');
        Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->whereNumber('id')->name('documents.download');
        Route::get('/archive', [PageController::class, 'archive'])->name('archive.index');
    });

    // Mahasiswa
    Route::middleware('admin:mahasiswa')->group(function () {
        Route::get('/kp/pengajuan', [KpFlowController::class, 'pengajuan'])->name('kp.pengajuan');
        Route::post('/kp/pengajuan', [KpFlowController::class, 'storePengajuan'])->name('kp.store');
        Route::get('/kp/progress', [KpFlowController::class, 'progress'])->name('kp.progress');
        Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
        Route::put('/logbook/{id}', [LogbookController::class, 'update'])->name('logbook.update');
        Route::delete('/logbook/{id}', [LogbookController::class, 'destroy'])->name('logbook.destroy');
        Route::post('/bimbingan', [GuidanceController::class, 'store'])->name('bimbingan.store');
        Route::post('/bimbingan/{id}/lampiran', [GuidanceController::class, 'lampiran'])->name('bimbingan.lampiran');
    });

    // Logbook, bimbingan, nilai, monitoring (per peran, dibatasi di controller)
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook.index');
    Route::get('/logbook/{id}/file', [LogbookController::class, 'file'])->name('logbook.file');
    Route::post('/logbook/{id}/validasi', [LogbookController::class, 'validateLog'])->middleware('admin:lapangan')->name('logbook.validasi');
    Route::get('/bimbingan', [GuidanceController::class, 'index'])->middleware('admin:mahasiswa,dosen,admin')->name('bimbingan.index');
    Route::post('/bimbingan/{id}/respon', [GuidanceController::class, 'respond'])->middleware('admin:dosen')->name('bimbingan.respon');
    Route::get('/bimbingan/{id}/lampiran', [GuidanceController::class, 'download'])->name('bimbingan.download');
    Route::get('/nilai', [GradeController::class, 'index'])->name('nilai.index');
    Route::post('/nilai', [GradeController::class, 'store'])->middleware('admin:dosen,lapangan')->name('nilai.store');
    Route::post('/nilai/bobot', [GradeController::class, 'bobot'])->middleware('admin')->name('nilai.bobot');
    Route::post('/nilai/{id}/publikasi', [GradeController::class, 'publish'])->middleware('admin')->name('nilai.publikasi');
    Route::get('/monitoring', [KpFlowController::class, 'monitoring'])->middleware('admin:dosen,lapangan,admin')->name('monitoring.index');
    Route::post('/monitoring/{id}/konfirmasi', [KpFlowController::class, 'konfirmasi'])->middleware('admin:lapangan')->name('monitoring.konfirmasi');

    // Notifikasi & pengumuman
    Route::get('/notifications', [InfoController::class, 'notifications'])->name('notifications.index');
    Route::post('/notifications/read-all', [InfoController::class, 'readAll'])->name('notifications.readall');
    Route::get('/notifications/{id}/go', [InfoController::class, 'go'])->name('notifications.go');
    Route::get('/pengumuman', [InfoController::class, 'announcements'])->name('announcements.index');

    // Admin Prodi
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/users', [PageController::class, 'adminUsers'])->name('admin.users');
        Route::get('/categories', [PageController::class, 'adminCategories'])->name('admin.categories');
        Route::get('/kp', [PageController::class, 'adminKp'])->name('admin.kp');
        Route::get('/companies', [CompanyController::class, 'index'])->name('admin.companies');
        Route::get('/audit', [InfoController::class, 'audit'])->name('admin.audit');
        Route::get('/settings', [InfoController::class, 'settings'])->name('admin.settings');
        Route::post('/settings', [InfoController::class, 'saveSettings'])->name('admin.settings.save');
        Route::get('/export/{type}', [KpFlowController::class, 'export'])->name('admin.export');
    });

    Route::prefix('settings')->group(function () {
        Route::view('/profile', 'pages.settings.profile')->name('settings.profile');
        Route::post('/profile', [AuthController::class, 'updateProfile']);
        Route::view('/password', 'pages.settings.password')->name('settings.password');
        Route::post('/password', [AuthController::class, 'updatePassword']);
    });
    Route::view('/help', 'pages.help')->name('help');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // API (session + CSRF)
    Route::prefix('api')->group(function () {
        Route::get('/user/profile', fn () => response()->json(['success' => true, 'user' => auth()->user()]));
        Route::get('/documents', [DocumentController::class, 'index']);
        Route::post('/documents', [DocumentController::class, 'store']);
        Route::get('/documents/{id}', [DocumentController::class, 'show']);
        Route::get('/documents/{id}/versions', [DocumentController::class, 'versions']);
        Route::put('/documents/{id}', [DocumentController::class, 'update']);
        Route::post('/documents/{id}/replace', [DocumentController::class, 'update']);
        Route::delete('/documents/{id}', [DocumentController::class, 'destroy']);
        Route::post('/documents/{id}/submit', [DocumentController::class, 'submit']);
        Route::post('/documents/{id}/feedback', [DocumentController::class, 'feedback']);
        Route::put('/documents/{id}/approve', [DocumentController::class, 'approve']);
        Route::get('/categories', [AdminController::class, 'categories']);

        Route::middleware('admin')->group(function () {
            Route::get('/stats', [AdminController::class, 'stats']);
            Route::get('/users', [AdminController::class, 'users']);
            Route::post('/users', [AdminController::class, 'storeUser']);
            Route::put('/users/{user}', [AdminController::class, 'updateUser']);
            Route::delete('/users/{user}', [AdminController::class, 'destroyUser']);
            Route::post('/categories', [AdminController::class, 'storeCategory']);
            Route::put('/categories/{category}', [AdminController::class, 'updateCategory']);
            Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory']);
            Route::post('/kp', [AdminController::class, 'storeKp']);
            Route::put('/kp/{kp}', [AdminController::class, 'updateKp']);
            Route::delete('/kp/{kp}', [AdminController::class, 'destroyKp']);
            Route::post('/companies', [CompanyController::class, 'store']);
            Route::put('/companies/{company}', [CompanyController::class, 'update']);
            Route::delete('/companies/{company}', [CompanyController::class, 'destroy']);
            Route::post('/announcements', [InfoController::class, 'storeAnnouncement']);
            Route::put('/announcements/{announcement}', [InfoController::class, 'updateAnnouncement']);
            Route::delete('/announcements/{announcement}', [InfoController::class, 'destroyAnnouncement']);
        });
    });
});

Route::get('/', fn () => redirect(auth()->check() ? '/dashboard' : '/login'));
