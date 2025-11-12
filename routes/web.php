<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SwitchAccountController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserContextController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Services\UserContextManager;
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

    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

        Route::resource('roles', RoleController::class)
            ->middleware('permission:roles.manage');

        Route::get('users', [UserController::class, 'index'])
            ->name('users.index')
            ->middleware('permission:users.view');

        Route::get('users/create', [UserController::class, 'create'])
            ->name('users.create')
            ->middleware('permission:users.create');

        Route::get('users/show', [UserController::class, 'create'])
            ->name('users.show')
            ->middleware('permission:users.show');

        Route::post('users', [UserController::class, 'store'])
            ->name('users.store')
            ->middleware('permission:users.create');

        Route::get('users/{user}/edit', [UserController::class, 'edit'])
            ->name('users.edit')
            ->middleware('permission:users.edit');

        Route::put('users/{user}', [UserController::class, 'update'])
            ->name('users.update')
            ->middleware('permission:users.edit');

        Route::delete('users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy')
            ->middleware('permission:users.delete');

        Route::get('users/{user}/roles/assign', [UserController::class, 'assignRoleForm'])
            ->name('users.assignRoleForm')
            ->middleware('permission:users.assign');
        Route::post('users/{user}/roles/assign', [UserController::class, 'assignRoles'])
            ->name('users.assignRoles');
    });

/* Route::middleware(['auth', 'permission:users.manage'])->prefix('admin')->name('admin.')->group(function () {

    Route::resource('roles', RoleController::class);
    
    Route::resource('users', UserController::class);
    Route::get('users/create', [UserController::class, 'create'])
    ->name('users.create')->middleware('permission:users.create');
    //Route::resource('users', UserController::class)->except(['show']);
    Route::get('users/{user}/roles/assign', [UserController::class, 'assignRoleForm'])
        ->name('users.assignRoleForm')
        ->middleware('permission:users.assign');
    Route::post('users/{user}/roles/assign', [UserController::class, 'assignRoles'])
        ->name('users.assignRoles')
        ->middleware('permission:users.assign');
}); */


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Roles
    Route::resource('roles', RoleController::class)
        ->except(['show'])
        ->middleware('permission:roles.manage');

    // Users
    Route::get('users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users.view');
    Route::get('users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::post('users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.create');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:users.view');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.edit');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.delete');
    Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore')->middleware('permission:users.edit');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete')->middleware('permission:users.delete');
});


require __DIR__.'/auth.php';


Route::get('blank-page',fn () => view('blank-page'))->name('blank');
Route::get('blank-page',fn () => view('admin.roles.rolelist'));
Route::get('blank-page',fn () => view('admin.roles.roleedit'));


Route::middleware(['auth', 'permission:settings.manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update')->middleware('permission:settings.update');
});



Route::middleware('auth')->group(function () {
    Route::post('/switch-profile', [UserContextController::class, 'switchProfile'])->name('profile.switch');
});


//test repository
Route::get('/testrepso', [TestController::class, 'index']);


Route::get('/test', function () {
    return auth()->user()->hasPermission('users.manage') ? 'OK' : 'NO';
})->middleware('auth');



Route::get('/cache', function () {
    $contextManager = app(UserContextManager::class);
    $contextManager->clearAllCachesByProfile($contextManager->getUserProfileId(), $module = 'all');
    return  redirect('/dashboard');
})->middleware('auth');