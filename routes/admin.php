<?php

use App\Http\Controllers\Admin\BadgeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\ModerationController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Routes protégées pour l'administration de la plateforme.
| Toutes les routes sont protégées par le middleware 'admin'.
|
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');

    // Gestion des utilisateurs
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
        Route::patch('/{user}/status', [UserController::class, 'updateStatus'])->name('updateStatus');
        Route::patch('/{user}/type', [UserController::class, 'updateUserType'])->name('updateUserType');
        Route::patch('/{user}/verify', [UserController::class, 'verify'])->name('verify');
        Route::post('/{user}/badge', [UserController::class, 'awardBadge'])->name('awardBadge');
        Route::delete('/{user}/badge', [UserController::class, 'revokeBadge'])->name('revokeBadge');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::get('/export/csv', [UserController::class, 'export'])->name('export');
    });

    // Gestion des badges
    Route::prefix('badges')->name('badges.')->group(function () {
        Route::get('/', [BadgeController::class, 'index'])->name('index');
        Route::get('/list', [BadgeController::class, 'list'])->name('list');
        Route::post('/', [BadgeController::class, 'store'])->name('store');
        Route::get('/{badge}', [BadgeController::class, 'show'])->name('show');
        Route::patch('/{badge}', [BadgeController::class, 'update'])->name('update');
        Route::delete('/{badge}', [BadgeController::class, 'destroy'])->name('destroy');
        Route::post('/{badge}/toggle-status', [BadgeController::class, 'toggleStatus'])->name('toggleStatus');
        Route::post('/create-defaults', [BadgeController::class, 'createDefaults'])->name('createDefaults');
        Route::post('/bulk-award', [BadgeController::class, 'bulkAward'])->name('bulkAward');
        Route::get('/awarded-users/{badge}', [BadgeController::class, 'awardedUsers'])->name('awardedUsers');
        Route::post('/check-all-users', [BadgeController::class, 'checkAllUsers'])->name('checkAllUsers');
    });

    // Modération des avis
    Route::prefix('moderation')->name('moderation.')->group(function () {
        Route::get('/', [ModerationController::class, 'index'])->name('index');
        Route::get('/pending-reviews', [ModerationController::class, 'pendingReviews'])->name('pendingReviews');
        Route::get('/flagged-reviews', [ModerationController::class, 'flaggedReviews'])->name('flaggedReviews');
        Route::get('/reports', [ModerationController::class, 'reports'])->name('reports');
        Route::get('/history', [ModerationController::class, 'history'])->name('history');

        // Actions de modération
        Route::post('/reviews/{review}/moderate', [ModerationController::class, 'moderateReview'])->name('moderateReview');
        Route::post('/bulk-moderate', [ModerationController::class, 'bulkModerate'])->name('bulkModerate');
        Route::post('/reports/{report}/process', [ModerationController::class, 'processReport'])->name('processReport');

        // Détails
        Route::get('/reviews/{review}', [ModerationController::class, 'showReview'])->name('showReview');
        Route::get('/reports/{report}', [ModerationController::class, 'showReport'])->name('showReport');
    });

    // Gestion KYC des prestataires
    Route::prefix('kyc')->name('kyc.')->group(function () {
        Route::get('/', [KycController::class, 'index'])->name('index');
        Route::get('/statistics', [KycController::class, 'statistics'])->name('statistics');
        Route::get('/history', [KycController::class, 'history'])->name('history');
        Route::get('/export', [KycController::class, 'export'])->name('export');
        Route::get('/{user}', [KycController::class, 'show'])->name('show');
        Route::patch('/{user}/review', [KycController::class, 'review'])->name('review');
        Route::patch('/{user}/approve', [KycController::class, 'approve'])->name('approve');
        Route::patch('/{user}/reject', [KycController::class, 'reject'])->name('reject');
        Route::get('/{user}/documents/{field}', [KycController::class, 'downloadDocument'])->name('document.download');
    });

    // Gestion des services et catégories
    Route::get('/services', [DashboardController::class, 'services'])->name('services');
    Route::get('/bookings', [DashboardController::class, 'bookings'])->name('bookings');
});
