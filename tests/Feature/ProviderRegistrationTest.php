<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('can access provider registration step 1', function () {
    $response = $this->get(route('provider.registration.step1'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Provider/Registration/Step1')
        );
});

it('can process provider registration step 1', function () {
    $data = [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'email' => 'jean.dupont@example.com',
        'phone' => '0123456789',
        'date_of_birth' => '1990-01-01',
        'address' => '123 Rue de la Paix, 75001 Paris, France',
    ];

    $response = $this->post(route('provider.registration.step1.process'), $data);

    $response->assertRedirect(route('provider.registration.step2'))
        ->assertSessionHas('provider_registration.step1', $data);
});

it('validates required fields in step 1', function () {
    $response = $this->post(route('provider.registration.step1.process'), []);

    $response->assertSessionHasErrors([
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'address',
    ]);
});

it('can access step 2 only after completing step 1', function () {
    // Sans session step1
    $response = $this->get(route('provider.registration.step2'));
    $response->assertRedirect(route('provider.registration.step1'));

    // Avec session step1
    $this->withSession([
        'provider_registration.step1' => [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'phone' => '0123456789',
            'date_of_birth' => '1990-01-01',
            'address' => '123 Rue de la Paix, 75001 Paris, France',
        ],
    ]);

    $response = $this->get(route('provider.registration.step2'));
    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Provider/Registration/Step2')
        );
});

it('can complete the full registration process', function () {
    Storage::fake('private');

    // Créer les rôles nécessaires
    \Spatie\Permission\Models\Role::create(['name' => 'provider']);

    // Étape 1
    $step1Data = [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'email' => 'jean.dupont@example.com',
        'phone' => '0123456789',
        'date_of_birth' => '1990-01-01',
        'address' => '123 Rue de la Paix, 75001 Paris, France',
    ];

    $this->post(route('provider.registration.step1.process'), $step1Data);

    // Étape 2
    $step2Data = [
        'company_name' => 'Mon Entreprise',
        'bio' => 'Je suis un prestataire expérimenté',
        'experience' => 'Plus de 5 ans d\'expérience dans le domaine',
        'certifications' => ['Certification A', 'Certification B'],
    ];

    $this->post(route('provider.registration.step2.process'), $step2Data);

    // Étape 3
    $step3Data = [
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'identity_document' => UploadedFile::fake()->create('identity.pdf', 1000, 'application/pdf'),
        'terms_accepted' => '1',
    ];

    $response = $this->post(route('provider.registration.step3.process'), $step3Data);

    if ($response->status() !== 302) {
        dump($response->getContent());
    }

    $response->assertRedirect(route('provider.registration.success'));

    // Vérifier que l'utilisateur a été créé
    $user = User::where('email', 'jean.dupont@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->user_type)->toBe('provider');
    expect($user->verification_status)->toBe('pending');
    expect($user->is_active)->toBeFalse();

    // Vérifier que le profil a été créé
    expect($user->profile)->not->toBeNull();
    expect($user->profile->first_name)->toBe('Jean');
    expect($user->profile->last_name)->toBe('Dupont');

    // Vérifier que le rôle a été assigné
    expect($user->hasRole('provider'))->toBeTrue();

    // Vérifier que les documents ont été uploadés
    expect($user->profile->documents)->not->toBeNull();
    expect($user->profile->documents['identity_document'])->not->toBeNull();
});

it('requires identity document for step 3', function () {
    $this->withSession([
        'provider_registration.step1' => [
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean.dupont@example.com',
            'phone' => '0123456789',
            'date_of_birth' => '1990-01-01',
            'address' => '123 Rue de la Paix, 75001 Paris, France',
        ],
        'provider_registration.step2' => [
            'bio' => 'Je suis un prestataire expérimenté',
            'experience' => 'Plus de 5 ans d\'expérience dans le domaine',
        ],
    ]);

    $response = $this->post(route('provider.registration.step3.process'), [
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'terms_accepted' => '1',
    ]);

    $response->assertSessionHasErrors(['identity_document']);
});
