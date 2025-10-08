<?php

use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Services\CommissionService;
use App\Services\InvoiceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Créer les rôles
    Role::create(['name' => 'client', 'guard_name' => 'web']);
    Role::create(['name' => 'provider', 'guard_name' => 'web']);
    
    // Créer une catégorie
    $this->category = Category::create([
        'name' => 'Test Category',
        'description' => 'Category for testing',
        'icon' => 'test-icon',
        'status' => 'active',
    ]);
    
    // Créer les utilisateurs
    $this->client = User::factory()->create();
    $this->client->assignRole('client');
    
    $this->provider = User::factory()->create();
    $this->provider->assignRole('provider');
    
    // Créer un service
    $this->service = Service::create([
        'title' => 'Test Service',
        'description' => 'Service for testing',
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'price' => 150.00,
        'price_min' => 100.00,
        'price_max' => 200.00,
        'duration' => 120,
        'status' => 'active',
    ]);
});

test('complete payment workflow with commissions and invoices', function () {
    // 1. Créer une réservation terminée
    $booking = BookingRequest::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
        'status' => 'completed',
        'quoted_price' => 150.00,
        'final_price' => 150.00,
        'completed_at' => now(),
        'preferred_datetime' => now()->addDay(),
        'client_address' => ['street' => 'Test Street', 'city' => 'Test City'],
    ]);

    // 2. Traiter le paiement avec commissions
    $commissionService = app(CommissionService::class);
    $paymentResult = $commissionService->processPayment($booking);

    // 3. Vérifier les montants calculés
    expect($paymentResult['total_amount'])->toBe('150.00');
    expect($paymentResult['commission'])->toBe(22.50); // 15% de 150€
    expect($paymentResult['provider_amount'])->toBe(127.50); // 150€ - 22.50€
    expect($paymentResult['commission_rate'])->toBe(0.15);

    // 4. Vérifier la création du portefeuille et des fonds
    $wallet = Wallet::where('user_id', $this->provider->id)->first();
    expect($wallet)->not->toBeNull();
    expect($wallet->pending_balance)->toBe('127.50');
    expect($wallet->balance)->toBe('0.00');

    // 5. Vérifier la création des transactions
    $providerTransaction = Transaction::where('user_id', $this->provider->id)->first();
    expect($providerTransaction)->not->toBeNull();
    expect($providerTransaction->amount)->toBe('127.50');
    expect($providerTransaction->type)->toBe('service_payment');
    expect($providerTransaction->status)->toBe('pending');

    $commissionTransaction = Transaction::where('type', 'platform_commission')->first();
    expect($commissionTransaction)->not->toBeNull();
    expect($commissionTransaction->amount)->toBe('22.50');
    expect($commissionTransaction->status)->toBe('completed');

    // 6. Vérifier la création des factures automatiques
    expect($paymentResult)->toHaveKey('service_invoice');
    expect($paymentResult)->toHaveKey('commission_invoice');

    $serviceInvoice = $paymentResult['service_invoice'];
    expect($serviceInvoice->type)->toBe('service_invoice');
    expect($serviceInvoice->client_id)->toBe($this->client->id);
    expect($serviceInvoice->provider_id)->toBe($this->provider->id);
    expect($serviceInvoice->subtotal)->toBe('150.00');
    expect($serviceInvoice->total_amount)->toBe('180.00'); // 150€ + 20% TVA

    $commissionInvoice = $paymentResult['commission_invoice'];
    expect($commissionInvoice->type)->toBe('commission_invoice');
    expect($commissionInvoice->client_id)->toBe($this->provider->id);
    expect($commissionInvoice->subtotal)->toBe('22.50');
    expect($commissionInvoice->total_amount)->toBe('27.00'); // 22.50€ + 20% TVA

    // 7. Confirmer le paiement
    $commissionService->confirmPayment($booking);

    $wallet->refresh();
    expect($wallet->pending_balance)->toBe('0.00');
    expect($wallet->balance)->toBe('127.50');

    $providerTransaction->refresh();
    expect($providerTransaction->status)->toBe('completed');
});

test('wallet operations work correctly', function () {
    $wallet = Wallet::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'user_id' => $this->provider->id,
        'balance' => 100.00,
        'pending_balance' => 50.00,
        'frozen_balance' => 20.00,
        'currency' => 'EUR',
        'is_active' => true,
    ]);

    // Test disponible balance
    expect($wallet->getAvailableBalanceAttribute())->toBe(80.00); // 100 - 20

    // Test total balance
    expect($wallet->getTotalBalanceAttribute())->toBe(150.00); // 100 + 50

    // Test canWithdraw
    expect($wallet->canWithdraw(50.00))->toBeTrue();
    expect($wallet->canWithdraw(100.00))->toBeFalse(); // Plus que le solde disponible

    // Test ajout de fonds
    $wallet->addFunds(75.00, 'service_payment', 'Test payment');
    $wallet->refresh();
    expect($wallet->pending_balance)->toBe('125.00'); // 50 + 75

    // Test confirmation de fonds
    $wallet->confirmFunds(75.00);
    $wallet->refresh();
    expect($wallet->pending_balance)->toBe('50.00'); // 125 - 75
    expect($wallet->balance)->toBe('175.00'); // 100 + 75

    // Test gel de fonds
    $result = $wallet->freezeFunds(50.00, 'Test freeze');
    expect($result)->toBeTrue();
    $wallet->refresh();
    expect($wallet->balance)->toBe('125.00'); // 175 - 50
    expect($wallet->frozen_balance)->toBe('70.00'); // 20 + 50

    // Test dégel de fonds
    $result = $wallet->unfreezeFunds(30.00);
    expect($result)->toBeTrue();
    $wallet->refresh();
    expect($wallet->balance)->toBe('155.00'); // 125 + 30
    expect($wallet->frozen_balance)->toBe('40.00'); // 70 - 30
});

