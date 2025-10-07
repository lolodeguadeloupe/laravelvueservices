<?php

use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    // Créer des utilisateurs de test
    $this->provider = User::factory()->create(['user_type' => 'provider']);
    $this->otherProvider = User::factory()->create(['user_type' => 'provider']);
    $this->client = User::factory()->create(['user_type' => 'client']);

    // Créer une catégorie de test
    $this->category = Category::factory()->create([
        'name' => 'Ménage',
        'is_active' => true,
    ]);
});

it('peut afficher la liste des services du prestataire', function () {
    // Créer des services pour le prestataire connecté
    $myServices = Service::factory()->count(3)->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
    ]);

    // Créer des services pour un autre prestataire
    Service::factory()->count(2)->create([
        'provider_id' => $this->otherProvider->id,
        'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->provider)
        ->get(route('provider.services.index'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->component('Provider/Services/Index')
        ->has('services.data', 3) // Seulement les services du prestataire connecté
    );
});

it('peut créer un nouveau service', function () {
    $serviceData = [
        'category_id' => $this->category->id,
        'title' => 'Service de ménage à domicile',
        'description' => 'Un service de ménage complet pour votre domicile avec tous les produits inclus.',
        'short_description' => 'Ménage complet à domicile',
        'price' => 25.50,
        'price_type' => 'hourly',
        'duration' => 120,
        'location' => [
            'address' => '123 Rue de la Paix',
            'city' => 'Paris',
            'postal_code' => '75001',
            'radius_km' => 20,
        ],
        'requirements' => ['Fournir les clés', 'Produits d\'entretien inclus'],
        'is_active' => true,
    ];

    $response = $this->actingAs($this->provider)
        ->post(route('provider.services.store'), $serviceData);

    $response->assertStatus(201);
    $response->assertJsonStructure([
        'message',
        'service' => [
            'id',
            'title',
            'category',
        ],
    ]);

    $this->assertDatabaseHas('services', [
        'provider_id' => $this->provider->id,
        'title' => 'Service de ménage à domicile',
        'price' => 25.50,
        'price_type' => 'hourly',
    ]);
});

it('peut créer un service avec des images', function () {
    $images = [
        UploadedFile::fake()->image('service1.jpg', 800, 600),
        UploadedFile::fake()->image('service2.png', 1200, 800),
    ];

    $serviceData = [
        'category_id' => $this->category->id,
        'title' => 'Service avec images',
        'description' => 'Description complète du service avec galerie d\'images.',
        'short_description' => 'Service avec photos',
        'price' => 50.00,
        'price_type' => 'fixed',
        'images' => $images,
        'is_active' => true,
    ];

    $response = $this->actingAs($this->provider)
        ->post(route('provider.services.store'), $serviceData);

    $response->assertStatus(201);

    $service = Service::latest()->first();
    expect($service->images)->toHaveCount(2);

    // Vérifier que les images sont stockées
    foreach ($service->images as $imagePath) {
        Storage::disk('public')->assertExists($imagePath);
    }
});

it('valide les données lors de la création', function () {
    $response = $this->actingAs($this->provider)
        ->post(route('provider.services.store'), []);

    $response->assertSessionHasErrors([
        'category_id',
        'title',
        'description',
        'short_description',
        'price',
        'price_type',
    ]);
});

it('peut mettre à jour un service', function () {
    $service = Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'title' => 'Ancien titre',
        'price' => 20.00,
    ]);

    $updateData = [
        'title' => 'Nouveau titre',
        'price' => 30.00,
        'description' => 'Nouvelle description mise à jour avec plus de détails.',
    ];

    $response = $this->actingAs($this->provider)
        ->patch(route('provider.services.update', $service), $updateData);

    $response->assertStatus(200);

    $service->refresh();
    expect($service->title)->toBe('Nouveau titre');
    expect($service->price)->toBe(30.00);
});

it('empêche la modification du service d\'un autre prestataire', function () {
    $service = Service::factory()->create([
        'provider_id' => $this->otherProvider->id,
        'category_id' => $this->category->id,
    ]);

    $response = $this->actingAs($this->provider)
        ->patch(route('provider.services.update', $service), [
            'title' => 'Tentative de modification',
        ]);

    $response->assertStatus(403);
});

