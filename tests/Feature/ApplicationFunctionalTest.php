<?php

use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Models\Dispute;
use App\Services\CommissionService;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('application loads successfully', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

test('payment system integration works end-to-end', function () {
    // 1. Setup - Créer les rôles et utilisateurs
    Role::create(['name' => 'client', 'guard_name' => 'web']);
    Role::create(['name' => 'provider', 'guard_name' => 'web']);

    $provider = User::factory()->create();
    $provider->assignRole('provider');
    
    $client = User::factory()->create();
    $client->assignRole('client');

    // 2. Créer un service et une réservation
    $category = Category::create([
        'name' => 'Nettoyage',
        'description' => 'Services de nettoyage',
        'icon' => 'cleaning',
        'status' => 'active',
    ]);

    $service = Service::create([
        'title' => 'Nettoyage appartement',
        'description' => 'Service de nettoyage complet',
        'provider_id' => $provider->id,
        'category_id' => $category->id,
        'price' => 120.00,
        'status' => 'active',
    ]);

    $booking = BookingRequest::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'client_id' => $client->id,
        'provider_id' => $provider->id,
        'service_id' => $service->id,
        'status' => 'completed',
        'quoted_price' => 120.00,
        'final_price' => 120.00,
        'completed_at' => now(),
        'preferred_datetime' => now()->addDay(),
        'client_address' => ['street' => 'Test Street', 'city' => 'Paris'],
    ]);

    // 3. Test du système de paiements via API
    $this->actingAs($provider);
    
    $response = $this->patch(route('bookings.finish-intervention', $booking), [
        'work_summary' => 'Travail terminé avec succès',
        'final_price' => 120.00,
        'after_photos' => [],
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    // 4. Vérifications du système de paiements
    $booking->refresh();
    expect($booking->status)->toBe('completed');

    // Vérifier le portefeuille créé
    $wallet = Wallet::where('user_id', $provider->id)->first();
    expect($wallet)->not->toBeNull();
    expect($wallet->pending_balance)->toBe('102.00'); // 120€ - 15% commission

    // Vérifier les transactions
    $providerTransaction = Transaction::where('user_id', $provider->id)->first();
    expect($providerTransaction)->not->toBeNull();
    expect($providerTransaction->amount)->toBe('102.00');
    expect($providerTransaction->type)->toBe('service_payment');

    $commissionTransaction = Transaction::where('type', 'platform_commission')->first();
    expect($commissionTransaction)->not->toBeNull();
    expect($commissionTransaction->amount)->toBe('18.00'); // 15% de 120€

    // Vérifier les factures générées automatiquement
    $serviceInvoice = Invoice::where('type', 'service_invoice')->first();
    expect($serviceInvoice)->not->toBeNull();
    expect($serviceInvoice->total_amount)->toBe('144.00'); // 120€ + 20% TVA

    $commissionInvoice = Invoice::where('type', 'commission_invoice')->first();
    expect($commissionInvoice)->not->toBeNull();
    expect($commissionInvoice->total_amount)->toBe('21.60'); // 18€ + 20% TVA

    echo "✅ Système de paiements intégré avec succès !\n";
    echo "   💰 Commission plateforme: 18,00€ (15%)\n";
    echo "   👨‍💼 Gain prestataire: 102,00€\n";
    echo "   📄 Factures générées automatiquement\n";
});

test('database has all required tables', function () {
    // Vérifier que toutes les tables nécessaires existent
    $tables = [
        'users', 'categories', 'services', 'booking_requests',
        'wallets', 'transactions', 'payout_requests', 
        'disputes', 'invoices', 'messages', 'booking_status_history'
    ];

    foreach ($tables as $table) {
        expect(\Schema::hasTable($table))->toBe(true);
    }

    echo "✅ Toutes les tables de base de données sont présentes !\n";
});
