<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\BookingRequest;
use App\Models\Category;
use App\Models\Review;
use App\Models\ReviewReport;
use App\Models\Service;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\BadgeService;
use App\Services\ModerationService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly BadgeService $badgeService,
        private readonly ModerationService $moderationService
    ) {}

    /**
     * Dashboard principal d'administration
     */
    public function index(): Response
    {
        // Calculer les pourcentages de croissance
        $lastMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)->count();
        $thisMonthUsers = User::whereMonth('created_at', now()->month)->count();
        $userGrowth = $lastMonthUsers > 0 ? round((($thisMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1) : 0;

        $lastMonthProviders = User::where('user_type', 'provider')->whereMonth('created_at', now()->subMonth()->month)->count();
        $thisMonthProviders = User::where('user_type', 'provider')->whereMonth('created_at', now()->month)->count();
        $providerGrowth = $lastMonthProviders > 0 ? round((($thisMonthProviders - $lastMonthProviders) / $lastMonthProviders) * 100, 1) : 0;

        $lastMonthBookings = BookingRequest::whereMonth('created_at', now()->subMonth()->month)->count();
        $thisMonthBookings = BookingRequest::whereMonth('created_at', now()->month)->count();
        $bookingGrowth = $lastMonthBookings > 0 ? round((($thisMonthBookings - $lastMonthBookings) / $lastMonthBookings) * 100, 1) : 0;

        $lastMonthRevenue = BookingRequest::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('final_price');
        $thisMonthRevenue = BookingRequest::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('final_price');
        $revenueGrowth = $lastMonthRevenue > 0 ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        // Statistiques principales pour les cartes
        $stats = [
            'users' => [
                'total' => User::count(),
                'clients' => User::where('user_type', 'client')->count(),
                'providers' => User::where('user_type', 'provider')->count(),
                'admins' => User::where('user_type', 'admin')->count(),
                'growth' => $userGrowth,
                'provider_growth' => $providerGrowth,
                'top_rated' => User::providers()
                    ->whereHas('profile', fn ($q) => $q->where('rating', '>=', 4.0))
                    ->with(['profile', 'services'])
                    ->withCount('services')
                    ->orderByDesc('profile.rating')
                    ->limit(5)
                    ->get(),
            ],
            'bookings' => [
                'total' => BookingRequest::count(),
                'this_month' => $thisMonthBookings,
                'pending' => BookingRequest::where('status', 'pending')->count(),
                'completed' => BookingRequest::where('status', 'completed')->count(),
                'growth' => $bookingGrowth,
            ],
            'revenue' => [
                'total' => BookingRequest::where('status', 'completed')->sum('final_price'),
                'this_month' => $thisMonthRevenue,
                'growth' => $revenueGrowth,
            ],
            'categories' => [
                'total' => Category::count(),
                'top' => Category::withCount('services')
                    ->where('status', 'active')
                    ->orderBy('services_count', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($category) {
                        return [
                            'id' => $category->id,
                            'name' => $category->name,
                            'icon' => $category->icon ?? 'briefcase',
                            'services_count' => $category->services_count,
                        ];
                    }),
            ],
            'badges' => [
                'total' => Badge::count(),
                'awarded_count' => UserBadge::count(),
                'recent_awards' => UserBadge::with(['badge:id,name,icon', 'user:id,name'])
                    ->latest()
                    ->limit(10)
                    ->get(),
            ],
            'moderation' => [
                'pending_reviews' => Review::where('moderation_status', 'pending')->count(),
                'flagged_reviews' => Review::where('moderation_status', 'flagged')->count(),
                'reports_count' => ReviewReport::where('status', 'pending')->count(),
            ],
        ];

        // Données pour les graphiques d'inscription
        $charts = [
            'registrations' => $this->getUserRegistrationData(),
        ];

        // Activité récente
        $recentActivity = collect();

        // Nouveaux utilisateurs
        User::latest()->limit(3)->get(['id', 'name', 'user_type', 'created_at'])
            ->each(function ($user) use ($recentActivity) {
                $recentActivity->push([
                    'id' => 'user_'.$user->id,
                    'type' => 'user_registered',
                    'description' => "Nouvel utilisateur {$user->name} ({$user->user_type}) inscrit",
                    'created_at' => $user->created_at->toISOString(),
                ]);
            });

        // Nouveaux services
        Service::with('user:id,name')->latest()->limit(3)->get()
            ->each(function ($service) use ($recentActivity) {
                $recentActivity->push([
                    'id' => 'service_'.$service->id,
                    'type' => 'service_created',
                    'description' => "Nouveau service '{$service->title}' par {$service->user->name}",
                    'created_at' => $service->created_at->toISOString(),
                ]);
            });

        // Nouvelles réservations
        BookingRequest::with(['client:id,name', 'service:id,title'])->latest()->limit(3)->get()
            ->each(function ($booking) use ($recentActivity) {
                $recentActivity->push([
                    'id' => 'booking_'.$booking->id,
                    'type' => 'booking_created',
                    'description' => "Nouvelle réservation '{$booking->service->title}' par {$booking->client->name}",
                    'created_at' => $booking->created_at->toISOString(),
                ]);
            });

        // Badges récents
        UserBadge::with(['badge:id,name', 'user:id,name'])->latest()->limit(3)->get()
            ->each(function ($userBadge) use ($recentActivity) {
                $recentActivity->push([
                    'id' => 'badge_'.$userBadge->id,
                    'type' => 'badge_awarded',
                    'description' => "Badge '{$userBadge->badge->name}' attribué à {$userBadge->user->name}",
                    'created_at' => $userBadge->created_at->toISOString(),
                ]);
            });

        // Trier par date décroissante et limiter à 10
        $recentActivity = $recentActivity->sortByDesc('created_at')->take(10)->values();

        return Inertia::render('Admin/Dashboard/Index', [
            'stats' => $stats,
            'charts' => $charts,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * Gestion des utilisateurs
     */
    public function users(): Response
    {
        $users = User::with('profile')
            ->withCount(['services', 'clientBookings', 'providerBookings', 'userBadges'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'stats' => [
                'total' => User::count(),
                'clients' => User::where('user_type', 'client')->count(),
                'providers' => User::where('user_type', 'provider')->count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
            ],
        ]);
    }

    /**
     * Gestion des services et catégories
     */
    public function services(): Response
    {
        $services = Service::with(['user:id,name', 'category:id,name'])
            ->withCount('bookings')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $categories = Category::withCount('services')->get();

        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
            'categories' => $categories,
            'stats' => [
                'total_services' => Service::count(),
                'active_services' => Service::where('is_active', true)->count(),
                'total_categories' => Category::count(),
                'avg_price' => round(Service::avg('price_min'), 2),
            ],
        ]);
    }

    /**
     * Gestion des réservations
     */
    public function bookings(): Response
    {
        $bookings = BookingRequest::with(['client:id,name', 'provider:id,name', 'service:id,title'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Bookings/Index', [
            'bookings' => $bookings,
            'stats' => [
                'total' => BookingRequest::count(),
                'pending' => BookingRequest::where('status', 'pending')->count(),
                'confirmed' => BookingRequest::where('status', 'confirmed')->count(),
                'completed' => BookingRequest::where('status', 'completed')->count(),
                'revenue_total' => BookingRequest::where('status', 'completed')->sum('final_price'),
            ],
        ]);
    }

    /**
     * Statistiques avancées
     */
    public function analytics(): Response
    {
        $analytics = [
            'user_engagement' => $this->getUserEngagementMetrics(),
            'service_performance' => $this->getServicePerformanceMetrics(),
            'financial_overview' => $this->getFinancialOverview(),
            'quality_metrics' => $this->getQualityMetrics(),
        ];

        return Inertia::render('Admin/Analytics/Index', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Paramètres de la plateforme
     */
    public function settings(): Response
    {
        return Inertia::render('Admin/Settings/Index', [
            'settings' => [
                'platform' => [
                    'name' => config('app.name'),
                    'url' => config('app.url'),
                    'timezone' => config('app.timezone'),
                ],
                'features' => [
                    'badges_enabled' => true,
                    'auto_moderation' => true,
                    'manual_verification' => true,
                ],
                'moderation' => [
                    'auto_approve_threshold' => 0,
                    'manual_review_threshold' => 3,
                    'auto_flag_keywords' => $this->moderationService->suspiciousKeywords ?? [],
                ],
            ],
        ]);
    }

    // Méthodes privées pour les données de graphiques

    private function getUserRegistrationData(): array
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'clients' => User::where('user_type', 'client')->whereDate('created_at', $date)->count(),
                'providers' => User::where('user_type', 'provider')->whereDate('created_at', $date)->count(),
            ];
        }

        return $data;
    }

    private function getBookingsEvolutionData(): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'bookings' => BookingRequest::whereDate('created_at', $date)->count(),
                'completed' => BookingRequest::whereDate('created_at', $date)
                    ->where('status', 'completed')->count(),
            ];
        }

        return $data;
    }

    private function getRevenueChartData(): array
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = BookingRequest::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'completed')
                ->sum('final_price');

            $data[] = [
                'month' => $date->format('Y-m'),
                'revenue' => $revenue,
            ];
        }

        return $data;
    }

    private function getRatingsDistributionData(): array
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[] = [
                'rating' => $i,
                'count' => Review::where('overall_rating', $i)->count(),
            ];
        }

        return $distribution;
    }

    private function getUserEngagementMetrics(): array
    {
        return [
            'daily_active_users' => User::whereDate('last_login_at', today())->count(),
            'weekly_active_users' => User::whereDate('last_login_at', '>=', now()->subWeek())->count(),
            'monthly_active_users' => User::whereDate('last_login_at', '>=', now()->subMonth())->count(),
            'avg_session_duration' => 25, // En minutes - à calculer selon votre système de tracking
            'bounce_rate' => 35, // En pourcentage - à calculer selon votre système analytics
        ];
    }

    private function getServicePerformanceMetrics(): array
    {
        return [
            'most_popular_categories' => Category::withCount('services')
                ->orderBy('services_count', 'desc')
                ->limit(5)
                ->get(),
            'top_rated_services' => Service::whereHas('reviews')
                ->with(['reviews' => fn ($q) => $q->published()])
                ->get()
                ->map(function ($service) {
                    $avgRating = $service->reviews->avg('overall_rating');

                    return [
                        'id' => $service->id,
                        'title' => $service->title,
                        'rating' => round($avgRating, 1),
                        'reviews_count' => $service->reviews->count(),
                    ];
                })
                ->sortByDesc('rating')
                ->take(5)
                ->values(),
        ];
    }

    private function getFinancialOverview(): array
    {
        $thisMonth = BookingRequest::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->sum('final_price');

        $lastMonth = BookingRequest::where('status', 'completed')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->sum('final_price');

        return [
            'revenue_this_month' => $thisMonth,
            'revenue_last_month' => $lastMonth,
            'growth_rate' => $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 0,
            'avg_transaction_value' => round(BookingRequest::where('status', 'completed')->avg('final_price'), 2),
            'total_transactions' => BookingRequest::where('status', 'completed')->count(),
        ];
    }

    private function getQualityMetrics(): array
    {
        return [
            'average_rating' => round(Review::published()->avg('overall_rating'), 1),
            'satisfaction_rate' => round(Review::published()->where('overall_rating', '>=', 4)->count() /
                max(Review::published()->count(), 1) * 100, 1),
            'moderation_efficiency' => round($this->moderationService->getModerationStats()['auto_approved_today'] /
                max(Review::whereDate('created_at', today())->count(), 1) * 100, 1),
            'complaint_rate' => round(ReviewReport::count() /
                max(Review::count(), 1) * 100, 2),
        ];
    }
}
