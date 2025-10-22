<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn () => view('welcome'));

// Dashboard (only authenticated & verified users)
Route::get('/dashboard', [DashboardController::class, 'dashboard'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile routes
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'edit'])->name('edit');
    Route::patch('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'permission:users.manage'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('roles', RoleController::class);

    Route::resource('users', UserController::class);
    //Route::resource('users', UserController::class)->except(['show']);
    Route::get('users/{user}/roles/assign', [UserController::class, 'assignRoleForm'])
        ->name('users.assignRoleForm')
        ->middleware('permission:users.assign'); 
    Route::post('users/{user}/roles/assign', [UserController::class, 'assignRoles'])
        ->name('users.assignRoles')
        ->middleware('permission:users.assign');
});

require __DIR__.'/auth.php';


Route::get('/dashboard', fn () => view('dashboard'))->name('dashboard');

Route::get('blank-page',fn () => view('blank-page'))->name('blank');
Route::get('blank-page',fn () => view('admin.roles.rolelist'));
Route::get('blank-page',fn () => view('admin.roles.roleedit'));


Route::middleware(['auth', 'permission:settings.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:settings.update');
});




//test repository
Route::get('/testrepso', [TestController::class, 'index']);