<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\v1\MiscellaneousController;
use App\Http\Controllers\v1\Settings\User\AuthController;
use App\Http\Controllers\v1\Settings\User\RoleController;
use App\Http\Controllers\v1\Settings\User\UserController;
use App\Http\Controllers\v1\DataMaster\Supplier\SupplierController;
use App\Http\Controllers\v1\DataMaster\Material\AksesorisController;
use App\Http\Controllers\v1\DataMaster\Produk\PaketProdukController;
use App\Http\Controllers\v1\DataMaster\Pelanggan\PelangganController;
use App\Http\Controllers\v1\Settings\Perusahaan\PerusahaanController;
use App\Http\Controllers\v1\DataMaster\Material\KacaController;
use App\Http\Controllers\v1\DataMaster\Material\MaterialController;
use App\Http\Controllers\v1\DataMaster\Produk\KategoriProdukController;
use App\Http\Controllers\v1\Settings\ActivityLog\ActivityLogController;
use App\Http\Controllers\v1\DataMaster\Material\PaketAksesorisController;
use App\Http\Controllers\v1\DataMaster\Supplier\KontakSupplierController;
use App\Http\Controllers\v1\DataMaster\Penawaran\KalkulasiProdukController;
use App\Http\Controllers\v1\DataMaster\Produk\PaketProdukAksesorisController;
use App\Http\Controllers\v1\Settings\TipePenyesuaian\TipePenyesuaianController;
use App\Http\Controllers\v1\DataMaster\Produk\PaketProdukKacaController;
use App\Http\Controllers\v1\DataMaster\Produk\PaketProdukMaterialController;
use App\Http\Controllers\v1\DataMaster\Material\PaketAksesorisMaterialController;

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
        Route::controller(SupplierController::class)->group(function(){
            Route::get('/supplier/options', 'fetchDataOptions')->name('supplier.fetchDataOptions');
            Route::get('/supplier/all', 'list')->name('supplier.list');
            Route::get('/supplier', 'index')->name('supplier.index');
            Route::post('/supplier', 'store')->name('supplier.store');
            Route::get('/supplier/{id}', 'show')->name('supplier.show');
            Route::put('/supplier/{id}', 'update')->name('supplier.update');
            Route::delete('/supplier/{id}', 'destroy')->name('supplier.destroy');
            Route::delete('/supplier', 'destroyMultiple')->name('supplier.destroyMultiple');
            Route::post('/supplier/import', 'import')->name('supplier.import');
            Route::post('/supplier/export', 'export')->name('supplier.export');
            Route::post('/supplier/draft', 'exportDraft')->name('supplier.exportDraft');
        });

        /* KONTAK SUPPLIER */
        Route::controller(KontakSupplierController::class)->group(function(){
            Route::get('/kontak-supplier/all/{id}', 'list')->name('kontakSupplier.list');
            Route::get('/kontak-supplier/limit/{id}', 'index')->name('kontakSupplier.index');
            Route::post('/kontak-supplier', 'store')->name('kontakSupplier.store');
            Route::get('/kontak-supplier/{id}', 'show')->name('kontakSupplier.show');
            Route::put('/kontak-supplier/{id}', 'update')->name('kontakSupplier.update');
            Route::delete('/kontak-supplier/{id}', 'destroy')->name('kontakSupplier.destroy');
            Route::delete('/kontak-supplier', 'destroyMultiple')->name('kontakSupplier.destroyMultiple');
        });

        /* PELANGGAN */
        Route::controller(PelangganController::class)->group(function(){
            Route::get('/pelanggan/options', 'fetchDataOptions')->name('pelanggan.fetchDataOptions');
            Route::get('/pelanggan/all', 'list')->name('pelanggan.list');
            Route::get('/pelanggan', 'index')->name('pelanggan.index');
            Route::post('/pelanggan', 'store')->name('pelanggan.store');
            Route::get('/pelanggan/{id}', 'show')->name('pelanggan.show');
            Route::put('/pelanggan/{id}', 'update')->name('pelanggan.update');
            Route::delete('/pelanggan/{id}', 'destroy')->name('pelanggan.destroy');
            Route::delete('/pelanggan', 'destroyMultiple')->name('pelanggan.destroyMultiple');
            Route::post('/pelanggan/export', 'export')->name('pelanggan.export');
            Route::post('/pelanggan/draft', 'exportDraft')->name('pelanggan.exportDraft');
        });

        /* MATERIAL */
        Route::controller(MaterialController::class)->group(function(){
            Route::get('/material/options', 'fetchDataOptions')->name('material.fetchDataOptions');
            Route::get('/material/all', 'list')->name('material.list');
            Route::get('/material', 'index')->name('material.index');
            Route::post('/material', 'store')->name('material.store');
            Route::get('/material/{id}', 'show')->name('material.show');
            Route::put('/material/{id}', 'update')->name('material.update');
            Route::delete('/material/{id}', 'destroy')->name('material.destroy');
            Route::delete('/material', 'destroyMultiple')->name('material.destroyMultiple');
            Route::post('/material/export', 'export')->name('material.export');
        });

        /* KACA */
        Route::controller(KacaController::class)->group(function(){
            Route::get('/kaca/options', 'fetchDataOptions')->name('kaca.fetchDataOptions');
            Route::get('/kaca/all', 'list')->name('kaca.list');
            Route::get('/kaca', 'index')->name('kaca.index');
            Route::post('/kaca', 'store')->name('kaca.store');
            Route::get('/kaca/{id}', 'show')->name('kaca.show');
            Route::put('/kaca/{id}', 'update')->name('kaca.update');
            Route::delete('/kaca/{id}', 'destroy')->name('kaca.destroy');
            Route::delete('/kaca', 'destroyMultiple')->name('kaca.destroyMultiple');
            Route::post('/kaca/export', 'export')->name('kaca.export');
        });

        /* AKSESORIS */
        Route::controller(AksesorisController::class)->group(function(){
            Route::get('/aksesoris/options', 'fetchDataOptions')->name('aksesoris.fetchDataOptions');
            Route::get('/aksesoris/all', 'list')->name('aksesoris.list');
            Route::get('/aksesoris', 'index')->name('aksesoris.index');
            Route::post('/aksesoris', 'store')->name('aksesoris.store');
            Route::get('/aksesoris/{id}', 'show')->name('aksesoris.show');
            Route::put('/aksesoris/{id}', 'update')->name('aksesoris.update');
            Route::delete('/aksesoris/{id}', 'destroy')->name('aksesoris.destroy');
            Route::delete('/aksesoris', 'destroyMultiple')->name('aksesoris.destroyMultiple');
            Route::post('/aksesoris/export', 'export')->name('aksesoris.export');
        });

        /* PAKET AKSESORIS */
        Route::controller(PaketAksesorisController::class)->group(function(){
            Route::get('/paket-aksesoris/options', 'fetchDataOptions')->name('paketAksesoris.fetchDataOptions');
            Route::get('/paket-aksesoris/all', 'list')->name('paketAksesoris.list');
            Route::get('/paket-aksesoris', 'index')->name('paketAksesoris.index');
            Route::post('/paket-aksesoris', 'store')->name('paketAksesoris.store');
            Route::get('/paket-aksesoris/{id}', 'show')->name('paketAksesoris.show');
            Route::put('/paket-aksesoris/{id}', 'update')->name('paketAksesoris.update');
            Route::delete('/paket-aksesoris/{id}', 'destroy')->name('paketAksesoris.destroy');
            Route::delete('/paket-aksesoris', 'destroyMultiple')->name('paketAksesoris.destroyMultiple');
            Route::post('/paket-aksesoris/export', 'export')->name('paketAksesoris.export');
        });

        /* PAKET AKSESORIS MATERIAL */
        Route::controller(PaketAksesorisMaterialController::class)->group(function(){
            Route::get('/paket-material/all/{id}', 'list')->name('paketMaterial.list');
            Route::get('/paket-material/limit/{id}', 'index')->name('paketMaterial.index');
            Route::post('/paket-material', 'store')->name('paketMaterial.store');
            Route::get('/paket-material/{id}', 'show')->name('paketMaterial.show');
            Route::put('/paket-material/{id}', 'update')->name('paketMaterial.update');
            Route::delete('/paket-material/{id}', 'destroy')->name('paketMaterial.destroy');
            Route::delete('/paket-material', 'destroyMultiple')->name('paketMaterial.destroyMultiple');
        });

        /* KATEGORI PRODUK */
        Route::controller(KategoriProdukController::class)->group(function(){
            Route::get('/kategori-produk/options', 'fetchDataOptions')->name('kategoriProduk.fetchDataOptions');
            Route::get('/kategori-produk/all', 'list')->name('kategoriProduk.list');
            Route::get('/kategori-produk', 'index')->name('kategoriProduk.index');
            Route::post('/kategori-produk', 'store')->name('kategoriProduk.store');
            Route::get('/kategori-produk/{id}', 'show')->name('kategoriProduk.show');
            Route::put('/kategori-produk/{id}', 'update')->name('kategoriProduk.update');
            Route::delete('/kategori-produk/{id}', 'destroy')->name('kategoriProduk.destroy');
            Route::delete('/kategori-produk', 'destroyMultiple')->name('kategoriProduk.destroyMultiple');
        });

        /* PAKET PRODUK */
        Route::controller(PaketProdukController::class)->group(function(){
            Route::get('/paket-produk/options', 'fetchDataOptions')->name('paketProduk.fetchDataOptions');
            Route::get('/paket-produk/all', 'list')->name('paketProduk.list');
            Route::get('/paket-produk', 'index')->name('paketProduk.index');
            Route::post('/paket-produk', 'store')->name('paketProduk.store');
            Route::get('/paket-produk/{id}', 'show')->name('paketProduk.show');
            Route::put('/paket-produk/{id}', 'update')->name('paketProduk.update');
            Route::delete('/paket-produk/{id}', 'destroy')->name('paketProduk.destroy');
            Route::delete('/paket-produk', 'destroyMultiple')->name('paketProduk.destroyMultiple');
        });

        /* KACA PAKET PRODUK */
        Route::controller(PaketProdukKacaController::class)->group(function(){
            Route::get('/kaca-produk/all', 'list')->name('kacaProduk.list');
            Route::get('/kaca-produk', 'index')->name('kacaProduk.index');
            Route::post('/kaca-produk', 'store')->name('kacaProduk.store');
            Route::get('/kaca-produk/{id}', 'show')->name('kacaProduk.show');
            Route::put('/kaca-produk/{id}', 'update')->name('kacaProduk.update');
            Route::delete('/kaca-produk/{id}', 'destroy')->name('kacaProduk.destroy');
            Route::delete('/kaca-produk', 'destroyMultiple')->name('kacaProduk.destroyMultiple');
        });

        /* MATERIAL PAKET PRODUK */
        Route::controller(PaketProdukMaterialController::class)->group(function(){
            Route::get('/material-produk/all', 'list')->name('materialProduk.list');
            Route::get('/material-produk', 'index')->name('materialProduk.index');
            Route::post('/material-produk', 'store')->name('materialProduk.store');
            Route::get('/material-produk/{id}', 'show')->name('materialProduk.show');
            Route::put('/material-produk/{id}', 'update')->name('materialProduk.update');
            Route::delete('/material-produk/{id}', 'destroy')->name('materialProduk.destroy');
            Route::delete('/material-produk', 'destroyMultiple')->name('materialProduk.destroyMultiple');
        });

        /* AKSESORIS PAKET PRODUK */
        Route::controller(PaketProdukAksesorisController::class)->group(function(){
            Route::get('/aksesoris-produk/all', 'list')->name('aksesorisProduk.list');
            Route::get('/aksesoris-produk', 'index')->name('aksesorisProduk.index');
            Route::post('/aksesoris-produk', 'store')->name('aksesorisProduk.store');
            Route::get('/aksesoris-produk/{id}', 'show')->name('aksesorisProduk.show');
            Route::put('/aksesoris-produk/{id}', 'update')->name('aksesorisProduk.update');
            Route::delete('/aksesoris-produk/{id}', 'destroy')->name('aksesorisProduk.destroy');
            Route::delete('/aksesoris-produk', 'destroyMultiple')->name('aksesorisProduk.destroyMultiple');
        });

        /* KALKULASI PRODUK */
        // Route::controller(KalkulasiProdukController::class)->group(function(){
        //     Route::post('/kalkulasi-produk', 'processCalculation')->name('kalkulasiProduk.processCalculation');
        // });

        /* PERUSAHAAN */
        Route::controller(PerusahaanController::class)->group(function(){
            Route::get('/perusahaan/{id}', 'show')->name('perusahaan.show');
            Route::post('/perusahaan', 'save')->name('perusahaan.save');
            Route::delete('/perusahaan/{id}', 'destroy')->name('perusahaan.destroy');
        });

        /* TIPE PENYESUAIAN */
        Route::controller(TipePenyesuaianController::class)->group(function(){
            Route::get('/tipe-penyesuaian/options', 'fetchDataOptions')->name('tipePenyesuaian.fetchDataOptions');
            Route::get('/tipe-penyesuaian/all', 'list')->name('tipePenyesuaian.list');
            Route::get('/tipe-penyesuaian', 'index')->name('tipePenyesuaian.index');
            Route::post('/tipe-penyesuaian', 'store')->name('tipePenyesuaian.store');
            Route::get('/tipe-penyesuaian/{id}', 'show')->name('tipePenyesuaian.show');
            Route::put('/tipe-penyesuaian/{id}', 'update')->name('tipePenyesuaian.update');
            Route::delete('/tipe-penyesuaian/{id}', 'destroy')->name('tipePenyesuaian.destroy');
            Route::delete('/tipe-penyesuaian', 'destroyMultiple')->name('tipePenyesuaian.destroyMultiple');
        });

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
