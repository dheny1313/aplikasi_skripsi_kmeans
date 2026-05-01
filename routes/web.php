<?php

use App\Http\Controllers\Admin\StudentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// -------------------------------------------------------------------
// AREA ADMIN
// -------------------------------------------------------------------
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    // URL: domain.com/admin/dashboard

    Route::post('/kmeans/sync-normalization', [\App\Http\Controllers\Admin\KMeansController::class, 'syncNormalization'])->name('admin.kmeans.sync');


    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    // Rute Master Pengguna (User Management)
    Route::resource('user', \App\Http\Controllers\Admin\UserController::class)->except(['show'])->names('admin.user');

    // Nanti route kelola user, master siswa, dll kita taruh di sini
    Route::get('/student', [StudentController::class, 'index'])->name('admin.student.index');
    Route::post('/student', [StudentController::class, 'store'])->name('admin.student.store');
    Route::put('/student/{id}', [StudentController::class, 'update'])->name('admin.student.update');
    Route::patch('/student/{id}/toggle', [StudentController::class, 'toggleStatus'])->name('admin.student.toggle');

    // Tambahkan route Import ini:
    Route::post('/student/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('admin.student.import');

    // Rute Master Data Kriteria
    Route::get('/criteria', [\App\Http\Controllers\Admin\CriterionController::class, 'index'])->name('admin.criteria.index');
    Route::post('/criteria', [\App\Http\Controllers\Admin\CriterionController::class, 'store'])->name('admin.criteria.store');
    Route::put('/criteria/{id}', [\App\Http\Controllers\Admin\CriterionController::class, 'update'])->name('admin.criteria.update');
    Route::delete('/criteria/{id}', [\App\Http\Controllers\Admin\CriterionController::class, 'destroy'])->name('admin.criteria.destroy');

    // Rute Manajemen Nilai (Admin Takeover)
    Route::get('/score', [\App\Http\Controllers\Admin\ScoreController::class, 'index'])->name('admin.score.index');
    Route::post('/score/import', [\App\Http\Controllers\Admin\ScoreController::class, 'import'])->name('admin.score.import');
    Route::get('/score/{student_id}', [\App\Http\Controllers\Admin\ScoreController::class, 'edit'])->name('admin.score.edit');
    Route::put('/score/{student_id}', [\App\Http\Controllers\Admin\ScoreController::class, 'update'])->name('admin.score.update');
    Route::get('/score/detail/{student_id}', [\App\Http\Controllers\Admin\ScoreController::class, 'show'])->name('admin.score.show');

    // Rute Algoritma K-Means & Evaluasi
    Route::get('/kmeans', [\App\Http\Controllers\Admin\KMeansController::class, 'index'])->name('admin.kmeans.index');
    Route::get('/kmeans/elbow', [\App\Http\Controllers\Admin\KMeansController::class, 'runElbow'])->name('admin.kmeans.elbow'); // API untuk Grafik
    Route::post('/kmeans/calculate', [\App\Http\Controllers\Admin\KMeansController::class, 'calculate'])->name('admin.kmeans.calculate');
    Route::get('/kmeans/result/{id}', [\App\Http\Controllers\Admin\KMeansController::class, 'showResult'])->name('admin.kmeans.result');


    // --- MANAJEMEN LAPORAN ---
    Route::get('/report', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.report.index');
    Route::get('/report/{log_id}/excel', [\App\Http\Controllers\Admin\ReportController::class, 'exportExcel'])->name('admin.report.excel');
    Route::get('/report/{log_id}/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('admin.report.pdf');
});

// -------------------------------------------------------------------
// AREA TEACHER
// -------------------------------------------------------------------
Route::middleware(['auth', 'role:teacher'])->prefix('teacher')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('teacher.dashboard');
    // URL: domain.com/teacher/dashboard
    //Route::get('/dashboard', function () {
    //  return view(
    //    'teacher.dashboard'
    //);
    //})->name('teacher.dashboard');

    // Nanti route input nilai siswa kita taruh di sini
    // Rute Manajemen Nilai Siswa
    Route::get('/score', [\App\Http\Controllers\Teacher\ScoreController::class, 'index'])->name('teacher.score.index');
    Route::get('/score/{student_id}', [\App\Http\Controllers\Teacher\ScoreController::class, 'edit'])->name('teacher.score.edit');
    Route::put('/score/{student_id}', [\App\Http\Controllers\Teacher\ScoreController::class, 'update'])->name('teacher.score.update');
    // --- Laporan K-Means (Hanya Baca) ---
    Route::get('/kmeans', [\App\Http\Controllers\Teacher\KMeansController::class, 'index'])->name('teacher.kmeans.index');
    Route::get('/kmeans/result/{log_id}', [\App\Http\Controllers\Teacher\KMeansController::class, 'showResult'])->name('teacher.kmeans.result');
});
