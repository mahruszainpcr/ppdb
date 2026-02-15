<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\PsbWizardController;
use App\Http\Controllers\Admin\RegistrationAdminController;
use App\Http\Controllers\Admin\PeriodController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::get('/psb', fn() => view('public.psb.index'));
Route::get('/psb/syarat', fn() => view('public.psb.syarat'));

// Parent App
Route::middleware(['auth', 'role:parent'])->prefix('app')->group(function () {
    Route::get('/', [PsbWizardController::class, 'dashboard'])->name('app.dashboard');

    Route::get('/psb/wizard', [PsbWizardController::class, 'show'])->name('psb.wizard');
    Route::post('/psb/step-1', [PsbWizardController::class, 'saveStep1'])->name('psb.step1');
    Route::post('/psb/step-2', [PsbWizardController::class, 'saveStep2'])->name('psb.step2');
    Route::post('/psb/step-3', [PsbWizardController::class, 'saveStep3Submit'])->name('psb.step3.submit');

    Route::get('/result', [PsbWizardController::class, 'result'])->name('psb.result');
});

// Admin
Route::prefix('admin')->group(function () {
    // login admin (guest)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
    });

    // admin area (auth + role)
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        Route::get('/registrations', [RegistrationAdminController::class, 'index'])->name('admin.registrations.index');
        Route::get('/registrations/data', [RegistrationAdminController::class, 'data'])->name('admin.registrations.data');
        Route::get('/registrations/export', [RegistrationAdminController::class, 'export'])->name('admin.registrations.export');
        Route::get('/registrations/{registration}', [RegistrationAdminController::class, 'show'])->name('admin.registrations.show');
        Route::post(
            'registrations/{registration}/graduation',
            [\App\Http\Controllers\Admin\RegistrationAdminController::class, 'setGraduation']
        )->name('admin.registrations.graduation');
        Route::get('/users', [UserAdminController::class, 'index'])
            ->name('admin.users.index');
        Route::get('/users/data', [UserAdminController::class, 'data'])
            ->name('admin.users.data');

        Route::post('/users/{user}/update', [UserAdminController::class, 'update'])
            ->name('admin.users.update');

        Route::post('/users/{user}/reset-password', [UserAdminController::class, 'resetPassword'])
            ->name('admin.users.resetPassword');

    });
});

Route::get('/', function () {
    return view('welcome');
});
use App\Http\Controllers\Auth\ParentAuthController;

Route::get('/register', [ParentAuthController::class, 'showRegister'])->name('parent.register');
Route::post('/register', [ParentAuthController::class, 'register'])->name('parent.register.store');

Route::get('/login', [ParentAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [ParentAuthController::class, 'login'])->name('parent.login.store');

Route::post('/logout', [ParentAuthController::class, 'logout'])->middleware('auth')->name('logout');
// Route::get('/app', fn() => view('app.dashboard'))->middleware(['auth', 'role:parent'])->name('app.dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__ . '/auth.php';
