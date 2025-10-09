<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProviderDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('user_type:provider');
    }

    public function index()
    {
        $provider = Auth::user();

        // Statistiques générales
        $totalServices = Service::where('provider_id', $provider->id)->count();
        $activeServices = Service::where('provider_id', $provider->id)->where('is_active', true)->count();

        // Données simulées pour le dashboard (en attendant le système de réservations)
        $stats = [
            'total_services' => $totalServices,
            'active_services' => $activeServices,
            'pending_requests' => 3,
            'completed_bookings' => 15,
            'total_earnings' => 2840.50,
            'current_month_earnings' => 890.00,
            'earnings_growth' => 12.5,
            'average_rating' => 4.7,
            'reviews_count' => 12,
            'completion_rate' => 94.2,
        ];

        // Graphique des gains des 6 derniers mois
        $monthlyEarnings = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $earnings = rand(400, 1200); // Données simulées

            $monthlyEarnings[] = [
                'month' => $month->format('M Y'),
                'earnings' => $earnings,
            ];
        }

        // Réservations par statut pour le graphique en donut
        $bookingsByStatus = [
            ['status' => 'En attente', 'count' => 3, 'color' => '#f59e0b'],
            ['status' => 'Acceptée', 'count' => 2, 'color' => '#3b82f6'],
            ['status' => 'Terminée', 'count' => 15, 'color' => '#10b981'],
            ['status' => 'Annulée', 'count' => 1, 'color' => '#ef4444'],
        ];

        // Services avec statistiques
        $services = Service::where('provider_id', $provider->id)
            ->with('category')
            ->latest()
            ->limit(3)
            ->get()
            ->map(function ($service) {
                // Simulation de données de réservation
                $service->bookings_count = rand(2, 15);
                $service->revenue = rand(200, 800);
                $service->rating = round(rand(35, 50) / 10, 1);

                return $service;
            });

        // Activité récente
        $recentActivity = [
            [
                'type' => 'new_booking',
                'title' => 'Nouvelle réservation',
                'description' => 'Service de ménage pour le 15 octobre',
                'date' => now()->subHours(2),
                'icon' => 'calendar',
                'color' => 'blue',
            ],
            [
                'type' => 'new_review',
                'title' => 'Nouvel avis reçu',
                'description' => '5/5 étoiles - "Excellent travail !"',
                'date' => now()->subHours(8),
                'icon' => 'star',
                'color' => 'yellow',
            ],
            [
                'type' => 'service_updated',
                'title' => 'Service modifié',
                'description' => 'Tarifs mis à jour pour le jardinage',
                'date' => now()->subDay(),
                'icon' => 'edit',
                'color' => 'green',
            ],
            [
                'type' => 'payment_received',
                'title' => 'Paiement reçu',
                'description' => '150€ pour service de bricolage',
                'date' => now()->subDays(2),
                'icon' => 'credit-card',
                'color' => 'green',
            ],
        ];

        // Prochains rendez-vous (simulés)
        $upcomingBookings = [
            [
                'id' => 1,
                'client_name' => 'Marie Dubois',
                'service_title' => 'Ménage appartement',
                'scheduled_date' => now()->addDays(2),
                'amount' => 80,
                'status' => 'accepted',
            ],
            [
                'id' => 2,
                'client_name' => 'Pierre Martin',
                'service_title' => 'Jardinage',
                'scheduled_date' => now()->addDays(5),
                'amount' => 120,
                'status' => 'pending',
            ],
        ];

        // Données adaptées pour le nouveau dashboard
        $dashboardStats = [
            'totalRevenue' => $stats['total_earnings'],
            'thisMonthRevenue' => $stats['current_month_earnings'],
            'totalBookings' => $stats['completed_bookings'] + $stats['pending_requests'],
            'pendingBookings' => $stats['pending_requests'],
            'completedBookings' => $stats['completed_bookings'],
            'averageRating' => $stats['average_rating'],
            'totalReviews' => $stats['reviews_count'],
            'activeServices' => $stats['active_services']
        ];

        // Réservations récentes simulées
        $recentBookings = [
            [
                'id' => 1,
                'status' => 'pending',
                'price' => 80,
                'scheduled_date' => now()->addDays(2),
                'client' => ['name' => 'Marie Dubois'],
                'service' => ['title' => 'Ménage appartement']
            ],
            [
                'id' => 2,
                'status' => 'confirmed',
                'price' => 120,
                'scheduled_date' => now()->addDays(5),
                'client' => ['name' => 'Pierre Martin'],
                'service' => ['title' => 'Jardinage']
            ],
            [
                'id' => 3,
                'status' => 'completed',
                'price' => 150,
                'scheduled_date' => now()->subDay(),
                'client' => ['name' => 'Sophie Laurent'],
                'service' => ['title' => 'Bricolage']
            ]
        ];

        // Notifications simulées
        $notifications = [
            [
                'id' => 1,
                'title' => 'Nouvelle réservation en attente',
                'created_at' => now()->subHours(2)->format('d/m/Y H:i')
            ],
            [
                'id' => 2,
                'title' => 'Paiement reçu pour le service de bricolage',
                'created_at' => now()->subHours(8)->format('d/m/Y H:i')
            ]
        ];

        return Inertia::render('Provider/Dashboard', [
            'provider' => $provider,
            'stats' => $dashboardStats,
            'recentBookings' => $recentBookings,
            'services' => $services,
            'upcomingBookings' => $upcomingBookings,
            'revenueData' => $monthlyEarnings,
            'notifications' => $notifications,
        ]);
    }

    public function bookings(Request $request)
    {
        $provider = Auth::user();

        // Données simulées de réservations
        $bookings = collect([
            [
                'id' => 1,
                'client_name' => 'Marie Dubois',
                'client_email' => 'marie.dubois@email.com',
                'service_title' => 'Ménage appartement',
                'scheduled_date' => now()->addDays(2),
                'created_at' => now()->subDays(3),
                'amount' => 80,
                'status' => 'accepted',
                'notes' => 'Appartement 3 pièces, 2e étage',
            ],
            [
                'id' => 2,
                'client_name' => 'Pierre Martin',
                'client_email' => 'pierre.martin@email.com',
                'service_title' => 'Jardinage',
                'scheduled_date' => now()->addDays(5),
                'created_at' => now()->subDays(1),
                'amount' => 120,
                'status' => 'pending',
                'notes' => 'Taille de haies et pelouse',
            ],
            [
                'id' => 3,
                'client_name' => 'Sophie Laurent',
                'client_email' => 'sophie.laurent@email.com',
                'service_title' => 'Bricolage',
                'scheduled_date' => now()->subDay(),
                'created_at' => now()->subDays(5),
                'amount' => 150,
                'status' => 'completed',
                'notes' => 'Montage de meubles IKEA',
            ],
        ]);

        // Filtrage basique
        if ($request->filled('status') && $request->status !== 'all') {
            $bookings = $bookings->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $bookings = $bookings->filter(function ($booking) use ($search) {
                return str_contains(strtolower($booking['client_name']), $search) ||
                       str_contains(strtolower($booking['service_title']), $search);
            });
        }

        // Pagination simulée
        $total = $bookings->count();
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $bookings = $bookings->forPage($currentPage, $perPage)->values();

        $bookingStats = [
            'total' => 18,
            'pending' => 3,
            'accepted' => 2,
            'completed' => 12,
            'cancelled' => 1,
        ];

        return Inertia::render('Provider/Bookings/Index', [
            'bookings' => [
                'data' => $bookings,
                'current_page' => $currentPage,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => ceil($total / $perPage),
            ],
            'bookingStats' => $bookingStats,
            'filters' => [
                'status' => $request->status ?? 'all',
                'search' => $request->search ?? '',
            ],
        ]);
    }

    public function earnings()
    {
        // Gains par mois (12 derniers mois)
        $monthlyEarnings = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $earnings = rand(300, 1200); // Données simulées

            $monthlyEarnings[] = [
                'month' => $month->format('M Y'),
                'earnings' => $earnings,
                'year' => $month->year,
                'month_num' => $month->month,
            ];
        }

        // Gains par service
        $earningsByService = [
            ['service_name' => 'Ménage', 'category' => 'Maison', 'earnings' => 1200],
            ['service_name' => 'Jardinage', 'category' => 'Extérieur', 'earnings' => 890],
            ['service_name' => 'Bricolage', 'category' => 'Réparation', 'earnings' => 650],
            ['service_name' => 'Repassage', 'category' => 'Maison', 'earnings' => 300],
        ];

        // Gains par semaine (8 dernières semaines)
        $weeklyEarnings = [];
        for ($i = 7; $i >= 0; $i--) {
            $startOfWeek = now()->subWeeks($i)->startOfWeek();
            $earnings = rand(50, 300);

            $weeklyEarnings[] = [
                'week' => $startOfWeek->format('d M'),
                'earnings' => $earnings,
            ];
        }

        $financialStats = [
            'total_earnings' => 8450.50,
            'net_earnings' => 7605.45, // Après commission 10%
            'total_commissions' => 845.05,
            'current_month' => 890.00,
            'last_month' => 750.00,
            'average_booking_value' => 115.50,
            'growth_rate' => 18.7,
        ];

        // Paiements en attente
        $pendingPayments = [
            [
                'client_name' => 'Marie Dubois',
                'service_title' => 'Ménage appartement',
                'amount' => 80,
                'completed_at' => now()->subDays(2),
            ],
            [
                'client_name' => 'Jean Moreau',
                'service_title' => 'Jardinage',
                'amount' => 150,
                'completed_at' => now()->subDays(1),
            ],
        ];

        return Inertia::render('Provider/Earnings', [
            'monthlyEarnings' => $monthlyEarnings,
            'weeklyEarnings' => $weeklyEarnings,
            'earningsByService' => $earningsByService,
            'financialStats' => $financialStats,
            'pendingPayments' => $pendingPayments,
        ]);
    }

    public function profile()
    {
        $provider = Auth::user()->load('userProfile');

        return Inertia::render('Provider/Profile/Edit', [
            'provider' => $provider,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $provider = Auth::user();

        $validated = $request->validate([
            'bio' => 'required|string|max:1000',
            'experience' => 'required|string|max:2000',
            'certifications' => 'nullable|array',
            'certifications.*' => 'string|max:255',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|array',
            'address.street' => 'nullable|string|max:255',
            'address.city' => 'nullable|string|max:100',
            'address.postal_code' => 'nullable|string|max:10',
            'address.country' => 'nullable|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        // Mettre à jour le profil
        if ($provider->userProfile) {
            $provider->userProfile->update($validated);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Get performance analytics
     */
    public function analytics()
    {
        // Analyse des performances par mois
        $performanceData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);

            // Données simulées
            $totalBookings = rand(5, 20);
            $completedBookings = rand(3, $totalBookings);
            $cancelledBookings = rand(0, 2);
            $earnings = rand(200, 1000);

            $performanceData[] = [
                'month' => $month->format('M Y'),
                'total_bookings' => $totalBookings,
                'completed_bookings' => $completedBookings,
                'cancelled_bookings' => $cancelledBookings,
                'completion_rate' => $totalBookings > 0 ? round(($completedBookings / $totalBookings) * 100, 1) : 0,
                'earnings' => $earnings,
            ];
        }

        // Top services par nombre de réservations
        $provider = Auth::user();
        $topServices = Service::where('provider_id', $provider->id)
            ->with('category')
            ->get()
            ->map(function ($service) {
                $service->bookings_count = rand(1, 15); // Simulé

                return $service;
            })
            ->sortByDesc('bookings_count')
            ->take(5)
            ->values();

        // Analyse des avis (simulée)
        $ratingAnalysis = [
            ['provider_rating' => 5, 'count' => 8],
            ['provider_rating' => 4, 'count' => 3],
            ['provider_rating' => 3, 'count' => 1],
            ['provider_rating' => 2, 'count' => 0],
            ['provider_rating' => 1, 'count' => 0],
        ];

        return Inertia::render('Provider/Analytics', [
            'performanceData' => $performanceData,
            'topServices' => $topServices,
            'ratingAnalysis' => $ratingAnalysis,
        ]);
    }

    /**
     * Get calendar data for provider
     */
    public function calendar(Request $request)
    {
        // Événements simulés pour le calendrier
        $events = [
            [
                'id' => 1,
                'title' => 'Ménage - Marie D.',
                'start' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'end' => now()->addDays(2)->addHours(3)->format('Y-m-d H:i:s'),
                'status' => 'accepted',
                'client_name' => 'Marie Dubois',
                'amount' => 80,
                'color' => '#3b82f6',
            ],
            [
                'id' => 2,
                'title' => 'Jardinage - Pierre M.',
                'start' => now()->addDays(5)->format('Y-m-d H:i:s'),
                'end' => now()->addDays(5)->addHours(4)->format('Y-m-d H:i:s'),
                'status' => 'pending',
                'client_name' => 'Pierre Martin',
                'amount' => 120,
                'color' => '#f59e0b',
            ],
            [
                'id' => 3,
                'title' => 'Bricolage - Sophie L.',
                'start' => now()->addDays(7)->format('Y-m-d H:i:s'),
                'end' => now()->addDays(7)->addHours(2)->format('Y-m-d H:i:s'),
                'status' => 'accepted',
                'client_name' => 'Sophie Laurent',
                'amount' => 150,
                'color' => '#3b82f6',
            ],
        ];

        return response()->json([
            'events' => $events,
        ]);
    }
}
