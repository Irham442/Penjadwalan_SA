<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ScheduleGeneratorController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\BebanAjarController;
use App\Http\Controllers\Admin\RuanganController;


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
    // Ini adalah 'gerbang pintar' yang mengarahkan user setelah login.
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        if ($role === 'Kurikulum') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'Super Admin') {
            return redirect()->route('approval.dashboard');
        }

        // Untuk peran lain (misal: 'Guru'), tampilkan dashboard biasa
        return view('dashboard');
    })->middleware('verified')->name('dashboard');


    // Rute Profil Pengguna (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    // ------------------- GRUP RUTE BERDASARKAN PERAN -------------------

    // Rute Khusus Admin Kurikulum
    Route::middleware('cekperan:Kurikulum,Super Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('jadwal/generate', [ScheduleGeneratorController::class, 'generate'])->name('jadwal.generate');
        Route::get('jadwal/show/{jadwal}', [ScheduleGeneratorController::class, 'show'])->name('jadwal.show');
        Route::post('jadwal/submit/{jadwal}', [DashboardController::class, 'submitForApproval'])->name('jadwal.submit');
        Route::delete('jadwal/destroy/{jadwal}', [DashboardController::class, 'destroyDraft'])->name('jadwal.destroy');
        Route::post('jadwal/regenerate/{jadwal}', [ScheduleGeneratorController::class, 'regenerate'])->name('jadwal.regenerate');
        Route::resource('beban-ajar', BebanAjarController::class);
        Route::resource('ruangan', RuanganController::class);
        Route::resource('beban-ajar', BebanAjarController::class);
    });

    // Rute Khusus Super Admin (Persetujuan)
    Route::middleware('cekperan:Super Admin')->prefix('approval')->name('approval.')->group(function () {
        Route::get('dashboard', [ApprovalController::class, 'dashboard'])->name('dashboard');
        Route::post('approve/{jadwal}', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('reject/{jadwal}', [ApprovalController::class, 'reject'])->name('reject');
    });

});


// File otentikasi Breeze
require __DIR__.'/auth.php';