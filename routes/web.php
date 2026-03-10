<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\ApprovalController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\UserController;

// --- ROUTE PUBLIC ---
Route::get('/', [\App\Http\Controllers\FrontController::class, 'index'])->name('home');
// Route::redirect('/', '/login');

// --- ROUTE AUTENTIKASI ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- ROUTE ADMIN (Wajib Login) ---
Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/cek-nik', [AnggotaController::class, 'checkNik'])->name('anggota.checkNik');
    Route::get('/get-cities', [AnggotaController::class, 'getCities'])->name('getCities');
    Route::get('/get-districts', [AnggotaController::class, 'getDistricts'])->name('getDistricts');

    Route::prefix('anggota')->name('anggota.')->controller(AnggotaController::class)->group(function () {
        Route::get('/import', 'importForm')->name('importForm');
        Route::post('/import', 'importData')->name('importData');
        Route::get('/data', 'data')->name('data');
        Route::get('/export-kta', 'exportKta')->name('exportKta');
        Route::get('/{id}/print', 'printKta')->name('print');
        Route::post('/{id}/pengurus', 'storePengurus')->name('storePengurus');
        Route::post('/pengurus/{id}/nonaktifkan', 'nonaktifkanPengurus')->name('nonaktifkanPengurus');
    });

    Route::resource('anggota', AnggotaController::class);

    // Route Khusus Approval (Hanya bisa diakses PW dan PP)
    Route::prefix('approval')->name('approval.')->middleware('role:Admin PW,Admin PP')->controller(ApprovalController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/member/{id}/approve', 'approveMember')->name('member.approve');
        Route::post('/member/{id}/reject', 'rejectMember')->name('member.reject');
        Route::post('/draft/{id}/approve', 'approveDraft')->name('draft.approve');
        Route::post('/draft/{id}/reject', 'rejectDraft')->name('draft.reject');
    });

    // Manajemen Akun Admin
    Route::resource('users', UserController::class)->except(['show', 'edit', 'update']);

    // --- ROUTE PROFIL ADMIN ---
    Route::prefix('profil')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/update', 'update')->name('update');
    });
});
