<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\App\PsbWizardController;
use App\Http\Controllers\App\WilayahController;
use App\Http\Controllers\Admin\RegistrationAdminController;
use App\Http\Controllers\Admin\PeriodController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\StaffAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\NewsCategoryController;
use App\Http\Controllers\Admin\NewsPostController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\AdminDocumentController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\GuestAttendanceController;
use App\Http\Controllers\Auth\ParentAuthController;
use App\Http\Controllers\Auth\PasswordController;
use Illuminate\Support\Facades\Artisan;

Route::get('/psb', fn() => view('public.psb.index'));
Route::get('/psb/syarat', fn() => view('public.psb.syarat'));
Route::get('/ppdb', [LandingController::class, 'ppdbInfo'])->name('ppdb.info');
Route::get('/absensi', [GuestAttendanceController::class, 'index'])->name('attendance.index');
Route::get('/absensi/{event:slug}', [GuestAttendanceController::class, 'show'])->name('attendance.event');
Route::post('/absensi/{event:slug}/lookup', [GuestAttendanceController::class, 'lookup'])->name('attendance.lookup');
Route::post('/absensi/{event:slug}', [GuestAttendanceController::class, 'store'])->name('attendance.store');

// Parent App
Route::middleware(['auth', 'role:parent'])->prefix('app')->group(function () {
    Route::get('/', [PsbWizardController::class, 'dashboard'])->name('app.dashboard');
    Route::get('/psb/create', [PsbWizardController::class, 'createChoice'])->name('psb.create.choice');
    Route::get('/psb/new', [PsbWizardController::class, 'createNew'])->name('psb.new');
    Route::post('/psb/continuation/new', [PsbWizardController::class, 'createContinuation'])->name('psb.continuation.new');
    Route::post('/psb/{registration}/delete', [PsbWizardController::class, 'destroyRegistration'])->name('psb.delete');
    Route::get('/psb/{registration}/qr', [PsbWizardController::class, 'showQr'])->name('psb.qr');
    Route::get('/psb/{registration}/proof-pdf', [PsbWizardController::class, 'downloadProofPdf'])->name('psb.proof.pdf');
    Route::get('/psb/{registration}/continuation', [PsbWizardController::class, 'showContinuationForm'])->name('psb.continuation.form');
    Route::post('/psb/{registration}/continuation', [PsbWizardController::class, 'saveContinuationForm'])->name('psb.continuation.save');

    Route::get('/psb/wizard', [PsbWizardController::class, 'show'])->name('psb.wizard');
    Route::post('/psb/step-1', [PsbWizardController::class, 'saveStep1'])->name('psb.step1');
    Route::post('/psb/step-2', [PsbWizardController::class, 'saveStep2'])->name('psb.step2');
    Route::post('/psb/step-3', [PsbWizardController::class, 'saveStep3Submit'])->name('psb.step3.submit');
    Route::get('/wilayah/options', [WilayahController::class, 'options'])->name('app.wilayah.options');

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
        Route::get('/registrations/assessments', [RegistrationAdminController::class, 'assessments'])->name('admin.registrations.assessments');
        Route::post('/registrations/assessments', [RegistrationAdminController::class, 'saveAssessments'])->name('admin.registrations.assessments.save');
        Route::post('/registrations/{registration}/assessment', [RegistrationAdminController::class, 'saveAssessment'])->name('admin.registrations.assessment.save');
        Route::get('/registrations/data', [RegistrationAdminController::class, 'data'])->name('admin.registrations.data');
        Route::get('/registrations/export', [RegistrationAdminController::class, 'export'])->name('admin.registrations.export');
        Route::middleware(['role:admin,ustadz'])->group(function () {
            Route::get('/registrations/scan', [RegistrationAdminController::class, 'scanPage'])->name('admin.registrations.scan.page');
            Route::get('/registrations/scan/{registration:registration_no}', [RegistrationAdminController::class, 'show'])->name('admin.registrations.scan');
            Route::get('/registrations/proofs/download', [RegistrationAdminController::class, 'downloadCompleteProofs'])->name('admin.registrations.proofs.download');
            Route::get('/registrations/qr-cards/print', [RegistrationAdminController::class, 'printQrCardsPdf'])->name('admin.registrations.qr-cards.print');
        });
        Route::delete('/registrations/{registration}', [RegistrationAdminController::class, 'destroy'])->name('admin.registrations.destroy');
        Route::get('/registrations/{registration}/proof-pdf', [RegistrationAdminController::class, 'downloadProofPdf'])->name('admin.registrations.proof.pdf');
        Route::get('/registrations/{registration}/continuation', [RegistrationAdminController::class, 'editContinuation'])->name('admin.registrations.continuation.edit');
        Route::post('/registrations/{registration}/continuation', [RegistrationAdminController::class, 'saveContinuation'])->name('admin.registrations.continuation.update');
        Route::get('/registrations/{registration}/edit', [RegistrationAdminController::class, 'edit'])->name('admin.registrations.edit');
        Route::post('/registrations/{registration}/step-1', [RegistrationAdminController::class, 'saveStep1'])->name('admin.registrations.step1');
        Route::post('/registrations/{registration}/step-2', [RegistrationAdminController::class, 'saveStep2'])->name('admin.registrations.step2');
        Route::post('/registrations/{registration}/step-3', [RegistrationAdminController::class, 'saveStep3'])->name('admin.registrations.step3');
        Route::get('/registrations/{registration}', [RegistrationAdminController::class, 'show'])->name('admin.registrations.show');
        Route::get('/wilayah/options', [WilayahController::class, 'options'])->name('admin.wilayah.options');
        Route::post(
            'registrations/{registration}/graduation',
            [\App\Http\Controllers\Admin\RegistrationAdminController::class, 'setGraduation']
        )->name('admin.registrations.graduation');
        Route::middleware(['role:admin'])->group(function () {
            Route::get('/users', [UserAdminController::class, 'index'])
                ->name('admin.users.index');
            Route::get('/users/data', [UserAdminController::class, 'data'])
                ->name('admin.users.data');
            Route::post('/users', [UserAdminController::class, 'store'])
                ->name('admin.users.store');

            Route::post('/users/{user}/update', [UserAdminController::class, 'update'])
                ->name('admin.users.update');

            Route::post('/users/{user}/update-password', [UserAdminController::class, 'updatePassword'])
                ->name('admin.users.updatePassword');
            Route::post('/users/{user}/delete', [UserAdminController::class, 'destroy'])
                ->name('admin.users.destroy');

            Route::get('/periods', [PeriodController::class, 'index'])->name('admin.periods.index');
            Route::post('/periods/save', [PeriodController::class, 'save'])->name('admin.periods.save');
            Route::post('/periods/{period}/activate', [PeriodController::class, 'activate'])->name('admin.periods.activate');
            Route::post('/periods/{period}/delete', [PeriodController::class, 'destroy'])->name('admin.periods.destroy');

            Route::get('/staff', [StaffAdminController::class, 'index'])->name('admin.staff.index');
            Route::get('/staff/data', [StaffAdminController::class, 'data'])->name('admin.staff.data');
            Route::post('/staff', [StaffAdminController::class, 'store'])->name('admin.staff.store');
            Route::post('/staff/{user}/update', [StaffAdminController::class, 'update'])->name('admin.staff.update');
            Route::post('/staff/{user}/reset-password', [StaffAdminController::class, 'resetPassword'])
                ->name('admin.staff.resetPassword');
            Route::post('/staff/{user}/delete', [StaffAdminController::class, 'destroy'])->name('admin.staff.destroy');
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

        Route::get('/events', [EventController::class, 'index'])->name('admin.events.index');
        Route::get('/events/create', [EventController::class, 'create'])->name('admin.events.create');
        Route::post('/events', [EventController::class, 'store'])->name('admin.events.store');
        Route::get('/events/{event}', [EventController::class, 'show'])->name('admin.events.show');
        Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('admin.events.edit');
        Route::put('/events/{event}', [EventController::class, 'update'])->name('admin.events.update');
        Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('admin.events.destroy');
        Route::get('/events/{event}/attendances/export', [EventController::class, 'exportAttendances'])->name('admin.events.attendances.export');
        Route::delete('/events/{event}/attendances/{attendance}', [EventController::class, 'destroyAttendance'])->name('admin.events.attendance.destroy');
        Route::post('/events/{event}/scan-attendance', [EventController::class, 'scanAttendance'])->name('admin.events.scan-attendance');

        Route::get('/documents', [AdminDocumentController::class, 'index'])->name('admin.documents.index');
        Route::get('/documents/create', [AdminDocumentController::class, 'create'])->name('admin.documents.create');
        Route::post('/documents', [AdminDocumentController::class, 'store'])->name('admin.documents.store');
        Route::get('/documents/{document}', [AdminDocumentController::class, 'show'])->name('admin.documents.show');
        Route::get('/documents/{document}/edit', [AdminDocumentController::class, 'edit'])->name('admin.documents.edit');
        Route::put('/documents/{document}', [AdminDocumentController::class, 'update'])->name('admin.documents.update');
        Route::delete('/documents/{document}', [AdminDocumentController::class, 'destroy'])->name('admin.documents.destroy');
        Route::get('/documents/{document}/preview', [AdminDocumentController::class, 'preview'])->name('admin.documents.preview');
        Route::get('/documents/{document}/download', [AdminDocumentController::class, 'download'])->name('admin.documents.download');
    });
});

Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/informasi', [NewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [NewsController::class, 'show'])->name('news.show');

Route::get('/register', [ParentAuthController::class, 'showLogin'])->name('parent.register');
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
Route::get('/migrate', function () {
    // Run the migrations
    Artisan::call('migrate', ['--force' => true]);

    return "Migrations completed successfully!";
});

// require __DIR__ . '/auth.php';
