<?php

use App\Http\Controllers\Backend\Admin\AboutUsBackendController;
use App\Http\Controllers\Backend\Admin\AdminBackendController;
use App\Http\Controllers\Backend\Admin\ContactUsBackendController;
use App\Http\Controllers\Backend\Admin\ContactUsRequestBackendController;
use App\Http\Controllers\Backend\Admin\PrivacyPolicyBackendController;
use App\Http\Controllers\Backend\Admin\SliderBackendController;
use App\Http\Controllers\Backend\Admin\TermsConditionBackendController;
use App\Http\Controllers\Backend\Admin\UserBackendController;
use App\Http\Controllers\Backend\Auth\AuthBackendController;
use App\Http\Controllers\Backend\Auth\TwoFactorChallengeController;
use App\Http\Controllers\Backend\Auth\TwoFactorEnrollmentController;
use App\Http\Controllers\Backend\Auth\TwoFactorSettingsController;
use App\Http\Controllers\Backend\Dashboard\DashboardBackendController;
use App\Http\Controllers\Backend\Support\SupportBackendController;
use App\Http\Controllers\Frontend\PageController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::get('/login', [AuthBackendController::class, 'showLoginForm'])->name('login');

Route::get('/', [PageController::class, 'home'])->name('frontend.home');
Route::get('/about', [PageController::class, 'about'])->name('frontend.about');
Route::get('/products', [PageController::class, 'products'])->name('frontend.products');
Route::get('/products/{slug}', [PageController::class, 'show'])->name('frontend.products.show');
Route::get('/contact', [PageController::class, 'contact'])->name('frontend.contact');
Route::post('/contact', [PageController::class, 'store'])->middleware('throttle:5,1')->name('frontend.contact.store');

