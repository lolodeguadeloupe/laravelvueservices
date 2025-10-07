<?php

use App\Http\Controllers\Provider\RegistrationController;
use App\Http\Controllers\Provider\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Provider Routes
|--------------------------------------------------------------------------
|
| Routes spécifiques aux prestataires pour leur inscription,
| gestion de profil et services.
|
*/

// Routes d'inscription prestataire (public)
Route::prefix('provider')->name('provider.')->group(function () {

    // Inscription multi-étapes
    Route::get('/registration', [RegistrationController::class, 'show'])->name('registration.show');
    Route::post('/registration/step1', [RegistrationController::class, 'step1'])->name('registration.step1');
    Route::post('/registration/step2', [RegistrationController::class, 'step2'])->name('registration.step2');
    Route::post('/registration/step3', [RegistrationController::class, 'step3'])->name('registration.step3');
    Route::get('/registration/progress', [RegistrationController::class, 'getProgress'])->name('registration.progress');
    Route::delete('/registration/restart', [RegistrationController::class, 'restart'])->name('registration.restart');
});

// Routes protégées pour prestataires authentifiés
Route::middleware(['auth', 'user.type:provider'])->prefix('provider')->name('provider.')->group(function () {

    // Dashboard prestataire
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
    Route::get('/earnings', [DashboardController::class, 'earnings'])->name('earnings');

    // Gestion du profil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // Gestion des services
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('services.show');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::patch('/services/{service}', [ServiceController::class, 'update'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy');
    Route::patch('/services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])->name('services.toggle-status');

    // Galerie de services
    Route::post('/services/{service}/media', [ServiceController::class, 'uploadMedia'])->name('services.media.upload');
    Route::delete('/services/{service}/media/{media}', [ServiceController::class, 'deleteMedia'])->name('services.media.delete');
    Route::patch('/services/{service}/media/{media}/primary', [ServiceController::class, 'setPrimaryMedia'])->name('services.media.primary');

    // Gestion des réservations
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::patch('/bookings/{booking}/accept', [BookingController::class, 'accept'])->name('bookings.accept');
    Route::patch('/bookings/{booking}/decline', [BookingController::class, 'decline'])->name('bookings.decline');
    Route::patch('/bookings/{booking}/complete', [BookingController::class, 'complete'])->name('bookings.complete');
    Route::patch('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    // Disponibilités et calendrier
    Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
    Route::patch('/availability', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::post('/availability/time-off', [AvailabilityController::class, 'addTimeOff'])->name('availability.time-off');
    Route::delete('/availability/time-off/{timeOff}', [AvailabilityController::class, 'removeTimeOff'])->name('availability.time-off.delete');

    // Avis et évaluations
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{booking}/respond', [ReviewController::class, 'respond'])->name('reviews.respond');

    // Paiements et finances
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
    Route::get('/invoices', [PaymentController::class, 'invoices'])->name('invoices.index');
    Route::get('/invoices/{invoice}/download', [PaymentController::class, 'downloadInvoice'])->name('invoices.download');

    // Support et messages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');
    Route::patch('/messages/{conversation}/mark-read', [MessageController::class, 'markAsRead'])->name('messages.mark-read');

    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support/tickets', [SupportController::class, 'createTicket'])->name('support.tickets.create');
    Route::get('/support/tickets/{ticket}', [SupportController::class, 'showTicket'])->name('support.tickets.show');

    // Paramètres
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
    Route::patch('/settings/privacy', [SettingsController::class, 'updatePrivacy'])->name('settings.privacy');
    Route::patch('/settings/banking', [SettingsController::class, 'updateBanking'])->name('settings.banking');
});
