<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScheduleGeneratorController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\BebanAjarController;
use App\Http\Controllers\Admin\RuanganController;
use App\Http\Controllers\Teacher\TeacherController; // Pastikan namespace Teacher
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Teacher\TeacherAvailabilityController; 


/*
|--------------------------------------------------------------------------
| Rute Publik
|--------------------------------------------------------------------------
|
| Rute yang bisa diakses oleh siapa saja tanpa perlu login.
|
*/

Route::get('/', function () {
    return view('welcome');
});


/*
|--------------------------------------------------------------------------
| Rute yang Membutuhkan Autentikasi
|--------------------------------------------------------------------------
|
| Semua rute di dalam grup ini hanya bisa diakses setelah user login.
|
*/

Route::middleware('auth')->group(function () {

    // Rute Pengalihan Dashboard Utama
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        if ($role === 'Kurikulum') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'Super Admin') {
            return redirect()->route('approval.dashboard');
        }
        if ($role === 'guru') {
            return redirect()->route('teacher.dashboard');
        }

        // Jika tidak ada role yang cocok, throw 403
        abort(403, 'Role tidak dikenali.');
    })->middleware('verified')->name('dashboard');


    // Rute Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ------------------- GRUP RUTE BERDASARKAN PERAN -------------------

    // 1. Rute Khusus Admin Kurikulum
    Route::middleware('cekperan:Kurikulum,Super Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('jadwal/generate', [ScheduleGeneratorController::class, 'generate'])->name('jadwal.generate');
        Route::get('jadwal/show/{jadwal}', [ScheduleGeneratorController::class, 'show'])->name('jadwal.show');
        Route::post('jadwal/submit/{jadwal}', [DashboardController::class, 'submitForApproval'])->name('jadwal.submit');
        Route::delete('jadwal/destroy/{jadwal}', [DashboardController::class, 'destroyDraft'])->name('jadwal.destroy');
        Route::post('jadwal/regenerate/{jadwal}', [ScheduleGeneratorController::class, 'regenerate'])->name('jadwal.regenerate');
        Route::resource('beban-ajar', BebanAjarController::class);
        Route::resource('ruangan', RuanganController::class);
        Route::delete('/availability/reset/{guruId}', [DashboardController::class, 'resetAvailability'])->name('availability.reset');
        
        // Manajemen Tahun Ajaran & Guru Aktif
        Route::prefix('tahun-ajaran')->name('tahun_ajaran.')->group(function () {
            Route::get('/', [TahunAjaranController::class, 'index'])->name('index');
            Route::post('/store', [TahunAjaranController::class, 'store'])->name('store');
            Route::post('/activate/{id}', [TahunAjaranController::class, 'activate'])->name('activate');
            Route::delete('/{id}', [TahunAjaranController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/manage-teachers', [TahunAjaranController::class, 'manageTeachers'])->name('manage_teachers');
            Route::post('/{id}/update-teachers', [TahunAjaranController::class, 'updateTeachers'])->name('update_teachers');
        });
    });

    // 2. Rute Khusus Super Admin (Persetujuan)
    Route::middleware('cekperan:Super Admin')->prefix('approval')->name('approval.')->group(function () {
        Route::get('dashboard', [ApprovalController::class, 'dashboard'])->name('dashboard');
        Route::post('approve/{jadwal}', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('reject/{jadwal}', [ApprovalController::class, 'reject'])->name('reject');
    });

    // 3. Rute Khusus GURU (Dashboard Utama)
    Route::middleware(['auth', 'cekperan:guru'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('dashboard', [TeacherController::class, 'index'])->name('dashboard');
    });

    // 4. Rute Khusus GURU (Fitur Availability / Ketersediaan)
    Route::middleware(['auth', 'cekperan:guru'])->prefix('guru')->name('guru.')->group(function () {
        // Halaman List & Form (GET) -> nama route: guru.availability.index
        Route::get('/availability', [TeacherAvailabilityController::class, 'index'])->name('availability.index');
        
        // Proses Simpan (POST) -> nama route: guru.availability.store
        Route::post('/availability', [TeacherAvailabilityController::class, 'store'])->name('availability.store');
        
        // Proses Hapus (DELETE) -> nama route: guru.availability.destroy
        Route::delete('/availability/{id}', [TeacherAvailabilityController::class, 'destroy'])->name('availability.destroy');
    });

});

// Rute AJAX Beban Ajar (Di luar group middleware peran, tapi tetap auth)
Route::middleware('auth')->get('/get-data-beban-ajar', [\App\Http\Controllers\Admin\BebanAjarController::class, 'getDataBebanAjar'])->name('admin.beban-ajar.getData');

require __DIR__.'/auth.php';