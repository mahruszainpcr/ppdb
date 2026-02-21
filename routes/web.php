<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\PsbWizardController;
use App\Http\Controllers\Admin\RegistrationAdminController;
use App\Http\Controllers\Admin\PeriodController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\StaffAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\NewsPostController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\ClassLevelController;
use App\Http\Controllers\Admin\SemesterController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Auth\ParentAuthController;
use App\Http\Controllers\Auth\PasswordController;

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
    Route::middleware(['auth', 'role:admin,ustadz'])->group(function () {
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
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/users', [UserAdminController::class, 'index'])
                ->name('admin.users.index');
            Route::get('/users/data', [UserAdminController::class, 'data'])
                ->name('admin.users.data');

            Route::post('/users/{user}/update', [UserAdminController::class, 'update'])
                ->name('admin.users.update');

            Route::post('/users/{user}/reset-password', [UserAdminController::class, 'resetPassword'])
                ->name('admin.users.resetPassword');

            Route::get('/staff', [StaffAdminController::class, 'index'])->name('admin.staff.index');
            Route::get('/staff/data', [StaffAdminController::class, 'data'])->name('admin.staff.data');
            Route::post('/staff', [StaffAdminController::class, 'store'])->name('admin.staff.store');
            Route::post('/staff/{user}/update', [StaffAdminController::class, 'update'])->name('admin.staff.update');
            Route::post('/staff/{user}/reset-password', [StaffAdminController::class, 'resetPassword'])
                ->name('admin.staff.resetPassword');
            Route::post('/staff/{user}/delete', [StaffAdminController::class, 'destroy'])->name('admin.staff.destroy');

            Route::get('/academic-years', [AcademicYearController::class, 'index'])
                ->name('admin.academic-years.index');
            Route::get('/academic-years/create', [AcademicYearController::class, 'create'])
                ->name('admin.academic-years.create');
            Route::post('/academic-years', [AcademicYearController::class, 'store'])
                ->name('admin.academic-years.store');
            Route::get('/academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])
                ->name('admin.academic-years.edit');
            Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])
                ->name('admin.academic-years.update');
            Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])
                ->name('admin.academic-years.destroy');
            Route::patch('/academic-years/{academicYear}/toggle', [AcademicYearController::class, 'toggle'])
                ->name('admin.academic-years.toggle');

            Route::get('/class-levels', [ClassLevelController::class, 'index'])
                ->name('admin.class-levels.index');
            Route::get('/class-levels/create', [ClassLevelController::class, 'create'])
                ->name('admin.class-levels.create');
            Route::post('/class-levels', [ClassLevelController::class, 'store'])
                ->name('admin.class-levels.store');
            Route::get('/class-levels/{classLevel}/edit', [ClassLevelController::class, 'edit'])
                ->name('admin.class-levels.edit');
            Route::put('/class-levels/{classLevel}', [ClassLevelController::class, 'update'])
                ->name('admin.class-levels.update');
            Route::delete('/class-levels/{classLevel}', [ClassLevelController::class, 'destroy'])
                ->name('admin.class-levels.destroy');
            Route::patch('/class-levels/{classLevel}/toggle', [ClassLevelController::class, 'toggle'])
                ->name('admin.class-levels.toggle');

            Route::get('/semesters', [SemesterController::class, 'index'])
                ->name('admin.semesters.index');
            Route::get('/semesters/create', [SemesterController::class, 'create'])
                ->name('admin.semesters.create');
            Route::post('/semesters', [SemesterController::class, 'store'])
                ->name('admin.semesters.store');
            Route::get('/semesters/{semester}/edit', [SemesterController::class, 'edit'])
                ->name('admin.semesters.edit');
            Route::put('/semesters/{semester}', [SemesterController::class, 'update'])
                ->name('admin.semesters.update');
            Route::delete('/semesters/{semester}', [SemesterController::class, 'destroy'])
                ->name('admin.semesters.destroy');
            Route::patch('/semesters/{semester}/toggle', [SemesterController::class, 'toggle'])
                ->name('admin.semesters.toggle');
        });

        Route::get('/news-categories', [NewsCategoryController::class, 'index'])->name('admin.news-categories.index');
        Route::get('/news-categories/data', [NewsCategoryController::class, 'data'])->name('admin.news-categories.data');
        Route::get('/news-categories/create', [NewsCategoryController::class, 'create'])->name('admin.news-categories.create');
        Route::post('/news-categories', [NewsCategoryController::class, 'store'])->name('admin.news-categories.store');
        Route::get('/news-categories/{newsCategory}/edit', [NewsCategoryController::class, 'edit'])->name('admin.news-categories.edit');
        Route::put('/news-categories/{newsCategory}', [NewsCategoryController::class, 'update'])->name('admin.news-categories.update');
        Route::delete('/news-categories/{newsCategory}', [NewsCategoryController::class, 'destroy'])->name('admin.news-categories.destroy');

        Route::get('/news-posts', [NewsPostController::class, 'index'])->name('admin.news-posts.index');
        Route::get('/news-posts/data', [NewsPostController::class, 'data'])->name('admin.news-posts.data');
        Route::get('/news-posts/create', [NewsPostController::class, 'create'])->name('admin.news-posts.create');
        Route::post('/news-posts', [NewsPostController::class, 'store'])->name('admin.news-posts.store');
        Route::get('/news-posts/{newsPost}/edit', [NewsPostController::class, 'edit'])->name('admin.news-posts.edit');
        Route::put('/news-posts/{newsPost}', [NewsPostController::class, 'update'])->name('admin.news-posts.update');
        Route::delete('/news-posts/{newsPost}', [NewsPostController::class, 'destroy'])->name('admin.news-posts.destroy');
    });
});

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/informasi', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/register', [ParentAuthController::class, 'showRegister'])->name('parent.register');
Route::post('/register', [ParentAuthController::class, 'register'])->name('parent.register.store');

Route::get('/login', [ParentAuthController::class, 'showLogin'])->name('login');
Route::post('/login', [ParentAuthController::class, 'login'])->name('parent.login.store');

Route::post('/logout', [ParentAuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/change-password', function () {
        return view('auth.change-password');
    })->name('password.change');
    Route::post('/change-password', [PasswordController::class, 'update'])
        ->name('password.update.self');
});
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
