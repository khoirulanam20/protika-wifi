<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\PelangganController;
use App\Http\Controllers\Master\DusunController;
use App\Http\Controllers\Master\BulanController;
use App\Http\Controllers\Master\KolektorController;
use App\Http\Controllers\Master\AdminDesaController;
use App\Http\Controllers\Master\TeknisiController;
use App\Http\Controllers\Master\PenagihController;
use App\Http\Controllers\Master\UserController;
use App\Http\Controllers\Tagihan\TagihanController;
use App\Http\Controllers\Tagihan\RekapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\WilayahController;

Route::get('/', fn() => redirect()->route('login'));

// API Wilayah Routes
Route::prefix('api/wilayah')->middleware(['auth'])->group(function () {
    Route::get('/provinces', [WilayahController::class, 'provinces']);
    Route::get('/regencies/{province}', [WilayahController::class, 'regencies']);
    Route::get('/districts/{regency}', [WilayahController::class, 'districts']);
    Route::get('/villages/{district}', [WilayahController::class, 'villages']);
});

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware(['role:superadmin|kolektor|admin_desa'])
        ->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::middleware(['role:superadmin|kolektor|admin_desa'])->prefix('master')->name('master.')->group(function () {
        Route::resource('pelanggan',  PelangganController::class);
        Route::post('pelanggan/{pelanggan}/nonaktif', [PelangganController::class, 'nonaktifkan'])->name('pelanggan.nonaktif');
        Route::post('pelanggan/{pelanggan}/aktifkan', [PelangganController::class, 'aktifkan'])->name('pelanggan.aktifkan');
        Route::resource('dusun',      DusunController::class);
        Route::resource('bulanan',    BulanController::class);
        Route::resource('teknisi',    TeknisiController::class);

        Route::middleware(['role:superadmin|kolektor'])->group(function () {
            Route::resource('kolektor', KolektorController::class);
        });
        Route::resource('penagih',    PenagihController::class);

        Route::middleware(['role:superadmin'])->group(function () {
            Route::resource('admin-desa', AdminDesaController::class)
                ->except(['create', 'show', 'edit'])
                ->parameters(['admin-desa' => 'adminDesa']);
            Route::resource('users',      UserController::class);
        });
    });

    Route::middleware(['role:superadmin|kolektor|admin_desa'])->group(function () {
        Route::get('/tagihan/rekap',      [RekapController::class, 'index'])->name('tagihan.rekap');
        Route::post('/tagihan/lunas-banyak', [TagihanController::class, 'lunaskanBanyak'])->name('tagihan.lunas-banyak');
        Route::get('/tagihan/rekap/export', [RekapController::class, 'export'])->name('tagihan.rekap.export')
             ->middleware('role:superadmin');
        Route::post('/tagihan/{tagihan}/lunas-cepat', [TagihanController::class, 'lunaskanCepat'])->name('tagihan.lunas-cepat');
        Route::post('/tagihan/{tagihan}/batal-lunas', [TagihanController::class, 'batalLunas'])->name('tagihan.batal-lunas');
        Route::resource('tagihan',      TagihanController::class);
    });

    // Notification Routes
    Route::get('/api/notifications', function(Illuminate\Http\Request $request) {
        return response()->json($request->user()->notifications()->latest()->get());
    })->name('notifications.index');
    
    Route::post('/api/notifications/{id}/read', function(Illuminate\Http\Request $request, $id) {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true]);
    });
    
    Route::post('/api/notifications/read-all', function(Illuminate\Http\Request $request) {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    });
    
    Route::delete('/api/notifications/{id}', function(Illuminate\Http\Request $request, $id) {
        $request->user()->notifications()->where('id', $id)->delete();
        return response()->json(['success' => true]);
    });
    
    Route::delete('/api/notifications', function(Illuminate\Http\Request $request) {
        $request->user()->notifications()->delete();
        return response()->json(['success' => true]);
    });
});
