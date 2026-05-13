<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Admin\AdminController;
use App\Http\Controllers\Backend\AdminProfileController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\DynamicPageController;
use App\Http\Controllers\Backend\RolePermissionController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\VideoController;


Route::controller(AdminController::class)->group(function(){
    Route::get('/admin/dashboard','adminDashboard')->name('admin.dashboard');
});


Route::middleware('auth')->group(function () {
    Route::get('/admin/profile', [AdminProfileController::class, 'index'])->name('admin.profile');
    Route::post('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');

    Route::prefix('admin/settings')->name('admin.settings.')->middleware('permission:manage settings')->group(function () {
        Route::get('/smtp', [SettingsController::class, 'smtp'])->name('smtp');
        Route::post('/smtp', [SettingsController::class, 'updateSmtp'])->name('smtp.update');

        Route::get('/website', [SettingsController::class, 'website'])->name('website');
        Route::post('/website', [SettingsController::class, 'updateWebsite'])->name('website.update');

        Route::get('/admin', [SettingsController::class, 'admin'])->name('admin');
        Route::post('/admin', [SettingsController::class, 'updateAdmin'])->name('admin.update');

        Route::get('/stripe', [SettingsController::class, 'stripe'])->name('stripe');
        Route::post('/stripe', [SettingsController::class, 'updateStripe'])->name('stripe.update');

        Route::get('/dynamic-page', [DynamicPageController::class, 'index'])->name('dynamic.page');
        Route::get('/dynamic-page/data', [DynamicPageController::class, 'data'])->name('dynamic.page.data');
        Route::post('/dynamic-page', [DynamicPageController::class, 'store'])->name('dynamic.page.store');
        Route::get('/dynamic-page/{dynamicPage}/edit', [DynamicPageController::class, 'edit'])->name('dynamic.page.edit');
        Route::put('/dynamic-page/{dynamicPage}', [DynamicPageController::class, 'update'])->name('dynamic.page.update');
        Route::delete('/dynamic-page/{dynamicPage}', [DynamicPageController::class, 'destroy'])->name('dynamic.page.destroy');

    });

    Route::prefix('admin/settings')->name('admin.settings.')->middleware('permission:manage roles')->group(function () {
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])->name('roles.permissions');
        Route::post('/roles', [RolePermissionController::class, 'storeRole'])->name('roles.store');
        Route::post('/permissions', [RolePermissionController::class, 'storePermission'])->name('permissions.store');
        Route::post('/assign-role', [RolePermissionController::class, 'assignRole'])->name('assign-role');
        Route::post('/sync-role-permissions', [RolePermissionController::class, 'syncPermissionToRole'])->name('sync-role-permissions');
    });

    Route::prefix('admin/users')->name('admin.users.')->middleware('permission:manage users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/data', [UserController::class, 'data'])->name('data');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
    });

    Route::prefix('admin/categories')->name('admin.categories.')->middleware('permission:manage settings')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/data', [CategoryController::class, 'data'])->name('data');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::post('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('admin/videos')->name('admin.videos.')->middleware('permission:manage settings')->group(function () {
        Route::get('/', [VideoController::class, 'index'])->name('index');
        Route::get('/create', [VideoController::class, 'create'])->name('create');
        Route::get('/data', [VideoController::class, 'data'])->name('data');
        Route::post('/', [VideoController::class, 'store'])->name('store');
        Route::get('/{video}/edit', [VideoController::class, 'edit'])->name('edit');
        Route::get('/{video}/edit-page', [VideoController::class, 'editPage'])->name('edit-page');
        Route::post('/{video}', [VideoController::class, 'update'])->name('update');
        Route::delete('/{video}', [VideoController::class, 'destroy'])->name('destroy');
    });
});