it('peut basculer le statut d\'un service', function () {
    $service = Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'is_active' => true,
    ]);

    $response = $this->actingAs($this->provider)
        ->patch(route('provider.services.toggle-status', $service));

    $response->assertStatus(200);

    $service->refresh();
    expect($service->is_active)->toBeFalse();

    // Basculer à nouveau
    $response = $this->actingAs($this->provider)
        ->patch(route('provider.services.toggle-status', $service));

    $response->assertStatus(200);

    $service->refresh();
    expect($service->is_active)->toBeTrue();
});

it('peut supprimer un service sans réservations actives', function () {
    $service = Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'images' => ['service/test1.jpg', 'service/test2.jpg'],
    ]);

    // Créer les fichiers factices
    Storage::disk('public')->put('service/test1.jpg', 'fake image content');
    Storage::disk('public')->put('service/test2.jpg', 'fake image content');

    $response = $this->actingAs($this->provider)
        ->delete(route('provider.services.destroy', $service));

    $response->assertStatus(200);

    $this->assertDatabaseMissing('services', ['id' => $service->id]);

    // Vérifier que les images sont supprimées
    Storage::disk('public')->assertMissing('service/test1.jpg');
    Storage::disk('public')->assertMissing('service/test2.jpg');
});

it('peut ajouter une image à un service existant', function () {
    $service = Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'images' => [],
    ]);

    $image = UploadedFile::fake()->image('nouvelle-image.jpg');

    $response = $this->actingAs($this->provider)
        ->post(route('provider.services.media.upload', $service), [
            'media' => $image,
        ]);

    $response->assertStatus(200);

    $service->refresh();
    expect($service->images)->toHaveCount(1);

    Storage::disk('public')->assertExists($service->images[0]);
});

it('peut supprimer une image d\'un service', function () {
    $imagePath = 'services/images/test-image.jpg';
    Storage::disk('public')->put($imagePath, 'fake content');

    $service = Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'images' => [$imagePath],
    ]);

    $response = $this->actingAs($this->provider)
        ->delete(route('provider.services.media.delete', [$service, $imagePath]));

    $response->assertStatus(200);

    $service->refresh();
    expect($service->images)->toBeEmpty();

    Storage::disk('public')->assertMissing($imagePath);
});

it('peut définir une image principale', function () {
    $image1 = 'services/images/image1.jpg';
    $image2 = 'services/images/image2.jpg';

    $service = Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'images' => [$image1, $image2],
    ]);

    $response = $this->actingAs($this->provider)
        ->patch(route('provider.services.media.primary', [$service, $image2]));

    $response->assertStatus(200);

    $service->refresh();
    expect($service->images[0])->toBe($image2);
    expect($service->images[1])->toBe($image1);
});

it('filtre les services par catégorie', function () {
    $category2 = Category::factory()->create(['name' => 'Jardinage']);

    // Services dans la première catégorie
    Service::factory()->count(2)->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
    ]);

    // Service dans la deuxième catégorie
    Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $category2->id,
    ]);

    $response = $this->actingAs($this->provider)
        ->get(route('provider.services.index', ['category' => $this->category->id]));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->has('services.data', 2)
    );
});

it('filtre les services par statut', function () {
    // Services actifs
    Service::factory()->count(2)->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'is_active' => true,
    ]);

    // Service inactif
    Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'is_active' => false,
    ]);

    $response = $this->actingAs($this->provider)
        ->get(route('provider.services.index', ['status' => 'active']));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->has('services.data', 2)
    );
});

it('recherche dans les services par titre et description', function () {
    Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'title' => 'Service de ménage spécialisé',
        'description' => 'Nettoyage professionnel',
    ]);

    Service::factory()->create([
        'provider_id' => $this->provider->id,
        'category_id' => $this->category->id,
        'title' => 'Cours de cuisine',
        'description' => 'Apprentissage culinaire',
    ]);

    $response = $this->actingAs($this->provider)
        ->get(route('provider.services.index', ['search' => 'ménage']));

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page->has('services.data', 1)
    );
});

it('empêche l\'accès aux clients', function () {
    $response = $this->actingAs($this->client)
        ->get(route('provider.services.index'));

    $response->assertStatus(403);
});
