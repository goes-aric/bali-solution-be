<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\MiscellaneousController;
use App\Http\Controllers\v1\Settings\User\AuthController;
use App\Http\Controllers\v1\Settings\User\RoleController;
use App\Http\Controllers\v1\Settings\User\UserController;
// use App\Http\Controllers\v1\DataMaster\Supplier\SupplierController;
// use App\Http\Controllers\v1\DataMaster\Material\AksesorisController;
// use App\Http\Controllers\v1\DataMaster\Pelanggan\PelangganController;
// use App\Http\Controllers\v1\Settings\Perusahaan\PerusahaanController;
// use App\Http\Controllers\v1\DataMaster\Material\MaterialKacaController;
// use App\Http\Controllers\v1\DataMaster\Material\MaterialUpvcController;
// use App\Http\Controllers\v1\DataMaster\Produk\KategoriProdukController;
use App\Http\Controllers\v1\Settings\ActivityLog\ActivityLogController;
// use App\Http\Controllers\v1\DataMaster\Material\PaketAksesorisController;
// use App\Http\Controllers\v1\DataMaster\Supplier\KontakSupplierController;
// use App\Http\Controllers\v1\DataMaster\Material\PaketAksesorisMaterialController;

Route::prefix('v1')->group(function(){
    /* REGISTER & LOGIN (AUTH) */
    Route::controller(AuthController::class)->group(function(){
        Route::post('/auth/register', 'register')->name('auth.register');
        Route::post('/auth/login', 'login')->name('auth.login');
    });

    /* FORGOT & RESET PASSWORD */
    Route::controller(UserController::class)->group(function(){
        Route::post('/users/forgot', 'forgotPassword')->name('users.forgotPassword');
        Route::post('/users/forgot/reset', 'resetPasswordByUser')->name('users.resetPasswordByUser');
    });

    Route::middleware(['auth:api'])->group(function(){
        /* MISCELLANEOUS */
        Route::controller(MiscellaneousController::class)->group(function(){
            Route::get('/satuan-panjang/options', 'fetchUnitLengthOptions')->name('misc.fetchUnitLengthOptions');
            Route::get('/satuan/options', 'fetchUnitOptions')->name('misc.fetchUnitOptions');
        });

        /* SUPPLIER */
        // Route::controller(SupplierController::class)->group(function(){
        //     Route::get('/supplier/options', 'fetchDataOptions')->name('supplier.fetchDataOptions');
        //     Route::get('/supplier/all', 'list')->name('supplier.list');
        //     Route::get('/supplier', 'index')->name('supplier.index');
        //     Route::post('/supplier', 'store')->name('supplier.store');
        //     Route::get('/supplier/{id}', 'show')->name('supplier.show');
        //     Route::put('/supplier/{id}', 'update')->name('supplier.update');
        //     Route::delete('/supplier/{id}', 'destroy')->name('supplier.destroy');
        //     Route::delete('/supplier', 'destroyMultiple')->name('supplier.destroyMultiple');
        //     Route::post('/supplier/import', 'import')->name('supplier.import');
        //     Route::post('/supplier/export', 'export')->name('supplier.export');
        //     Route::post('/supplier/draft', 'exportDraft')->name('supplier.exportDraft');
        // });

        /* KONTAK SUPPLIER */
        // Route::controller(KontakSupplierController::class)->group(function(){
        //     Route::get('/kontak-supplier/all/{id}', 'list')->name('kontakSupplier.list');
        //     Route::get('/kontak-supplier/limit/{id}', 'index')->name('kontakSupplier.index');
        //     Route::post('/kontak-supplier', 'store')->name('kontakSupplier.store');
        //     Route::get('/kontak-supplier/{id}', 'show')->name('kontakSupplier.show');
        //     Route::put('/kontak-supplier/{id}', 'update')->name('kontakSupplier.update');
        //     Route::delete('/kontak-supplier/{id}', 'destroy')->name('kontakSupplier.destroy');
        //     Route::delete('/kontak-supplier', 'destroyMultiple')->name('kontakSupplier.destroyMultiple');
        // });

        /* PELANGGAN */
        // Route::controller(PelangganController::class)->group(function(){
        //     Route::get('/pelanggan/options', 'fetchDataOptions')->name('pelanggan.fetchDataOptions');
        //     Route::get('/pelanggan/all', 'list')->name('pelanggan.list');
        //     Route::get('/pelanggan', 'index')->name('pelanggan.index');
        //     Route::post('/pelanggan', 'store')->name('pelanggan.store');
        //     Route::get('/pelanggan/{id}', 'show')->name('pelanggan.show');
        //     Route::put('/pelanggan/{id}', 'update')->name('pelanggan.update');
        //     Route::delete('/pelanggan/{id}', 'destroy')->name('pelanggan.destroy');
        //     Route::delete('/pelanggan', 'destroyMultiple')->name('pelanggan.destroyMultiple');
        //     Route::post('/pelanggan/export', 'export')->name('pelanggan.export');
        //     Route::post('/pelanggan/draft', 'exportDraft')->name('pelanggan.exportDraft');
        // });

        /* MATERIAL UPVC */
        // Route::controller(MaterialUpvcController::class)->group(function(){
        //     Route::get('/material-upvc/options', 'fetchDataOptions')->name('materialUpvc.fetchDataOptions');
        //     Route::get('/material-upvc/type', 'fetchDataTypeOptions')->name('materialUpvc.fetchDataTypeOptions');
        //     Route::get('/material-upvc/all', 'list')->name('materialUpvc.list');
        //     Route::get('/material-upvc', 'index')->name('materialUpvc.index');
        //     Route::post('/material-upvc', 'store')->name('materialUpvc.store');
        //     Route::get('/material-upvc/{id}', 'show')->name('materialUpvc.show');
        //     Route::put('/material-upvc/{id}', 'update')->name('materialUpvc.update');
        //     Route::delete('/material-upvc/{id}', 'destroy')->name('materialUpvc.destroy');
        //     Route::delete('/material-upvc', 'destroyMultiple')->name('materialUpvc.destroyMultiple');
        //     Route::post('/material-upvc/export', 'export')->name('materialUpvc.export');
        // });

        /* MATERIAL KACA */
        // Route::controller(MaterialKacaController::class)->group(function(){
        //     Route::get('/material-kaca/options', 'fetchDataOptions')->name('materialKaca.fetchDataOptions');
        //     Route::get('/material-kaca/all', 'list')->name('materialKaca.list');
        //     Route::get('/material-kaca', 'index')->name('materialKaca.index');
        //     Route::post('/material-kaca', 'store')->name('materialKaca.store');
        //     Route::get('/material-kaca/{id}', 'show')->name('materialKaca.show');
        //     Route::put('/material-kaca/{id}', 'update')->name('materialKaca.update');
        //     Route::delete('/material-kaca/{id}', 'destroy')->name('materialKaca.destroy');
        //     Route::delete('/material-kaca', 'destroyMultiple')->name('materialKaca.destroyMultiple');
        //     Route::post('/material-kaca/export', 'export')->name('materialKaca.export');
        // });

        /* AKSESORIS */
        // Route::controller(AksesorisController::class)->group(function(){
        //     Route::get('/aksesoris/options', 'fetchDataOptions')->name('aksesoris.fetchDataOptions');
        //     Route::get('/aksesoris/all', 'list')->name('aksesoris.list');
        //     Route::get('/aksesoris', 'index')->name('aksesoris.index');
        //     Route::post('/aksesoris', 'store')->name('aksesoris.store');
        //     Route::get('/aksesoris/{id}', 'show')->name('aksesoris.show');
        //     Route::put('/aksesoris/{id}', 'update')->name('aksesoris.update');
        //     Route::delete('/aksesoris/{id}', 'destroy')->name('aksesoris.destroy');
        //     Route::delete('/aksesoris', 'destroyMultiple')->name('aksesoris.destroyMultiple');
        //     Route::post('/aksesoris/export', 'export')->name('aksesoris.export');
        // });

        /* PAKET AKSESORIS */
        // Route::controller(PaketAksesorisController::class)->group(function(){
        //     Route::get('/paket-aksesoris/options', 'fetchDataOptions')->name('paketAksesoris.fetchDataOptions');
        //     Route::get('/paket-aksesoris/all', 'list')->name('paketAksesoris.list');
        //     Route::get('/paket-aksesoris', 'index')->name('paketAksesoris.index');
        //     Route::post('/paket-aksesoris', 'store')->name('paketAksesoris.store');
        //     Route::get('/paket-aksesoris/{id}', 'show')->name('paketAksesoris.show');
        //     Route::put('/paket-aksesoris/{id}', 'update')->name('paketAksesoris.update');
        //     Route::delete('/paket-aksesoris/{id}', 'destroy')->name('paketAksesoris.destroy');
        //     Route::delete('/paket-aksesoris', 'destroyMultiple')->name('paketAksesoris.destroyMultiple');
        //     Route::post('/paket-aksesoris/export', 'export')->name('paketAksesoris.export');
        // });

        /* PAKET AKSESORIS MATERIAL */
        // Route::controller(PaketAksesorisMaterialController::class)->group(function(){
        //     Route::get('/paket-material/all/{id}', 'list')->name('paketMaterial.list');
        //     Route::get('/paket-material/limit/{id}', 'index')->name('paketMaterial.index');
        //     Route::post('/paket-material', 'store')->name('paketMaterial.store');
        //     Route::get('/paket-material/{id}', 'show')->name('paketMaterial.show');
        //     Route::put('/paket-material/{id}', 'update')->name('paketMaterial.update');
        //     Route::delete('/paket-material/{id}', 'destroy')->name('paketMaterial.destroy');
        //     Route::delete('/paket-material', 'destroyMultiple')->name('paketMaterial.destroyMultiple');
        // });

        /* KATEGORI PRODUK */
        // Route::controller(KategoriProdukController::class)->group(function(){
        //     Route::get('/kategori-produk/options', 'fetchDataOptions')->name('kategori-produk.fetchDataOptions');
        //     Route::get('/kategori-produk/all', 'list')->name('kategori-produk.list');
        //     Route::get('/kategori-produk', 'index')->name('kategori-produk.index');
        //     Route::post('/kategori-produk', 'store')->name('kategori-produk.store');
        //     Route::get('/kategori-produk/{id}', 'show')->name('kategori-produk.show');
        //     Route::put('/kategori-produk/{id}', 'update')->name('kategori-produk.update');
        //     Route::delete('/kategori-produk/{id}', 'destroy')->name('kategori-produk.destroy');
        //     Route::delete('/kategori-produk', 'destroyMultiple')->name('kategori-produk.destroyMultiple');
        // });

        /* PERUSAHAAN */
        // Route::controller(PerusahaanController::class)->group(function(){
        //     Route::get('/perusahaan/{id}', 'show')->name('perusahaan.show');
        //     Route::post('/perusahaan', 'save')->name('perusahaan.save');
        //     Route::delete('/perusahaan/{id}', 'destroy')->name('perusahaan.destroy');
        // });

        /* ACTIVITY LOGS */
        Route::controller(ActivityLogController::class)->group(function(){
            Route::get('/activity-logs', 'index')->name('activity.index');
            Route::get('/activity-logs/{id}', 'show')->name('activity.show');
        });

        /* ROLES */
        Route::controller(RoleController::class)->group(function(){
            Route::get('/roles/permissions/options', 'fetchPermissionOptions')->name('roles.fetchPermissionOptions');
            Route::get('/roles/all', 'list')->name('roles.list');
            Route::get('/roles', 'index')->name('roles.index');
            Route::post('/roles', 'store')->name('roles.store');
            Route::get('/roles/{id}', 'show')->name('roles.show');
            Route::put('/roles/{id}', 'update')->name('roles.update');
            Route::delete('/roles/{id}', 'destroy')->name('roles.destroy');
            Route::delete('/roles', 'destroyMultiple')->name('roles.destroyMultiple');
        });

        /* USERS & LOGOUT */
        Route::controller(UserController::class)->group(function(){
            Route::get('/users/options', 'fetchDataOptions')->name('users.fetchDataOptions');
            Route::post('/users/forgot/reset', 'resetPasswordByUser')->name('users.resetPasswordByUser');
            Route::delete('/users', 'destroyMultiple')->name('users.destroyMultiple');
            Route::put('/users/password', 'changePassword')->name('users.changePassword');
            Route::put('/users/password/{id}', 'resetPassword')->name('users.resetPassword');
        });
        Route::apiResource('users', UserController::class);
        Route::get('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    });
});
