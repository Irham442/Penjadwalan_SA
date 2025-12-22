<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScheduleGeneratorController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\BebanAjarController;
use App\Http\Controllers\Admin\RuanganController;
use App\Http\Controllers\Admin\GuruController; // TAMBAHKAN INI
use App\Http\Controllers\Admin\KelasController; // TAMBAHKAN INI
use App\Http\Controllers\Admin\MapelController; // TAMBAHKAN INI
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Admin\TahunAjaranController;
use App\Http\Controllers\Teacher\TeacherAvailabilityController; 

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {

    // Pengalihan Dashboard
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        if ($role === 'Kurikulum') return redirect()->route('admin.dashboard');
        if ($role === 'Super Admin') return redirect()->route('approval.dashboard');
        if ($role === 'guru') return redirect()->route('teacher.dashboard');
        abort(403, 'Role tidak dikenali.');
    })->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ------------------- ADMIN KURIKULUM -------------------
    Route::middleware('cekperan:Kurikulum,Super Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // --- JADWAL GENERATOR ---
        Route::post('jadwal/generate', [ScheduleGeneratorController::class, 'generate'])->name('jadwal.generate');
        Route::get('jadwal/show/{jadwal}', [ScheduleGeneratorController::class, 'show'])->name('jadwal.show');
        Route::post('jadwal/submit/{jadwal}', [DashboardController::class, 'submitForApproval'])->name('jadwal.submit');
        Route::delete('jadwal/destroy/{jadwal}', [DashboardController::class, 'destroyDraft'])->name('jadwal.destroy');
        Route::post('jadwal/regenerate/{jadwal}', [ScheduleGeneratorController::class, 'regenerate'])->name('jadwal.regenerate');
        
        // --- MASTER DATA (TAMBAHAN BARU) ---
        Route::resource('guru', GuruController::class);
        Route::resource('kelas', KelasController::class);
        Route::resource('mapel', MapelController::class);
        Route::resource('ruangan', RuanganController::class);
        Route::resource('beban-ajar', BebanAjarController::class);

        // --- MANAJEMEN TAHUN AJARAN ---
        Route::prefix('tahun-ajaran')->name('tahun_ajaran.')->group(function () {
            Route::get('/', [TahunAjaranController::class, 'index'])->name('index');
            Route::post('/store', [TahunAjaranController::class, 'store'])->name('store');
            Route::post('/activate/{id}', [TahunAjaranController::class, 'activate'])->name('activate');
            Route::delete('/{id}', [TahunAjaranController::class, 'destroy'])->name('destroy');
            Route::get('/{id}/manage-teachers', [TahunAjaranController::class, 'manageTeachers'])->name('manage_teachers');
            Route::post('/{id}/update-teachers', [TahunAjaranController::class, 'updateTeachers'])->name('update_teachers');
        });

        Route::delete('/availability/reset/{guruId}', [DashboardController::class, 'resetAvailability'])->name('availability.reset');
    });

    // ------------------- SUPER ADMIN -------------------
    Route::middleware('cekperan:Super Admin')->prefix('approval')->name('approval.')->group(function () {
        Route::get('dashboard', [ApprovalController::class, 'dashboard'])->name('dashboard');
        Route::post('approve/{jadwal}', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('reject/{jadwal}', [ApprovalController::class, 'reject'])->name('reject');
    });

    // ------------------- GURU -------------------
    Route::middleware(['auth', 'cekperan:guru'])->prefix('teacher')->name('teacher.')->group(function () {
        Route::get('dashboard', [TeacherController::class, 'index'])->name('dashboard');
    });

    Route::middleware(['auth', 'cekperan:guru'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/availability', [TeacherAvailabilityController::class, 'index'])->name('availability.index');
        Route::post('/availability', [TeacherAvailabilityController::class, 'store'])->name('availability.store');
        Route::delete('/availability/{id}', [TeacherAvailabilityController::class, 'destroy'])->name('availability.destroy');
    });

});

Route::middleware('auth')->get('/get-data-beban-ajar', [BebanAjarController::class, 'getDataBebanAjar'])->name('admin.beban-ajar.getData');

require __DIR__.'/auth.php';