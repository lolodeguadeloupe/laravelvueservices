<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProviderDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('can:access provider dashboard');
    }

    public function index()
    {
        $provider = Auth::user();

        // Statistiques du prestataire
        $stats = [
            'total_services' => $provider->providedServices()->count(),
            'active_services' => $provider->providedServices()->where('is_active', true)->count(),
            'pending_requests' => $provider->providerBookings()->where('status', 'pending')->count(),
            'completed_bookings' => $provider->providerBookings()->where('status', 'completed')->count(),
            'total_earnings' => $provider->providerBookings()
                ->where('status', 'completed')
                ->sum('total_amount'),
            'rating' => $provider->getProviderRating(),
            'reviews_count' => $provider->profile?->reviews_count ?? 0,
        ];

        // Demandes récentes
        $recentRequests = $provider->providerBookings()
            ->with(['client.profile', 'service'])
            ->latest()
            ->limit(5)
            ->get();

        // Services du prestataire
        $services = $provider->providedServices()
            ->with('category')
            ->latest()
            ->limit(3)
            ->get();

        return Inertia::render('Provider/Dashboard', [
            'stats' => $stats,
            'recentRequests' => $recentRequests,
            'services' => $services,
            'provider' => $provider->load('profile'),
        ]);
    }

    public function services()
    {
        $provider = Auth::user();

        $services = $provider->providedServices()
            ->with(['category', 'bookingRequests'])
            ->withCount('bookingRequests')
            ->paginate(10);

        return Inertia::render('Provider/Services/Index', [
            'services' => $services,
        ]);
    }

    public function bookings(Request $request)
    {
        $provider = Auth::user();

        $query = $provider->providerBookings()
            ->with(['client.profile', 'service']);

        // Filtres
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->whereHas('client.profile', function ($q) use ($request) {
                $q->where('first_name', 'like', '%'.$request->search.'%')
                    ->orWhere('last_name', 'like', '%'.$request->search.'%');
            });
        }

        $bookings = $query->latest()->paginate(15);

        return Inertia::render('Provider/Bookings/Index', [
            'bookings' => $bookings,
            'filters' => [
                'status' => $request->status ?? 'all',
                'search' => $request->search ?? '',
            ],
        ]);
    }

    public function earnings()
    {
        $provider = Auth::user();

        // Gains par mois (12 derniers mois)
        $monthlyEarnings = $provider->providerBookings()
            ->where('status', 'completed')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total_amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();

        // Statistiques financières
        $financialStats = [
            'total_earnings' => $provider->providerBookings()
                ->where('status', 'completed')
                ->sum('total_amount'),
            'current_month' => $provider->providerBookings()
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
            'last_month' => $provider->providerBookings()
                ->where('status', 'completed')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->sum('total_amount'),
            'average_booking_value' => $provider->providerBookings()
                ->where('status', 'completed')
                ->avg('total_amount'),
        ];

        return Inertia::render('Provider/Earnings', [
            'monthlyEarnings' => $monthlyEarnings,
            'financialStats' => $financialStats,
        ]);
    }

    public function profile()
    {
        $provider = Auth::user()->load('profile');

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
        if ($provider->profile) {
            $provider->profile->update($validated);
        }

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}
