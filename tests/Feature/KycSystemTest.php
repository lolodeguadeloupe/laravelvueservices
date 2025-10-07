<?php

use App\Models\User;
use App\Services\KycService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('private');
});

test('can submit kyc documents', function () {
    $provider = User::factory()->create([
        'user_type' => 'provider',
        'email' => 'provider@test.com',
    ]);

    $provider->profile()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    $documents = [
        'identity_document_path' => UploadedFile::fake()->create('identity.pdf', 1000),
        'business_registration_path' => UploadedFile::fake()->create('kbis.pdf', 1000),
        'insurance_certificate_path' => UploadedFile::fake()->create('insurance.pdf', 1000),
    ];

    $kycService = new KycService;
    $result = $kycService->submitKycDocuments($provider, $documents);

    expect($result)->toBeTrue();
    expect($kycService->getKycStatus($provider))->toBe('under_review');

    // Vérifier que les documents sont stockés
    $provider->refresh();
    expect($provider->profile->identity_document_path)->not->toBeNull();
    expect($provider->profile->business_registration_path)->not->toBeNull();
    expect($provider->profile->insurance_certificate_path)->not->toBeNull();
});

test('admin can approve kyc', function () {
    $provider = User::factory()->create([
        'user_type' => 'provider',
        'is_verified' => false,
    ]);

    $provider->profile()->create([
        'kyc_status' => 'under_review',
        'kyc_submitted_at' => now(),
    ]);

    $admin = User::factory()->create([
        'user_type' => 'admin',
    ]);

    $kycService = new KycService;
    $result = $kycService->approveKyc($provider, $admin);

    expect($result)->toBeTrue();

    $provider->refresh();
    expect($provider->profile->kyc_status)->toBe('approved');
    expect($provider->is_verified)->toBeTrue();
    expect($provider->profile->kyc_reviewed_by)->toBe($admin->id);
    expect($provider->profile->kyc_reviewed_at)->not->toBeNull();
});

test('admin can reject kyc', function () {
    $provider = User::factory()->create([
        'user_type' => 'provider',
    ]);

    $provider->profile()->create([
        'kyc_status' => 'under_review',
        'kyc_submitted_at' => now(),
    ]);

    $admin = User::factory()->create([
        'user_type' => 'admin',
    ]);

    $reason = 'Documents illisibles';
    $kycService = new KycService;
    $result = $kycService->rejectKyc($provider, $admin, $reason);

    expect($result)->toBeTrue();

    $provider->refresh();
    expect($provider->profile->kyc_status)->toBe('rejected');
    expect($provider->profile->kyc_rejection_reason)->toBe($reason);
    expect($provider->profile->kyc_reviewed_by)->toBe($admin->id);
    expect($provider->profile->kyc_reviewed_at)->not->toBeNull();
});

test('can check provider service eligibility', function () {
    $kycService = new KycService;

    // Provider non vérifié
    $provider1 = User::factory()->create([
        'user_type' => 'provider',
        'is_verified' => false,
    ]);

    $provider1->profile()->create([
        'kyc_status' => 'pending',
    ]);

    expect($kycService->canProvideServices($provider1))->toBeFalse();

    // Provider vérifié et KYC approuvé
    $provider2 = User::factory()->create([
        'user_type' => 'provider',
        'is_verified' => true,
    ]);

    $provider2->profile()->create([
        'kyc_status' => 'approved',
    ]);

    expect($kycService->canProvideServices($provider2))->toBeTrue();
});

test('can validate business data', function () {
    $kycService = new KycService;

    $validationErrors = $kycService->validateBusinessData([
        'siret_number' => '12345678901234', // SIRET invalide
        'iban' => 'FR1420041010050500013M02606', // IBAN valide
    ]);

    expect($validationErrors)->toHaveKey('siret_number');
    expect($validationErrors)->not->toHaveKey('iban');
});

test('admin kyc routes require authentication', function () {
    $provider = User::factory()->create(['user_type' => 'provider']);

    // Test sans authentification
    $response = $this->get('/admin/kyc');
    $response->assertRedirect('/login');

    // Test avec utilisateur non-admin
    $user = User::factory()->create(['user_type' => 'client']);
    $response = $this->actingAs($user)->get('/admin/kyc');
    $response->assertStatus(403);
});

test('admin can access kyc management', function () {
    $admin = User::factory()->create(['user_type' => 'admin']);

    $response = $this->actingAs($admin)->get('/admin/kyc');
    $response->assertStatus(200);
});
