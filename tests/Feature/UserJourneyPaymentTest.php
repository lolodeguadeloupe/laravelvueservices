<?php

use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Créer les rôles
    Role::create(['name' => 'client', 'guard_name' => 'web']);
    Role::create(['name' => 'provider', 'guard_name' => 'web']);
    Role::create(['name' => 'admin', 'guard_name' => 'web']);
});

test('complete user journey from booking to payment', function () {
    // 1. Créer les données de test
    $category = Category::create([
        'name' => 'Nettoyage',
        'description' => 'Services de nettoyage à domicile',
        'icon' => 'cleaning',
        'status' => 'active',
    ]);

    $provider = User::factory()->create([
        'name' => 'Marie Dubois',
        'email' => 'marie@example.com',
    ]);
    $provider->assignRole('provider');

    $client = User::factory()->create([
        'name' => 'Jean Martin', 
        'email' => 'jean@example.com',
    ]);
    $client->assignRole('client');

    $service = Service::create([
        'title' => 'Nettoyage complet appartement',
        'description' => 'Nettoyage en profondeur de votre appartement',
        'provider_id' => $provider->id,
        'category_id' => $category->id,
        'price' => 120.00,
        'price_min' => 100.00,
        'price_max' => 150.00,
        'duration' => 180,
        'status' => 'active',
    ]);

    // 2. Simuler le workflow complet de réservation avec Pest Browser Testing
    $page = visit('/login');
    
    // Login client
    $page->fill('email', 'jean@example.com')
         ->fill('password', 'password')
         ->click('Se connecter');

    // Rechercher et réserver un service
    $page->visit('/')
         ->assertSee('Nettoyage complet appartement')
         ->click('Réserver ce service');

    // Remplir le formulaire de réservation
    $page->fill('preferred_datetime', now()->addDays(2)->format('Y-m-d H:i'))
         ->fill('client_address[street]', '123 Rue de la Paix')
         ->fill('client_address[city]', 'Paris')
         ->fill('client_address[postal_code]', '75001')
         ->fill('client_notes', 'Appartement 3 pièces, attention aux plantes')
         ->click('Envoyer la demande');

    $page->assertSee('Votre demande de service a été envoyée');

    // 3. Récupérer la réservation créée
    $booking = BookingRequest::where('client_id', $client->id)->first();
    expect($booking)->not->toBeNull();

    // 4. Simuler les actions du prestataire
    $page->visit('/logout');
    
    // Login prestataire
    $page->visit('/login')
         ->fill('email', 'marie@example.com')
         ->fill('password', 'password')
         ->click('Se connecter');

    // Voir les demandes et envoyer un devis
    $page->visit('/dashboard')
         ->assertSee('Nouvelle demande')
         ->click('Voir la demande');

    $page->fill('quoted_price', '120.00')
         ->fill('estimated_duration', '180')
         ->fill('confirmed_datetime', now()->addDays(2)->format('Y-m-d H:i'))
         ->fill('provider_notes', 'Je peux réaliser cette prestation')
         ->click('Envoyer le devis');

    // Accepter la demande
    $page->click('Accepter la demande');

    // Démarrer l'intervention
    $page->click('Démarrer l\'intervention')
         ->fill('provider_location[latitude]', '48.8566')
         ->fill('provider_location[longitude]', '2.3522')
         ->click('Commencer');

    // Terminer l'intervention avec paiement
    $page->click('Terminer l\'intervention')
         ->fill('work_summary', 'Nettoyage effectué avec succès. Toutes les pièces ont été nettoyées.')
         ->fill('final_price', '120.00')
         ->click('Finaliser');

    // 5. Vérifier que le système de paiement s'est déclenché
    $page->assertSee('Commission plateforme: 18,00€') // 15% de 120€
         ->assertSee('Votre gain: 102,00€'); // 120€ - 18€

    // 6. Vérifications en base de données
    $booking->refresh();
    expect($booking->status)->toBe('completed');
    expect($booking->final_price)->toBe('120.00');

    // Vérifier la création du portefeuille
    $wallet = Wallet::where('user_id', $provider->id)->first();
    expect($wallet)->not->toBeNull();
    expect($wallet->pending_balance)->toBe('102.00'); // Gain prestataire

    // Vérifier les transactions
    $providerTransaction = Transaction::where('user_id', $provider->id)->first();
    expect($providerTransaction)->not->toBeNull();
    expect($providerTransaction->amount)->toBe('102.00');
    expect($providerTransaction->type)->toBe('service_payment');

    $commissionTransaction = Transaction::where('type', 'platform_commission')->first();
    expect($commissionTransaction)->not->toBeNull();
    expect($commissionTransaction->amount)->toBe('18.00');

    // Vérifier les factures
    $serviceInvoice = Invoice::where('type', 'service_invoice')->first();
    expect($serviceInvoice)->not->toBeNull();
    expect($serviceInvoice->total_amount)->toBe('144.00'); // 120€ + 20% TVA

    $commissionInvoice = Invoice::where('type', 'commission_invoice')->first();
    expect($commissionInvoice)->not->toBeNull();
    expect($commissionInvoice->total_amount)->toBe('21.60'); // 18€ + 20% TVA

    // 7. Tester l'interface prestataire - demande de retrait
    $page->visit('/wallet')
         ->assertSee('102,00€') // Solde en attente
         ->click('Demander un retrait');

    // Confirmer les fonds d'abord (simule validation admin)
    $wallet->confirmFunds(102.00);
    $wallet->refresh();
    expect($wallet->balance)->toBe('102.00');
    expect($wallet->pending_balance)->toBe('0.00');

    // Maintenant faire la demande de retrait
    $page->refresh()
         ->fill('amount', '100.00')
         ->select('payout_method', 'bank_transfer')
         ->fill('bank_details[iban]', 'FR1420041010050500013M02606')
         ->click('Demander le retrait');

    $page->assertSee('Demande de retrait créée');

    // Vérifier en base
    $payoutRequest = $wallet->payoutRequests()->first();
    expect($payoutRequest)->not->toBeNull();
    expect($payoutRequest->amount)->toBe('100.00');
    expect($payoutRequest->status)->toBe('pending');

    // Vérifier que les fonds sont gelés
    $wallet->refresh();
    expect($wallet->frozen_balance)->toBe('100.00');

})->timeout(60);

