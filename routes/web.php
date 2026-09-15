<?php

use App\Http\Controllers\Admin\MonitoringController as AdminMonitoringController;
use App\Http\Controllers\Admin\VerifikasiController as AdminVerifikasiController;
use App\Http\Controllers\Calon\BerkasController;
use App\Http\Controllers\Calon\DashboardController as CalonDashboardController;
use App\Http\Controllers\Calon\LamaranController;
use App\Http\Controllers\Calon\LogbookController;
use App\Http\Controllers\Calon\LowonganController;
use App\Http\Controllers\Calon\VerifikasiController as CalonVerifikasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Firm\DashboardController as FirmDashboardController;
use App\Http\Controllers\Firm\LogbookController as FirmLogbookController;
use App\Http\Controllers\Firm\PelamarController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/up', function () {
    return response()->noContent();
});

Route::get('/', HomeController::class);

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:calon_advokat'])->prefix('calon')->name('calon.')->group(function () {
    Route::get('/dashboard', [CalonDashboardController::class, 'index'])->name('dashboard');
    Route::get('/lowongan', [LowonganController::class, 'index'])->name('lowongan');
    Route::post('/lowongan/{jobPosting}/lamar', [LowonganController::class, 'lamar'])->name('lowongan.lamar');
    Route::get('/lamaran', [LamaranController::class, 'index'])->name('lamaran');
    Route::get('/logbook', [LogbookController::class, 'index'])->name('logbook');
    Route::post('/logbook', [LogbookController::class, 'store'])->name('logbook.store');
    Route::get('/berkas', [BerkasController::class, 'index'])->name('berkas');
    Route::get('/verifikasi', [CalonVerifikasiController::class, 'index'])->name('verifikasi');
    Route::post('/verifikasi/{verificationChecklist}/upload', [CalonVerifikasiController::class, 'upload'])->name('verifikasi.upload');
});

Route::middleware(['auth', 'role:law_firm'])->prefix('firm')->name('firm.')->group(function () {
    Route::get('/dashboard', [FirmDashboardController::class, 'index'])->name('dashboard');
    Route::get('/pelamar', [PelamarController::class, 'index'])->name('pelamar');
    Route::post('/pelamar/{internshipApplication}/terima', [PelamarController::class, 'terima'])->name('pelamar.terima');
    Route::get('/logbook', [FirmLogbookController::class, 'index'])->name('logbook');
    Route::post('/logbook/{entry}/setujui', [FirmLogbookController::class, 'setujui'])->name('logbook.setujui');
    Route::post('/logbook/{entry}/revisi', [FirmLogbookController::class, 'revisi'])->name('logbook.revisi');
    Route::post('/logbook/rekap/{monthlyLogbookSummary}/tandatangani-semua', [FirmLogbookController::class, 'tandatanganiSemua'])->name('logbook.tandatangani-semua');
});

Route::middleware(['auth', 'role:admin_dpc'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/verifikasi', [AdminVerifikasiController::class, 'index'])->name('verifikasi');
    Route::post('/verifikasi/calon/{candidateAdvocate}/setujui', [AdminVerifikasiController::class, 'setujuiCalon'])->name('verifikasi.calon.setujui');
    Route::post('/verifikasi/firm/{lawFirm}/tetapkan-kuota', [AdminVerifikasiController::class, 'tetapkanKuotaFirm'])->name('verifikasi.firm.tetapkan-kuota');
    Route::get('/monitoring', [AdminMonitoringController::class, 'index'])->name('monitoring');
});

require __DIR__.'/auth.php';