test('dispute creation and resolution works', function () {
    $booking = BookingRequest::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
        'status' => 'completed',
        'final_price' => 150.00,
        'preferred_datetime' => now()->addDay(),
        'client_address' => ['street' => 'Test Street', 'city' => 'Test City'],
    ]);

    // Créer un litige
    $dispute = $booking->createDispute(
        $this->client,
        'refund_request',
        'Service non satisfaisant',
        ['photos' => ['evidence1.jpg']],
        150.00
    );

    expect($dispute->booking_request_id)->toBe($booking->id);
    expect($dispute->reported_by)->toBe($this->client->id);
    expect($dispute->reported_against)->toBe($this->provider->id);
    expect($dispute->type)->toBe('refund_request');
    expect($dispute->disputed_amount)->toBe('150.00');
    expect($dispute->status)->toBe('open');

    // Résoudre le litige avec remboursement partiel
    $dispute->resolve(75.00, 'Remboursement partiel accordé');

    $dispute->refresh();
    expect($dispute->status)->toBe('resolved');
    expect($dispute->refund_amount)->toBe('75.00');
    expect($dispute->resolution_notes)->toBe('Remboursement partiel accordé');
    expect($dispute->resolved_at)->not->toBeNull();
});

test('invoice generation works correctly', function () {
    $booking = BookingRequest::create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'client_id' => $this->client->id,
        'provider_id' => $this->provider->id,
        'service_id' => $this->service->id,
        'quoted_price' => 100.00,
        'final_price' => 120.00,
        'preferred_datetime' => now()->addDay(),
        'client_address' => ['street' => 'Test Street', 'city' => 'Test City'],
    ]);

    $invoiceService = app(InvoiceService::class);

    // Générer facture de service
    $serviceInvoice = $invoiceService->generateServiceInvoice($booking);

    expect($serviceInvoice->type)->toBe('service_invoice');
    expect($serviceInvoice->subtotal)->toBe('120.00');
    expect($serviceInvoice->tax_rate)->toBe('20.00');
    expect($serviceInvoice->tax_amount)->toBe('24.00'); // 20% de 120€
    expect($serviceInvoice->total_amount)->toBe('144.00'); // 120€ + 24€
    expect($serviceInvoice->status)->toBe('draft');
    expect($serviceInvoice->invoice_number)->toStartWith('INV-');

    // Générer facture de commission
    $commissionInvoice = $invoiceService->generateCommissionInvoice($booking, 18.00);

    expect($commissionInvoice->type)->toBe('commission_invoice');
    expect($commissionInvoice->subtotal)->toBe('18.00');
    expect($commissionInvoice->tax_amount)->toBe('3.60'); // 20% de 18€
    expect($commissionInvoice->total_amount)->toBe('21.60'); // 18€ + 3.60€
    expect($commissionInvoice->client_id)->toBe($this->provider->id); // Prestataire facturé
    expect($commissionInvoice->invoice_number)->toStartWith('COM-');

    // Test marquer comme payée
    $invoiceService->markAsPaid($serviceInvoice);
    $serviceInvoice->refresh();
    expect($serviceInvoice->status)->toBe('paid');
    expect($serviceInvoice->paid_at)->not->toBeNull();
});

test('payout request workflow', function () {
    // Supprimer tout portefeuille existant pour s'assurer d'un état propre
    Wallet::where('user_id', $this->provider->id)->delete();
    
    // Créer un portefeuille avec des fonds
    $wallet = Wallet::create([
        'user_id' => $this->provider->id,
        'balance' => 200.00,
        'pending_balance' => 0.00,
        'frozen_balance' => 0.00,
        'currency' => 'EUR',
        'is_active' => true,
    ]);

    // Demander un retrait
    $payoutRequest = $wallet->withdraw(150.00, [
        'method' => 'bank_transfer',
        'bank_details' => [
            'iban' => 'FR1420041010050500013M02606',
            'bic' => 'PSSTFRPPPAR',
        ]
    ]);

    expect($payoutRequest)->not->toBeNull();
    expect($payoutRequest->amount)->toBe('150.00');
    expect($payoutRequest->status)->toBe('pending');
    expect($payoutRequest->payout_method)->toBe('bank_transfer');

    // Vérifier que les fonds sont gelés
    $wallet->refresh();
    expect($wallet->balance)->toBe('50.00'); // 200 - 150
    expect($wallet->frozen_balance)->toBe('150.00');

    // Traiter le paiement
    $payoutRequest->markAsProcessed('stripe_po_1234567890');

    $payoutRequest->refresh();
    expect($payoutRequest->status)->toBe('completed');
    expect($payoutRequest->stripe_payout_id)->toBe('stripe_po_1234567890');
    expect($payoutRequest->processed_at)->not->toBeNull();

    // En cas d'échec
    $payoutRequest2 = $wallet->withdraw(40.00);
    $payoutRequest2->markAsFailed('Insufficient funds');

    $payoutRequest2->refresh();
    expect($payoutRequest2->status)->toBe('failed');
    expect($payoutRequest2->failure_reason)->toBe('Insufficient funds');

    // Les fonds sont remis dans le solde disponible
    $wallet->refresh();
    expect($wallet->balance)->toBe('90.00'); // 50 + 40 remis
    expect($wallet->frozen_balance)->toBe('150.00'); // Inchangé
});
