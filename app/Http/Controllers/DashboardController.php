<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer les réservations de l'utilisateur avec les relations
        $bookings = BookingRequest::query()
            ->with([
                'service.category',
                'service.provider.profile',
                'service.media'
            ])
            ->where('client_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Services favoris (fonctionnalité à implémenter plus tard)
        $favoriteServices = collect();

        // Calculer les statistiques
        $stats = [
            'totalBookings' => $bookings->count(),
            'activeBookings' => $bookings->whereIn('status', ['pending', 'confirmed', 'in_progress'])->count(),
            'completedBookings' => $bookings->where('status', 'completed')->count(),
            'totalSpent' => $bookings->where('status', 'completed')->sum('final_price'),
        ];

        return Inertia::render('Dashboard', [
            'user' => $user,
            'bookings' => $bookings,
            'favoriteServices' => $favoriteServices,
            'stats' => $stats,
        ]);
    }
}