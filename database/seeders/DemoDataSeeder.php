<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Category;
use App\Models\Service;
use App\Models\ServicePackage;
use App\Models\ServiceZone;
use App\Models\BookingRequest;
use App\Models\Review;
use App\Models\Badge;
use App\Models\UserBadge;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    private array $frenchCities = [
        ['name' => 'Paris', 'postal_code' => '75001', 'latitude' => 48.8566, 'longitude' => 2.3522],
        ['name' => 'Lyon', 'postal_code' => '69000', 'latitude' => 45.7640, 'longitude' => 4.8357],
        ['name' => 'Marseille', 'postal_code' => '13000', 'latitude' => 43.2965, 'longitude' => 5.3698],
        ['name' => 'Toulouse', 'postal_code' => '31000', 'latitude' => 43.6047, 'longitude' => 1.4442],
        ['name' => 'Nice', 'postal_code' => '06000', 'latitude' => 43.7102, 'longitude' => 7.2620],
        ['name' => 'Nantes', 'postal_code' => '44000', 'latitude' => 47.2184, 'longitude' => -1.5536],
        ['name' => 'Strasbourg', 'postal_code' => '67000', 'latitude' => 48.5734, 'longitude' => 7.7521],
        ['name' => 'Montpellier', 'postal_code' => '34000', 'latitude' => 43.6110, 'longitude' => 3.8767],
        ['name' => 'Bordeaux', 'postal_code' => '33000', 'latitude' => 44.8378, 'longitude' => -0.5792],
        ['name' => 'Lille', 'postal_code' => '59000', 'latitude' => 50.6292, 'longitude' => 3.0573],
    ];

    private array $providers = [
        // Ménage et nettoyage
        [
            'name' => 'Marie Dubois',
            'email' => 'marie.dubois@example.com',
            'category' => 'Ménage et nettoyage',
            'bio' => 'Professionnelle du ménage depuis 8 ans, spécialisée dans l\'entretien de domiciles et bureaux. Méticuleuse et respectueuse de votre intimité.',
            'experience_years' => 8,
            'certifications' => ['Certification nettoyage professionnel', 'Formation produits écologiques'],
            'services' => [
                ['title' => 'Ménage complet domicile', 'price_min' => 25, 'price_max' => 35, 'duration' => 120],
                ['title' => 'Nettoyage de printemps', 'price_min' => 45, 'price_max' => 65, 'duration' => 240],
                ['title' => 'Entretien bureau', 'price_min' => 30, 'price_max' => 40, 'duration' => 90],
            ]
        ],
        [
            'name' => 'Sophie Martin',
            'email' => 'sophie.martin@example.com',
            'category' => 'Ménage et nettoyage',
            'bio' => 'Service de ménage éco-responsable. J\'utilise uniquement des produits naturels et respectueux de l\'environnement.',
            'experience_years' => 5,
            'certifications' => ['Label vert nettoyage', 'Formation éco-responsable'],
            'services' => [
                ['title' => 'Ménage écologique', 'price_min' => 28, 'price_max' => 38, 'duration' => 135],
                ['title' => 'Nettoyage après déménagement', 'price_min' => 80, 'price_max' => 120, 'duration' => 300],
            ]
        ],

        // Garde d'enfants
        [
            'name' => 'Julie Moreau',
            'email' => 'julie.moreau@example.com',
            'category' => 'Garde d\'enfants',
            'bio' => 'Éducatrice de jeunes enfants diplômée, passionnée par l\'accompagnement des enfants dans leur développement.',
            'experience_years' => 12,
            'certifications' => ['Diplôme EJE', 'PSC1', 'CAP Petite Enfance'],
            'services' => [
                ['title' => 'Garde à domicile', 'price_min' => 15, 'price_max' => 20, 'duration' => 240],
                ['title' => 'Baby-sitting occasionnel', 'price_min' => 12, 'price_max' => 16, 'duration' => 180],
                ['title' => 'Aide aux devoirs', 'price_min' => 18, 'price_max' => 25, 'duration' => 90],
            ]
        ],
        [
            'name' => 'Camille Rousseau',
            'email' => 'camille.rousseau@example.com',
            'category' => 'Garde d\'enfants',
            'bio' => 'Auxiliaire de puériculture avec 6 ans d\'expérience en crèche et garde à domicile. Spécialisée dans la garde de nourrissons.',
            'experience_years' => 6,
            'certifications' => ['Diplôme auxiliaire puériculture', 'PSC1'],
            'services' => [
                ['title' => 'Garde de nourrissons', 'price_min' => 18, 'price_max' => 24, 'duration' => 240],
                ['title' => 'Garde de nuit', 'price_min' => 80, 'price_max' => 120, 'duration' => 480],
            ]
        ],

        // Coiffure
        [
            'name' => 'Anaïs Leroy',
            'email' => 'anais.leroy@example.com',
            'category' => 'Coiffure',
            'bio' => 'Coiffeuse diplômée depuis 10 ans, spécialisée dans les coupes tendances et colorations. Je me déplace chez vous avec tout le matériel.',
            'experience_years' => 10,
            'certifications' => ['CAP Coiffure', 'BP Coiffure', 'Formation L\'Oréal'],
            'services' => [
                ['title' => 'Coupe femme', 'price_min' => 35, 'price_max' => 50, 'duration' => 60],
                ['title' => 'Coupe homme', 'price_min' => 25, 'price_max' => 35, 'duration' => 45],
                ['title' => 'Coloration', 'price_min' => 80, 'price_max' => 150, 'duration' => 150],
                ['title' => 'Balayage', 'price_min' => 120, 'price_max' => 200, 'duration' => 180],
            ]
        ],

        // Beauté et soins
        [
            'name' => 'Léa Girard',
            'email' => 'lea.girard@example.com',
            'category' => 'Beauté et soins',
            'bio' => 'Esthéticienne diplômée, spécialisée dans les soins du visage et l\'épilation. Institut mobile pour votre confort.',
            'experience_years' => 7,
            'certifications' => ['CAP Esthétique', 'Certification soins biologiques'],
            'services' => [
                ['title' => 'Soin visage hydratant', 'price_min' => 60, 'price_max' => 80, 'duration' => 75],
                ['title' => 'Épilation jambes complètes', 'price_min' => 40, 'price_max' => 55, 'duration' => 60],
                ['title' => 'Manucure', 'price_min' => 25, 'price_max' => 35, 'duration' => 45],
                ['title' => 'Pose de vernis semi-permanent', 'price_min' => 35, 'price_max' => 45, 'duration' => 60],
            ]
        ],

        // Massage et bien-être
        [
            'name' => 'Thomas Blanc',
            'email' => 'thomas.blanc@example.com',
            'category' => 'Massage et bien-être',
            'bio' => 'Masseur-kinésithérapeute diplômé, spécialisé dans les massages de détente et thérapeutiques. Table portable fournie.',
            'experience_years' => 15,
            'certifications' => ['Diplôme kinésithérapeute', 'Formation massage suédois', 'Certification shiatsu'],
            'services' => [
                ['title' => 'Massage relaxant', 'price_min' => 70, 'price_max' => 90, 'duration' => 60],
                ['title' => 'Massage thérapeutique', 'price_min' => 80, 'price_max' => 110, 'duration' => 75],
                ['title' => 'Massage sportif', 'price_min' => 85, 'price_max' => 120, 'duration' => 90],
            ]
        ],

        // Coaching sportif
        [
            'name' => 'Alexandre Durand',
            'email' => 'alexandre.durand@example.com',
            'category' => 'Coaching sportif',
            'bio' => 'Coach sportif diplômé, spécialisé dans le fitness, la musculation et la perte de poids. Programmes personnalisés.',
            'experience_years' => 8,
            'certifications' => ['BPJEPS', 'Certification CrossFit Level 1', 'Formation nutrition sportive'],
            'services' => [
                ['title' => 'Séance de coaching individuel', 'price_min' => 50, 'price_max' => 80, 'duration' => 60],
                ['title' => 'Programme perte de poids', 'price_min' => 200, 'price_max' => 300, 'duration' => 60],
                ['title' => 'Cours de Pilates', 'price_min' => 40, 'price_max' => 60, 'duration' => 55],
            ]
        ],

        // Jardinage
        [
            'name' => 'Pierre Fabre',
            'email' => 'pierre.fabre@example.com',
            'category' => 'Jardinage',
            'bio' => 'Jardinier paysagiste avec 20 ans d\'expérience. Entretien, création et conseils pour vos espaces verts.',
            'experience_years' => 20,
            'certifications' => ['CAP Paysagiste', 'Certificat phytosanitaire'],
            'services' => [
                ['title' => 'Entretien jardin', 'price_min' => 25, 'price_max' => 35, 'duration' => 120],
                ['title' => 'Taille des haies', 'price_min' => 30, 'price_max' => 45, 'duration' => 90],
                ['title' => 'Plantation et conseils', 'price_min' => 40, 'price_max' => 60, 'duration' => 150],
            ]
        ],

        // Bricolage
        [
            'name' => 'Nicolas Roux',
            'email' => 'nicolas.roux@example.com',
            'category' => 'Bricolage',
            'bio' => 'Artisan multi-services, spécialisé dans les petits travaux de bricolage, montage de meubles et dépannages.',
            'experience_years' => 12,
            'certifications' => ['CAP Menuiserie', 'Habilitation électrique'],
            'services' => [
                ['title' => 'Montage de meubles', 'price_min' => 30, 'price_max' => 50, 'duration' => 90],
                ['title' => 'Petits travaux électriques', 'price_min' => 45, 'price_max' => 70, 'duration' => 120],
                ['title' => 'Peinture intérieure', 'price_min' => 35, 'price_max' => 55, 'duration' => 240],
            ]
        ],
    ];

    private array $clients = [
        ['name' => 'Jean Dupont', 'email' => 'jean.dupont@example.com'],
        ['name' => 'Sarah Lemoine', 'email' => 'sarah.lemoine@example.com'],
        ['name' => 'Michel Bernard', 'email' => 'michel.bernard@example.com'],
        ['name' => 'Emma Petit', 'email' => 'emma.petit@example.com'],
        ['name' => 'Lucas Martin', 'email' => 'lucas.martin@example.com'],
        ['name' => 'Chloé Dubois', 'email' => 'chloe.dubois@example.com'],
        ['name' => 'Antoine Moreau', 'email' => 'antoine.moreau@example.com'],
        ['name' => 'Manon Leroy', 'email' => 'manon.leroy@example.com'],
        ['name' => 'Julien Garcia', 'email' => 'julien.garcia@example.com'],
        ['name' => 'Océane Rousseau', 'email' => 'oceane.rousseau@example.com'],
        ['name' => 'Maxime Girard', 'email' => 'maxime.girard@example.com'],
        ['name' => 'Laura Blanc', 'email' => 'laura.blanc@example.com'],
        ['name' => 'Kevin Durand', 'email' => 'kevin.durand@example.com'],
        ['name' => 'Amelia Fabre', 'email' => 'amelia.fabre@example.com'],
        ['name' => 'Hugo Roux', 'email' => 'hugo.roux@example.com'],
    ];

    public function run(): void
    {
        $this->command->info('🌱 Génération des données de démonstration...');

        // Créer les badges
        $this->createBadges();

        // Créer les clients
        $this->command->info('👥 Création des clients...');
        $clientUsers = $this->createClients();

        // Créer les prestataires
        $this->command->info('🔧 Création des prestataires...');
        $providerUsers = $this->createProviders();

        // Créer les réservations
        $this->command->info('📅 Création des réservations...');
        $this->createBookings($clientUsers, $providerUsers);

        // Créer les avis
        $this->command->info('⭐ Création des avis...');
        $this->createReviews($clientUsers, $providerUsers);

        $this->command->info('✅ Données de démonstration créées avec succès !');
        $this->command->line('');
        $this->command->info('📊 Résumé :');
        $this->command->line('• ' . count($this->clients) . ' clients créés');
        $this->command->line('• ' . count($this->providers) . ' prestataires créés');
        $this->command->line('• ~' . (count($this->providers) * 3) . ' services disponibles');
        $this->command->line('• Réservations et avis générés automatiquement');
    }

    private function createBadges(): void
    {
        $badges = [
            [
                'name' => 'Top Prestataire', 
                'slug' => 'top-prestataire',
                'description' => 'Prestataire avec plus de 50 avis positifs', 
                'icon' => 'star', 
                'color' => '#FFD700', 
                'type' => 'achievement',
                'rarity' => 'epic',
                'points' => 500,
                'criteria' => ['min_reviews' => 50, 'min_rating' => 4.5],
                'is_active' => true,
                'is_automatic' => false,
                'display_order' => 1,
            ],
            [
                'name' => 'Nouveau', 
                'slug' => 'nouveau',
                'description' => 'Nouveau sur la plateforme', 
                'icon' => 'sparkles', 
                'color' => '#00FF00', 
                'type' => 'milestone',
                'rarity' => 'common',
                'points' => 10,
                'criteria' => ['min_days_active' => 0],
                'is_active' => true,
                'is_automatic' => false,
                'display_order' => 5,
            ],
            [
                'name' => 'Certifié', 
                'slug' => 'certifie',
                'description' => 'Prestataire certifié par nos soins', 
                'icon' => 'shield-check', 
                'color' => '#007BFF', 
                'type' => 'certification',
                'rarity' => 'rare',
                'points' => 200,
                'criteria' => ['manual_award' => true],
                'is_active' => true,
                'is_automatic' => false,
                'display_order' => 2,
            ],
            [
                'name' => 'Écologique', 
                'slug' => 'ecologique',
                'description' => 'Utilise des produits éco-responsables', 
                'icon' => 'leaf', 
                'color' => '#28A745', 
                'type' => 'quality',
                'rarity' => 'rare',
                'points' => 150,
                'criteria' => ['manual_award' => true],
                'is_active' => true,
                'is_automatic' => false,
                'display_order' => 3,
            ],
            [
                'name' => 'Express', 
                'slug' => 'express',
                'description' => 'Intervention rapide sous 24h', 
                'icon' => 'zap', 
                'color' => '#FF6B00', 
                'type' => 'quality',
                'rarity' => 'common',
                'points' => 50,
                'criteria' => ['min_response_rate' => 90],
                'is_active' => true,
                'is_automatic' => false,
                'display_order' => 4,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::firstOrCreate(['slug' => $badge['slug']], $badge);
        }
    }

    private function createClients(): array
    {
        $clientUsers = [];
        
        foreach ($this->clients as $clientData) {
            $city = $this->frenchCities[array_rand($this->frenchCities)];
            
            $user = User::firstOrCreate(
                ['email' => $clientData['email']], 
                [
                    'name' => $clientData['name'],
                    'email' => $clientData['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'user_type' => 'client',
                    'phone' => '0' . rand(100000000, 999999999),
                    'address' => rand(1, 200) . ' rue ' . ['de la Paix', 'Victor Hugo', 'Jean Jaurès', 'des Fleurs', 'du Commerce'][array_rand(['de la Paix', 'Victor Hugo', 'Jean Jaurès', 'des Fleurs', 'du Commerce'])],
                    'is_active' => true,
                ]
            );
            
            $user->assignRole('client');
            
            // Créer le profil utilisateur
            UserProfile::create([
                'user_id' => $user->id,
                'bio' => 'Client satisfait de la plateforme StylServices.',
                'address' => $user->address . ', ' . $city['postal_code'] . ' ' . $city['name'],
                'latitude' => $city['latitude'] + (rand(-100, 100) / 1000),
                'longitude' => $city['longitude'] + (rand(-100, 100) / 1000),
            ]);
            
            $clientUsers[] = $user;
        }
        
        return $clientUsers;
    }

    private function createProviders(): array
    {
        $providerUsers = [];
        
        foreach ($this->providers as $providerData) {
            $city = $this->frenchCities[array_rand($this->frenchCities)];
            $category = Category::where('name', $providerData['category'])->first();
            
            $user = User::firstOrCreate(
                ['email' => $providerData['email']], 
                [
                    'name' => $providerData['name'],
                    'email' => $providerData['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'user_type' => 'provider',
                    'phone' => '0' . rand(100000000, 999999999),
                    'address' => rand(1, 200) . ' rue ' . ['de la République', 'Voltaire', 'Gambetta', 'des Roses', 'de l\'Église'][array_rand(['de la République', 'Voltaire', 'Gambetta', 'des Roses', 'de l\'Église'])],
                    'is_active' => true,
                ]
            );
            
            $user->assignRole('provider');
            
            // Créer le profil prestataire
            $profile = UserProfile::create([
                'user_id' => $user->id,
                'bio' => $providerData['bio'],
                'years_experience' => $providerData['experience_years'],
                'certifications' => json_encode($providerData['certifications']),
                'address' => $user->address . ', ' . $city['postal_code'] . ' ' . $city['name'],
                'latitude' => $city['latitude'] + (rand(-100, 100) / 1000),
                'longitude' => $city['longitude'] + (rand(-100, 100) / 1000),
                'hourly_rate_min' => rand(20, 50),
                'hourly_rate_max' => rand(60, 100),
                'rating' => rand(40, 50) / 10, // 4.0 à 5.0
            ]);
            
            // Attribuer des badges aléatoires
            $badges = Badge::inRandomOrder()->limit(rand(1, 3))->get();
            foreach ($badges as $badge) {
                UserBadge::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'badge_id' => $badge->id,
                    ],
                    [
                        'earned_at' => now()->subDays(rand(1, 365)),
                        'is_featured' => rand(0, 1),
                    ]
                );
            }
            
            // Créer les services
            foreach ($providerData['services'] as $serviceData) {
                $service = Service::firstOrCreate(
                    ['slug' => Str::slug($serviceData['title'])],
                    [
                        'provider_id' => $user->id,
                        'category_id' => $category->id,
                        'title' => $serviceData['title'],
                        'slug' => Str::slug($serviceData['title']),
                        'description' => 'Service professionnel de ' . strtolower($serviceData['title']) . '. Prestation de qualité avec matériel fourni.',
                        'short_description' => 'Service de ' . strtolower($serviceData['title']),
                        'price' => ($serviceData['price_min'] + $serviceData['price_max']) / 2,
                        'price_type' => 'fixed',
                        'duration' => $serviceData['duration'],
                        'location' => ['client', 'provider', 'both'][array_rand(['client', 'provider', 'both'])],
                        'is_active' => true,
                        'rating' => rand(40, 50) / 10,
                    ]
                );
                
                // Créer un forfait pour le service
                ServicePackage::create([
                    'service_id' => $service->id,
                    'name' => 'Forfait Standard',
                    'description' => 'Forfait standard pour ' . strtolower($serviceData['title']),
                    'price' => ($serviceData['price_min'] + $serviceData['price_max']) / 2,
                    'sessions_count' => 1,
                    'validity_days' => 30,
                    'is_active' => true,
                ]);
                
                // Créer les zones d'intervention
                $nearbyCity = $this->frenchCities[array_rand($this->frenchCities)];
                ServiceZone::create([
                    'service_id' => $service->id,
                    'name' => $city['name'] . ' et environs',
                    'postal_codes' => json_encode([$city['postal_code'], $nearbyCity['postal_code']]),
                    'latitude' => $city['latitude'],
                    'longitude' => $city['longitude'],
                    'radius_km' => rand(15, 30),
                    'is_active' => true,
                ]);
            }
            
            $providerUsers[] = $user;
        }
        
        return $providerUsers;
    }

    private function createBookings(array $clientUsers, array $providerUsers): void
    {
        $statuses = ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'];
        
        // Créer 30 réservations aléatoires
        for ($i = 0; $i < 30; $i++) {
            $client = $clientUsers[array_rand($clientUsers)];
            $provider = $providerUsers[array_rand($providerUsers)];
            $service = $provider->providedServices()->inRandomOrder()->first();
            
            if (!$service) continue;
            
            $status = $statuses[array_rand($statuses)];
            $scheduledDate = now()->addDays(rand(-30, 30));
            
            BookingRequest::create([
                'uuid' => \Str::uuid(),
                'client_id' => $client->id,
                'provider_id' => $provider->id,
                'service_id' => $service->id,
                'status' => $status,
                'preferred_datetime' => $scheduledDate,
                'estimated_duration' => $service->duration ?? 120,
                'quoted_price' => $service->price,
                'client_address' => json_encode([
                    'address' => $client->profile->address,
                    'city' => 'Paris',
                    'postal_code' => '75001'
                ]),
                'client_notes' => 'Demande de réservation pour ' . $service->title . '. Aucune note particulière.',
            ]);
        }
    }

    private function createReviews(array $clientUsers, array $providerUsers): void
    {
        // Récupérer une réservation pour les avis
        $firstBooking = \App\Models\BookingRequest::first();
        
        if (!$firstBooking) return; // Pas de réservations, pas d'avis
        
        // Créer des avis pour les prestataires
        foreach ($providerUsers as $provider) {
            $numReviews = rand(3, 15);
            
            for ($i = 0; $i < $numReviews; $i++) {
                $client = $clientUsers[array_rand($clientUsers)];
                $rating = rand(3, 5); // Notes entre 3 et 5 pour avoir de bons prestataires
                
                $reviewTexts = [
                    5 => [
                        'Excellent service, je recommande vivement !',
                        'Très professionnel, travail impeccable.',
                        'Parfait ! Je referai appel à ses services.',
                        'Service de qualité, ponctuel et efficace.',
                        'Vraiment satisfait de la prestation.',
                    ],
                    4 => [
                        'Bon service, quelques petits détails à améliorer.',
                        'Satisfait dans l\'ensemble, bon rapport qualité-prix.',
                        'Professionnel et à l\'écoute.',
                        'Bonne prestation, je recommande.',
                        'Travail correct, prestataire sympathique.',
                    ],
                    3 => [
                        'Service correct sans plus.',
                        'Ça va, sans être exceptionnel.',
                        'Prestation dans la moyenne.',
                        'Correct mais peut mieux faire.',
                        'Passable, quelques points à améliorer.',
                    ]
                ];
                
                Review::create([
                    'uuid' => \Str::uuid(),
                    'booking_request_id' => $firstBooking->id,
                    'reviewer_id' => $client->id,
                    'reviewed_id' => $provider->id,
                    'reviewer_type' => 'client',
                    'overall_rating' => $rating,
                    'quality_rating' => $rating,
                    'communication_rating' => $rating,
                    'punctuality_rating' => rand(max(1, $rating - 1), min(5, $rating + 1)),
                    'professionalism_rating' => $rating,
                    'title' => 'Avis sur ' . $provider->name,
                    'comment' => $reviewTexts[$rating][array_rand($reviewTexts[$rating])],
                    'status' => 'approved',
                    'is_verified' => true,
                    'created_at' => now()->subDays(rand(1, 180)),
                ]);
            }
        }
    }
}