<?php

use App\Models\Category;
use App\Models\Service;
use App\Models\User;

test('user model has correct methods', function () {
    $user = new User;

    // Vérifier que les méthodes de type utilisateur existent
    expect(method_exists($user, 'isClient'))->toBeTrue();
    expect(method_exists($user, 'isProvider'))->toBeTrue();
    expect(method_exists($user, 'isAdmin'))->toBeTrue();
});

test('user type methods work correctly', function () {
    $clientUser = new User(['user_type' => 'client']);
    $providerUser = new User(['user_type' => 'provider']);
    $adminUser = new User(['user_type' => 'admin']);

    expect($clientUser->isClient())->toBeTrue();
    expect($providerUser->isProvider())->toBeTrue();
    expect($adminUser->isAdmin())->toBeTrue();
});

test('category model has correct structure', function () {
    $category = new Category;

    expect($category->getFillable())->toContain('name', 'slug', 'description', 'icon');
});

test('service model has correct structure', function () {
    $service = new Service;

    expect($service->getFillable())->toContain('title', 'description', 'price');
});

test('welcome page loads with new design', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    // Vérifier que la page contient les éléments de design
    $response->assertSee('Welcome');
});
