<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\DusunController;
use App\Http\Controllers\Master\BulanController;
use App\Http\Controllers\Master\KolektorController;
use App\Http\Controllers\Master\TeknisiController;
use App\Http\Controllers\Master\PenagihController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Tagihan\TagihanController;
use App\Http\Controllers\Tagihan\RekapController;

Route::get('/', fn() => redirect()->route('login'));

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['role:superadmin|kolektor'])->prefix('master')->name('master.')->group(function () {
        Route::resource('pelanggan',  PelangganController::class);
        Route::resource('dusun',      DusunController::class);
        Route::resource('bulanan',    BulanController::class);

        Route::resource('teknisi',    TeknisiController::class);
        Route::resource('penagih',    PenagihController::class);
        
        Route::middleware(['role:superadmin'])->group(function () {
            Route::resource('kolektor',   KolektorController::class);
            Route::resource('users',      UserController::class);
        });
    });

    Route::middleware(['role:superadmin|kolektor'])->group(function () {
        Route::get('/tagihan/rekap',      [RekapController::class, 'index'])->name('tagihan.rekap');
        Route::post('/tagihan/lunas-banyak', [TagihanController::class, 'lunaskanBanyak'])->name('tagihan.lunas-banyak');
        Route::get('/tagihan/rekap/export', [RekapController::class, 'export'])->name('tagihan.rekap.export')
             ->middleware('role:superadmin');
        Route::post('/tagihan/{tagihan}/lunas-cepat', [TagihanController::class, 'lunaskanCepat'])->name('tagihan.lunas-cepat');
        Route::post('/tagihan/{tagihan}/batal-lunas', [TagihanController::class, 'batalLunas'])->name('tagihan.batal-lunas');
        Route::resource('tagihan',      TagihanController::class);
    });
});