// Locale prefix wraps ONLY the super_admin group, so it must sit as the outermost
// group here (the package always reads the locale from the very first URL segment).
// Default locale (config('app.locale') / APP_LOCALE) stays unprefixed
// (e.g. /super_admin/dashboard); the other locale gets its segment
// (e.g. /ar/super_admin/dashboard). No other route in this file is touched.
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localizationRedirect'],
], function () {
    Route::prefix('super_admin')->name('super_admin.')->group(function () {
        Route::get('/login', [AuthBackendController::class, 'showLoginForm'])->name('loginUser');
        Route::post('/loginFormSubmit', [AuthBackendController::class, 'loginFormSubmit'])->name('loginFormSubmit');
        Route::post('/logout', [AuthBackendController::class, 'logout'])->name('logout');

        Route::get('/two-factor/challenge', [TwoFactorChallengeController::class, 'create'])->name('twoFactor-challenge')->middleware('admin.2fa.pending:challenge');
        Route::post('/two-factor/challenge', [TwoFactorChallengeController::class, 'store'])->name('twoFactor-verify')->middleware('admin.2fa.pending:challenge');
        Route::get('/two-factor/enroll', [TwoFactorEnrollmentController::class, 'create'])->name('twoFactor-enroll')->middleware('admin.2fa.pending:enrollment');
        Route::post('/two-factor/enroll', [TwoFactorEnrollmentController::class, 'store'])->name('twoFactor-enrollConfirm')->middleware('admin.2fa.pending:enrollment');

        Route::group(['middleware' => ['auth:super_admin']], function () {

            // Dashboard Route :
            // Created By:Qusai Alnablse
            // ==============================================================================
            Route::get('/dashboard', [DashboardBackendController::class, 'index'])->name('dashboard');

            Route::get('/two-factor/settings', [TwoFactorSettingsController::class, 'show'])->name('twoFactor-settings');
            Route::get('/two-factor/recovery-codes', [TwoFactorSettingsController::class, 'recovery'])->name('twoFactor-recovery');
            Route::post('/two-factor/recovery-codes', [TwoFactorSettingsController::class, 'regenerate'])->name('twoFactor-regenerateRecoveryCodes');
            Route::post('/two-factor/disable', [TwoFactorSettingsController::class, 'disable'])->name('twoFactor-disable');

            // Support Tickets :
            // Created By:Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'support_tickets'], function () {
                Route::get('/index', [SupportBackendController::class, 'index'])->name('support_tickets-index');
                Route::get('destroy/{id}', [SupportBackendController::class, 'destroy'])->name('support_tickets-destroy');
            });

            // Users :
            // Created By:Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'users'], function () {
                Route::get('/index', [UserBackendController::class, 'index'])->name('users-index');
                Route::get('/create', [UserBackendController::class, 'create'])->name('users-create');
                Route::post('/store', [UserBackendController::class, 'store'])->name('users-store');
                Route::get('show/{id}', [UserBackendController::class, 'show'])->name('users-show');
                Route::get('edit/{id}', [UserBackendController::class, 'edit'])->name('users-edit');
                Route::post('update/{id}', [UserBackendController::class, 'update'])->name('users-update');
                Route::get('softDelete/{id}', [UserBackendController::class, 'softDelete'])->name('users-softDelete');
                Route::get('/showSoftDelete', [UserBackendController::class, 'showSoftDelete'])->name('users-showSoftDelete');
                Route::get('softDeleteRestore/{id}', [UserBackendController::class, 'softDeleteRestore'])->name('users-softDeleteRestore');
                Route::get('/softDeleteSelected', [UserBackendController::class, 'softDeleteSelected'])->name('users-softDeleteSelected');
                Route::get('/softDeleteRestoreSelected', [UserBackendController::class, 'softDeleteRestoreSelected'])->name('users-softDeleteRestoreSelected');
                Route::get('/activeInactiveSingle/{id}', [UserBackendController::class, 'activeInactiveSingle'])->name('users-activeInactiveSingle');
                Route::get('/activeSelected', [UserBackendController::class, 'activeSelected'])->name('users-activeSelected');
                Route::get('/inactiveSelected', [UserBackendController::class, 'inactiveSelected'])->name('users-inactiveSelected');
                Route::post('/get-available-users', [UserBackendController::class, 'getAvailableUsers']);

            });

            // about_us :
            // Created By Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'about_us'], function () {
                Route::get('/index', [AboutUsBackendController::class, 'index'])->name('about_us-index');
                Route::get('edit/{id}', [AboutUsBackendController::class, 'edit'])->name('about_us-edit');
                Route::post('update/{id}', [AboutUsBackendController::class, 'update'])->name('about_us-update');
            });

            // contact_us :
            // Created By Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'contact_us'], function () {
                Route::get('/index', [ContactUsBackendController::class, 'index'])->name('contact_us-index');
                Route::get('edit/{id}', [ContactUsBackendController::class, 'edit'])->name('contact_us-edit');
                Route::post('update/{id}', [ContactUsBackendController::class, 'update'])->name('contact_us-update');
            });

            // contact_us_requests :
            // Created By Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'contact_us_requests'], function () {
                Route::get('/index', [ContactUsRequestBackendController::class, 'index'])->name('contact_us_requests-index');
                Route::get('destroyMessage/{id}', [ContactUsRequestBackendController::class, 'destroyMessage'])->name('contact_us_requests-destroyMessage');
            });

            // privacy_policies
            // Created By Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'privacy_policies'], function () {
                Route::get('/index', [PrivacyPolicyBackendController::class, 'index'])->name('privacy_policies-index');
                Route::get('/create', [PrivacyPolicyBackendController::class, 'create'])->name('privacy_policies-create');
                Route::post('/store', [PrivacyPolicyBackendController::class, 'store'])->name('privacy_policies-store');
                Route::get('show/{id}', [PrivacyPolicyBackendController::class, 'show'])->name('privacy_policies-show');
                Route::get('edit/{id}', [PrivacyPolicyBackendController::class, 'edit'])->name('privacy_policies-edit');
                Route::post('update/{id}', [PrivacyPolicyBackendController::class, 'update'])->name('privacy_policies-update');
                Route::get('softDelete/{id}', [PrivacyPolicyBackendController::class, 'softDelete'])->name('privacy_policies-softDelete');
                Route::get('/showSoftDelete', [PrivacyPolicyBackendController::class, 'showSoftDelete'])->name('privacy_policies-showSoftDelete');
                Route::get('softDeleteRestore/{id}', [PrivacyPolicyBackendController::class, 'softDeleteRestore'])->name('privacy_policies-softDeleteRestore');
                Route::get('/softDeleteSelected', [PrivacyPolicyBackendController::class, 'softDeleteSelected'])->name('privacy_policies-softDeleteSelected');
                Route::get('/softDeleteRestoreSelected', [PrivacyPolicyBackendController::class, 'softDeleteRestoreSelected'])->name('privacy_policies-softDeleteRestoreSelected');
                Route::get('/activeInactiveSingle/{id}', [PrivacyPolicyBackendController::class, 'activeInactiveSingle'])->name('privacy_policies-activeInactiveSingle');
                Route::get('/activeSelected', [PrivacyPolicyBackendController::class, 'activeSelected'])->name('privacy_policies-activeSelected');
                Route::get('/inactiveSelected', [PrivacyPolicyBackendController::class, 'inactiveSelected'])->name('privacy_policies-inactiveSelected');
            });

            // terms_and_conditions
            // Created By Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'terms_and_conditions'], function () {
                Route::get('/index', [TermsConditionBackendController::class, 'index'])->name('terms_and_conditions-index');
                Route::get('/create', [TermsConditionBackendController::class, 'create'])->name('terms_and_conditions-create');
                Route::post('/store', [TermsConditionBackendController::class, 'store'])->name('terms_and_conditions-store');
                Route::get('show/{id}', [TermsConditionBackendController::class, 'show'])->name('terms_and_conditions-show');
                Route::get('edit/{id}', [TermsConditionBackendController::class, 'edit'])->name('terms_and_conditions-edit');
                Route::post('update/{id}', [TermsConditionBackendController::class, 'update'])->name('terms_and_conditions-update');
                Route::get('softDelete/{id}', [TermsConditionBackendController::class, 'softDelete'])->name('terms_and_conditions-softDelete');
                Route::get('/showSoftDelete', [TermsConditionBackendController::class, 'showSoftDelete'])->name('terms_and_conditions-showSoftDelete');
                Route::get('softDeleteRestore/{id}', [TermsConditionBackendController::class, 'softDeleteRestore'])->name('terms_and_conditions-softDeleteRestore');
                Route::get('/softDeleteSelected', [TermsConditionBackendController::class, 'softDeleteSelected'])->name('terms_and_conditions-softDeleteSelected');
                Route::get('/softDeleteRestoreSelected', [TermsConditionBackendController::class, 'softDeleteRestoreSelected'])->name('terms_and_conditions-softDeleteRestoreSelected');
                Route::get('/activeInactiveSingle/{id}', [TermsConditionBackendController::class, 'activeInactiveSingle'])->name('terms_and_conditions-activeInactiveSingle');
                Route::get('/activeSelected', [TermsConditionBackendController::class, 'activeSelected'])->name('terms_and_conditions-activeSelected');
                Route::get('/inactiveSelected', [TermsConditionBackendController::class, 'inactiveSelected'])->name('terms_and_conditions-inactiveSelected');
            });

            // Slider Routes :
            // Created By Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'sliders'], function () {
                Route::get('/index', [SliderBackendController::class, 'index'])->name('sliders-index');
                Route::get('/create', [SliderBackendController::class, 'create'])->name('sliders-create');
                Route::post('/store', [SliderBackendController::class, 'store'])->name('sliders-store');
                Route::get('show/{id}', [SliderBackendController::class, 'show'])->name('sliders-show');
                Route::get('edit/{id}', [SliderBackendController::class, 'edit'])->name('sliders-edit');
                Route::post('update/{id}', [SliderBackendController::class, 'update'])->name('sliders-update');
                Route::get('softDelete/{id}', [SliderBackendController::class, 'softDelete'])->name('sliders-softDelete');
                Route::get('/showSoftDelete', [SliderBackendController::class, 'showSoftDelete'])->name('sliders-showSoftDelete');
                Route::get('softDeleteRestore/{id}', [SliderBackendController::class, 'softDeleteRestore'])->name('sliders-softDeleteRestore');
                Route::get('/softDeleteSelected', [SliderBackendController::class, 'softDeleteSelected'])->name('sliders-softDeleteSelected');
                Route::get('/softDeleteRestoreSelected', [SliderBackendController::class, 'softDeleteRestoreSelected'])->name('sliders-softDeleteRestoreSelected');
                Route::get('/activeInactiveSingle/{id}', [SliderBackendController::class, 'activeInactiveSingle'])->name('sliders-activeInactiveSingle');
                Route::get('/activeSelected', [SliderBackendController::class, 'activeSelected'])->name('sliders-activeSelected');
                Route::get('/inactiveSelected', [SliderBackendController::class, 'inactiveSelected'])->name('sliders-inactiveSelected');
            });

            // Admins :
            // Created By:Qusai Alnablse
            // ==============================================================================
            Route::group(['prefix' => 'admins'], function () {
                Route::get('/index', [AdminBackendController::class, 'index'])->name('admins-index');
                Route::get('/create', [AdminBackendController::class, 'create'])->name('admins-create');
                Route::post('/store', [AdminBackendController::class, 'store'])->name('admins-store');
                Route::get('show/{id}', [AdminBackendController::class, 'show'])->name('admins-show');
                Route::get('edit/{id}', [AdminBackendController::class, 'edit'])->name('admins-edit');
                Route::post('update/{id}', [AdminBackendController::class, 'update'])->name('admins-update');
                Route::get('softDelete/{id}', [AdminBackendController::class, 'softDelete'])->name('admins-softDelete');
                Route::get('/showSoftDelete', [AdminBackendController::class, 'showSoftDelete'])->name('admins-showSoftDelete');
                Route::get('softDeleteRestore/{id}', [AdminBackendController::class, 'softDeleteRestore'])->name('admins-softDeleteRestore');
                Route::get('/softDeleteSelected', [AdminBackendController::class, 'softDeleteSelected'])->name('admins-softDeleteSelected');
                Route::get('/softDeleteRestoreSelected', [AdminBackendController::class, 'softDeleteRestoreSelected'])->name('admins-softDeleteRestoreSelected');
                Route::get('/activeInactiveSingle/{id}', [AdminBackendController::class, 'activeInactiveSingle'])->name('admins-activeInactiveSingle');
                Route::get('/activeSelected', [AdminBackendController::class, 'activeSelected'])->name('admins-activeSelected');
                Route::get('/inactiveSelected', [AdminBackendController::class, 'inactiveSelected'])->name('admins-inactiveSelected');
                Route::post('/reset-two-factor/{id}', [TwoFactorSettingsController::class, 'reset'])->name('admins-resetTwoFactor');
            });
        });
    });
});
