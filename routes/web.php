<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProviderDashboardController;
use App\Http\Controllers\ProviderRegistrationController;
use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
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
Route::middleware(['auth', 'verified', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::get('dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
    Route::get('bookings', [ProviderDashboardController::class, 'bookings'])->name('bookings');
    Route::get('earnings', [ProviderDashboardController::class, 'earnings'])->name('earnings');
    Route::get('profile', [ProviderDashboardController::class, 'profile'])->name('profile');
    Route::put('profile', [ProviderDashboardController::class, 'updateProfile'])->name('profile.update');
    
    // Routes de gestion des services
    Route::resource('services', ServiceController::class);
    Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');
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

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
