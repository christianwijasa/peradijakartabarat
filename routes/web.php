<?php

use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\VerifikasiController as AdminVerifikasiController;
use App\Http\Controllers\Calon\BerkasController;
use App\Http\Controllers\Calon\DashboardController as CalonDashboardController;
use App\Http\Controllers\Calon\LamaranController;
use App\Http\Controllers\Calon\LogbookController;
use App\Http\Controllers\Calon\LowonganController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Firm\DashboardController as FirmDashboardController;
use App\Http\Controllers\Firm\LogbookController as FirmLogbookController;
use App\Http\Controllers\Firm\LowonganController as FirmLowonganController;
use App\Http\Controllers\Firm\PelamarController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:calon_advokat'])->prefix('calon')->name('calon.')->group(function () {
    Route::get('/dashboard', [CalonDashboardController::class, 'index'])->name('dashboard');
    Route::get('/lowongan', [LowonganController::class, 'index'])->name('lowongan');
    Route::post('/lowongan/{lowongan}/lamar', [LowonganController::class, 'lamar'])->name('lowongan.lamar');
    Route::get('/lamaran', [LamaranController::class, 'index'])->name('lamaran');
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
    Route::patch('/logbook/{id}', [LogbookController::class, 'update'])->name('logbook.update');
    Route::get('/berkas', [BerkasController::class, 'index'])->name('berkas');
    Route::post('/berkas/{berkas}/upload', [BerkasController::class, 'upload'])->name('berkas.upload');
    Route::get('/berkas/{berkas}/download', [BerkasController::class, 'download'])->name('berkas.download');
});

Route::middleware(['auth', 'role:law_firm'])->prefix('firm')->name('firm.')->group(function () {
    Route::get('/dashboard', [FirmDashboardController::class, 'index'])->name('dashboard');
    Route::get('/lowongan', [FirmLowonganController::class, 'index'])->name('lowongan.index');
    Route::get('/lowongan/create', [FirmLowonganController::class, 'create'])->name('lowongan.create');
    Route::post('/lowongan', [FirmLowonganController::class, 'store'])->name('lowongan.store');
    Route::get('/lowongan/{lowongan}/edit', [FirmLowonganController::class, 'edit'])->name('lowongan.edit');
    Route::patch('/lowongan/{lowongan}', [FirmLowonganController::class, 'update'])->name('lowongan.update');
    Route::post('/lowongan/{lowongan}/toggle', [FirmLowonganController::class, 'toggleStatus'])->name('lowongan.toggle');
    Route::get('/pelamar', [PelamarController::class, 'index'])->name('pelamar');
    Route::post('/pelamar/{lamaran}/terima', [PelamarController::class, 'terima'])->name('pelamar.terima');
    Route::post('/pelamar/{lamaran}/status', [PelamarController::class, 'updateStatus'])->name('pelamar.status');
    Route::get('/logbook', [FirmLogbookController::class, 'index'])->name('logbook');
    Route::post('/logbook/{entry}/setujui', [FirmLogbookController::class, 'setujui'])->name('logbook.setujui');
    Route::post('/logbook/{entry}/revisi', [FirmLogbookController::class, 'revisi'])->name('logbook.revisi');
    Route::post('/logbook/rekap/{rekap}/tandatangani-semua', [FirmLogbookController::class, 'tandatanganiSemua'])->name('logbook.tandatangani-semua');
});

Route::middleware(['auth', 'role:admin_dpc'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/verifikasi', [AdminVerifikasiController::class, 'index'])->name('verifikasi');
    Route::post('/verifikasi/calon/{calonAdvokat}/setujui', [AdminVerifikasiController::class, 'setujuiCalon'])->name('verifikasi.calon.setujui');
    Route::post('/verifikasi/firm/{lawFirm}/tetapkan-kuota', [AdminVerifikasiController::class, 'tetapkanKuotaFirm'])->name('verifikasi.firm.tetapkan-kuota');
    Route::get('/monitoring', [AdminMonitoringController::class, 'index'])->name('monitoring');
    Route::post('/monitoring/audit/{calonAdvokat}', [AdminMonitoringController::class, 'setAuditStatus'])->name('monitoring.audit');
});

require __DIR__.'/auth.php';