test('dispute workflow when client cancels after service', function () {
    // Créer une réservation terminée
    $provider = User::factory()->create();
    $provider->assignRole('provider');
    
    $client = User::factory()->create();
    $client->assignRole('client');

    $category = Category::create([
        'name' => 'Test Category',
        'description' => 'Category for testing',
        'icon' => 'test-icon',
        'status' => 'active',
    ]);

    $service = Service::create([
        'title' => 'Test Service',
        'description' => 'Service for testing',
        'provider_id' => $provider->id,
        'category_id' => $category->id,
        'price' => 150.00,
        'status' => 'active',
    ]);

    $booking = BookingRequest::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'service_id' => $service->id,
        'status' => 'completed',
        'quoted_price' => 150.00,
        'final_price' => 150.00,
        'completed_at' => now(),
        'preferred_datetime' => now()->addDay(),
        'client_address' => ['street' => 'Test Street', 'city' => 'Test City'],
    ]);

    // Client se connecte et demande annulation/remboursement
    $page = visit('/login');
    $page->fill('email', $client->email)
         ->fill('password', 'password')
         ->click('Se connecter');

    $page->visit("/bookings/{$booking->uuid}")
         ->click('Annuler la réservation')
         ->fill('cancellation_reason', 'Service non satisfaisant, je demande un remboursement')
         ->click('Confirmer l\'annulation');

    $page->assertSee('Votre demande de remboursement sera examinée');

    // Vérifier qu'un litige a été créé automatiquement
    $booking->refresh();
    expect($booking->status)->toBe('cancelled');

    $dispute = $booking->disputes()->first();
    expect($dispute)->not->toBeNull();
    expect($dispute->type)->toBe('refund_request');
    expect($dispute->reported_by)->toBe($client->id);

})->timeout(30);

test('admin can manage disputes and process refunds', function () {
    // Créer admin
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Créer un litige existant
    $provider = User::factory()->create();
    $provider->assignRole('provider');
    
    $client = User::factory()->create();
    $client->assignRole('client');

    $category = Category::create(['name' => 'Test', 'status' => 'active']);
    $service = Service::create([
        'title' => 'Test Service',
        'provider_id' => $provider->id,
        'category_id' => $category->id,
        'price' => 200.00,
        'status' => 'active',
    ]);

    $booking = BookingRequest::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'service_id' => $service->id,
        'status' => 'completed',
        'final_price' => 200.00,
        'preferred_datetime' => now()->addDay(),
        'client_address' => ['street' => 'Test', 'city' => 'Test'],
    ]);

    $dispute = $booking->createDispute(
        $client,
        'refund_request',
        'Service non conforme aux attentes',
        [],
        200.00
    );

    // Admin se connecte et gère le litige
    $page = visit('/login');
    $page->fill('email', $admin->email)
         ->fill('password', 'password')
         ->click('Se connecter');

    $page->visit('/admin/disputes')
         ->assertSee('Service non conforme')
         ->click('Traiter le litige');

    // Admin accorde un remboursement partiel
    $page->fill('refund_amount', '100.00')
         ->fill('resolution_notes', 'Remboursement partiel accordé après investigation')
         ->click('Résoudre le litige');

    $page->assertSee('Litige résolu avec succès');

    // Vérifications
    $dispute->refresh();
    expect($dispute->status)->toBe('resolved');
    expect($dispute->refund_amount)->toBe('100.00');

})->timeout(30);
