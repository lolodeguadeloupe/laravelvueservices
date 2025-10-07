<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Provider\ServiceController;
use App\Http\Controllers\Provider\ServiceMediaController;
use App\Http\Controllers\Provider\ServicePackageController;
use App\Http\Controllers\Provider\ServiceZoneController;
use App\Http\Controllers\ProviderDashboardController;
use App\Http\Controllers\ProviderRegistrationController;
use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes publiques des services
Route::get('services', [PublicServiceController::class, 'index'])->name('services.index');
Route::get('services/{service}', [PublicServiceController::class, 'show'])->name('services.show');

// Pages statiques
Route::get('about', [PageController::class, 'about'])->name('about');
Route::get('how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('contact', [PageController::class, 'contact'])->name('contact');
Route::get('help', [PageController::class, 'help'])->name('help');
Route::get('terms', [PageController::class, 'terms'])->name('terms');
Route::get('privacy', [PageController::class, 'privacy'])->name('privacy');

// Routes d'inscription prestataire
Route::prefix('provider/registration')->name('provider.registration.')->group(function () {
    Route::get('step1', [ProviderRegistrationController::class, 'showStep1'])->name('step1');
    Route::post('step1', [ProviderRegistrationController::class, 'processStep1'])->name('step1.process');

    Route::get('step2', [ProviderRegistrationController::class, 'showStep2'])->name('step2');
    Route::post('step2', [ProviderRegistrationController::class, 'processStep2'])->name('step2.process');

    Route::get('step3', [ProviderRegistrationController::class, 'showStep3'])->name('step3');
    Route::post('step3', [ProviderRegistrationController::class, 'processStep3'])->name('step3.process');

    Route::get('success', [ProviderRegistrationController::class, 'success'])->name('success');
});

// Routes d'administration des prestataires
Route::middleware(['auth', 'can:verify providers'])->prefix('admin/providers')->name('admin.providers.')->group(function () {
    Route::get('pending', [ProviderRegistrationController::class, 'pendingProviders'])->name('pending');
    Route::patch('{provider}/approve', [ProviderRegistrationController::class, 'approve'])->name('approve');
    Route::patch('{provider}/reject', [ProviderRegistrationController::class, 'reject'])->name('reject');
});

// Routes du dashboard prestataire
Route::middleware(['auth', 'verified', 'user_type:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
    Route::get('bookings', [ProviderDashboardController::class, 'bookings'])->name('bookings');
    Route::get('earnings', [ProviderDashboardController::class, 'earnings'])->name('earnings');
    Route::get('analytics', [ProviderDashboardController::class, 'analytics'])->name('analytics');
    Route::get('calendar', [ProviderDashboardController::class, 'calendar'])->name('calendar');
    Route::get('profile', [ProviderDashboardController::class, 'profile'])->name('profile');
    Route::put('profile', [ProviderDashboardController::class, 'updateProfile'])->name('profile.update');

    // Routes de gestion des services
    Route::resource('services', ServiceController::class);
    Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

    // Routes de gestion des forfaits de services
    Route::get('services/{service}/packages', [ServicePackageController::class, 'index'])->name('services.packages.index');
    Route::post('services/{service}/packages', [ServicePackageController::class, 'store'])->name('services.packages.store');
    Route::get('services/{service}/packages/{servicePackage}', [ServicePackageController::class, 'show'])->name('services.packages.show');
    Route::put('services/{service}/packages/{servicePackage}', [ServicePackageController::class, 'update'])->name('services.packages.update');
    Route::delete('services/{service}/packages/{servicePackage}', [ServicePackageController::class, 'destroy'])->name('services.packages.destroy');
    Route::patch('services/{service}/packages/{servicePackage}/toggle-status', [ServicePackageController::class, 'toggleStatus'])->name('services.packages.toggle-status');
    Route::post('services/{service}/packages/reorder', [ServicePackageController::class, 'reorder'])->name('services.packages.reorder');

    // Routes de gestion des zones d'intervention
    Route::get('services/{service}/zones', [ServiceZoneController::class, 'index'])->name('services.zones.index');
    Route::post('services/{service}/zones', [ServiceZoneController::class, 'store'])->name('services.zones.store');
    Route::get('services/{service}/zones/{serviceZone}', [ServiceZoneController::class, 'show'])->name('services.zones.show');
    Route::put('services/{service}/zones/{serviceZone}', [ServiceZoneController::class, 'update'])->name('services.zones.update');
    Route::delete('services/{service}/zones/{serviceZone}', [ServiceZoneController::class, 'destroy'])->name('services.zones.destroy');
    Route::patch('services/{service}/zones/{serviceZone}/toggle-status', [ServiceZoneController::class, 'toggleStatus'])->name('services.zones.toggle-status');
    Route::post('services/{service}/zones/{serviceZone}/check-location', [ServiceZoneController::class, 'checkLocation'])->name('services.zones.check-location');

    // Routes de gestion des médias de services
    Route::get('services/{service}/media', [ServiceMediaController::class, 'index'])->name('services.media.index');
    Route::post('services/{service}/media', [ServiceMediaController::class, 'store'])->name('services.media.store');
    Route::get('services/{service}/media/{serviceMedia}', [ServiceMediaController::class, 'show'])->name('services.media.show');
    Route::put('services/{service}/media/{serviceMedia}', [ServiceMediaController::class, 'update'])->name('services.media.update');
    Route::delete('services/{service}/media/{serviceMedia}', [ServiceMediaController::class, 'destroy'])->name('services.media.destroy');
    Route::patch('services/{service}/media/{serviceMedia}/set-primary', [ServiceMediaController::class, 'setPrimary'])->name('services.media.set-primary');
    Route::post('services/{service}/media/reorder', [ServiceMediaController::class, 'reorder'])->name('services.media.reorder');
});

// Routes de réservations
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('bookings/{booking:uuid}', [BookingController::class, 'show'])->name('bookings.show');

    // Routes pour les clients
    Route::middleware('role:client')->group(function () {
        Route::get('services/{service}/book', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('bookings', [BookingController::class, 'store'])->name('bookings.store');
    });

    // Routes pour les prestataires
    Route::middleware('role:provider')->group(function () {
        Route::patch('bookings/{booking}/accept', [BookingController::class, 'accept'])->name('bookings.accept');
        Route::patch('bookings/{booking}/reject', [BookingController::class, 'reject'])->name('bookings.reject');
        Route::patch('bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
        Route::patch('bookings/{booking}/quote', [BookingController::class, 'quote'])->name('bookings.quote');
    });

    // Route commune pour l'annulation
    Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
});

// Routes de paiement
Route::middleware(['auth'])->group(function () {
    Route::get('bookings/{booking}/payment', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('bookings/{booking}/payment/intent', [PaymentController::class, 'createIntent'])->name('payments.intent');
    Route::post('payments/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
});

// Webhook Stripe (sans auth)
Route::post('webhooks/stripe/payments', [PaymentController::class, 'webhook'])->name('payments.webhook');

// Routes des avis
Route::middleware(['auth'])->group(function () {
    // Routes publiques des avis
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');

    // Création d'avis
    Route::get('bookings/{booking}/review', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('bookings/{booking}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Réponses aux avis
    Route::post('reviews/{review}/respond', [ReviewController::class, 'respond'])->name('reviews.respond');

    // Réactions aux avis
    Route::post('reviews/{review}/helpful', [ReviewController::class, 'helpful'])->name('reviews.helpful');
    Route::post('reviews/{review}/not-helpful', [ReviewController::class, 'notHelpful'])->name('reviews.not-helpful');
    Route::post('reviews/{review}/report', [ReviewController::class, 'report'])->name('reviews.report');
});

// Routes d'administration des avis
Route::middleware(['auth', 'can:moderate-reviews'])->prefix('admin/reviews')->name('admin.reviews.')->group(function () {
    Route::get('moderate', [ReviewController::class, 'moderate'])->name('moderate');
    Route::post('{review}/approve', [ReviewController::class, 'approve'])->name('approve');
    Route::post('{review}/reject', [ReviewController::class, 'reject'])->name('reject');
});

// Routes d'administration des badges
Route::middleware(['auth', 'role:admin'])->prefix('admin/badges')->name('admin.badges.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\BadgeController::class, 'index'])->name('index');
    Route::get('/list', [App\Http\Controllers\Admin\BadgeController::class, 'list'])->name('list');
    Route::post('/', [App\Http\Controllers\Admin\BadgeController::class, 'store'])->name('store');
    Route::get('/{badge}', [App\Http\Controllers\Admin\BadgeController::class, 'show'])->name('show');
    Route::put('/{badge}', [App\Http\Controllers\Admin\BadgeController::class, 'update'])->name('update');
    Route::delete('/{badge}', [App\Http\Controllers\Admin\BadgeController::class, 'destroy'])->name('destroy');
    Route::post('/{badge}/toggle-status', [App\Http\Controllers\Admin\BadgeController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/check-all', [App\Http\Controllers\Admin\BadgeController::class, 'checkAll'])->name('check-all');
    Route::post('/check-user', [App\Http\Controllers\Admin\BadgeController::class, 'checkUser'])->name('check-user');
    Route::get('/stats', [App\Http\Controllers\Admin\BadgeController::class, 'stats'])->name('stats');
    Route::post('/award', [App\Http\Controllers\Admin\BadgeController::class, 'awardBadge'])->name('award');
    Route::post('/revoke', [App\Http\Controllers\Admin\BadgeController::class, 'revokeBadge'])->name('revoke');
});

// Routes des profils utilisateurs
Route::get('profile', [App\Http\Controllers\ProfileController::class, 'index'])->middleware('auth')->name('profile.index');
Route::get('users/{user}', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');

// Routes des badges utilisateurs
Route::middleware(['auth'])->group(function () {
    Route::get('profile/badges', [App\Http\Controllers\ProfileController::class, 'badges'])->name('profile.badges');
    Route::get('users/{user}/badges', [App\Http\Controllers\ProfileController::class, 'badges'])->name('users.badges');
    Route::post('profile/badges/toggle-visibility', [App\Http\Controllers\ProfileController::class, 'toggleBadgeVisibility'])->name('profile.badges.toggle-visibility');
    Route::post('profile/badges/toggle-featured', [App\Http\Controllers\ProfileController::class, 'toggleBadgeFeatured'])->name('profile.badges.toggle-featured');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
