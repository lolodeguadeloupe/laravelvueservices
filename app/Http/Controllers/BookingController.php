<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:client')->except(['index', 'show']);
    }

    /**
     * Display a listing of the user's bookings.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Détermine si l'utilisateur est un client ou un prestataire
        $isProvider = $user->hasRole('provider');
        
        $query = BookingRequest::query()
            ->with(['service.category', 'client.profile', 'provider.profile'])
            ->orderBy('created_at', 'desc');

        if ($isProvider) {
            $query->forProvider($user->id);
        } else {
            $query->forClient($user->id);
        }

        // Filtres
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('service', function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%');
            });
        }

        $bookings = $query->paginate(10);

        return Inertia::render($isProvider ? 'Provider/Bookings/Index' : 'Client/Bookings/Index', [
            'bookings' => $bookings,
            'filters' => [
                'status' => $request->status ?? '',
                'search' => $request->search ?? '',
            ],
            'stats' => [
                'total' => BookingRequest::query()
                    ->{$isProvider ? 'forProvider' : 'forClient'}($user->id)
                    ->count(),
                'pending' => BookingRequest::query()
                    ->{$isProvider ? 'forProvider' : 'forClient'}($user->id)
                    ->pending()
                    ->count(),
                'accepted' => BookingRequest::query()
                    ->{$isProvider ? 'forProvider' : 'forClient'}($user->id)
                    ->accepted()
                    ->count(),
                'completed' => BookingRequest::query()
                    ->{$isProvider ? 'forProvider' : 'forClient'}($user->id)
                    ->completed()
                    ->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new booking.
     */
    public function create(Service $service)
    {
        $service->load(['category', 'provider.profile']);

        // Vérifier que l'utilisateur peut faire une demande pour ce service
        if (!$service->is_active) {
            return redirect()->back()->withErrors([
                'service' => 'Ce service n\'est pas disponible actuellement.'
            ]);
        }

        if (Auth::id() === $service->provider_id) {
            return redirect()->back()->withErrors([
                'service' => 'Vous ne pouvez pas faire une demande pour votre propre service.'
            ]);
        }

        return Inertia::render('Client/Bookings/Create', [
            'service' => $service,
        ]);
    }

    /**
     * Store a newly created booking in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:services,id',
            'preferred_datetime' => 'required|date|after:now',
            'client_address' => 'required|array',
            'client_address.street' => 'required|string|max:255',
            'client_address.city' => 'required|string|max:100',
            'client_address.postal_code' => 'required|string|max:10',
            'client_address.additional_info' => 'nullable|string|max:500',
            'client_notes' => 'nullable|string|max:1000',
        ]);

        $service = Service::findOrFail($validated['service_id']);

        // Vérifications de sécurité
        if (!$service->is_active) {
            throw ValidationException::withMessages([
                'service_id' => 'Ce service n\'est pas disponible actuellement.'
            ]);
        }

        if (Auth::id() === $service->provider_id) {
            throw ValidationException::withMessages([
                'service_id' => 'Vous ne pouvez pas faire une demande pour votre propre service.'
            ]);
        }

        // Vérifier qu'il n'y a pas déjà une demande en cours
        $existingBooking = BookingRequest::where('service_id', $service->id)
            ->where('client_id', Auth::id())
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingBooking) {
            throw ValidationException::withMessages([
                'service_id' => 'Vous avez déjà une demande en cours pour ce service.'
            ]);
        }

        $booking = BookingRequest::create([
            'service_id' => $service->id,
            'client_id' => Auth::id(),
            'provider_id' => $service->provider_id,
            'status' => 'pending',
            'preferred_datetime' => $validated['preferred_datetime'],
            'client_address' => $validated['client_address'],
            'client_notes' => $validated['client_notes'],
        ]);

        // TODO: Envoyer une notification au prestataire

        return redirect()->route('bookings.show', $booking->uuid)
            ->with('success', 'Votre demande de service a été envoyée au prestataire.');
    }

    /**
     * Display the specified booking.
     */
    public function show(BookingRequest $booking)
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur peut voir cette réservation
        if ($booking->client_id !== $user->id && $booking->provider_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas autorisé à voir cette réservation.');
        }

        $booking->load([
            'service.category',
            'client.profile',
            'provider.profile',
            'cancelledBy'
        ]);

        $isProvider = $user->hasRole('provider');
        $isClient = $user->hasRole('client');

        return Inertia::render('Shared/Bookings/Show', [
            'booking' => $booking,
            'isProvider' => $isProvider,
            'isClient' => $isClient,
            'canAccept' => $isProvider && $booking->canBeAccepted(),
            'canReject' => $isProvider && $booking->canBeRejected(),
            'canComplete' => $isProvider && $booking->canBeCompleted(),
            'canCancel' => $booking->canBeCancelled() && 
                         ($isClient || ($isProvider && $booking->isPending())),
        ]);
    }

    /**
     * Accept a booking request (Provider only).
     */
    public function accept(BookingRequest $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->canBeAccepted()) {
            return back()->withErrors([
                'booking' => 'Cette demande ne peut plus être acceptée.'
            ]);
        }

        $booking->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // TODO: Envoyer notification au client

        return back()->with('success', 'Demande acceptée avec succès.');
    }

    /**
     * Reject a booking request (Provider only).
     */
    public function reject(Request $request, BookingRequest $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->canBeRejected()) {
            return back()->withErrors([
                'booking' => 'Cette demande ne peut plus être refusée.'
            ]);
        }

        $request->validate([
            'provider_notes' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'provider_notes' => $request->provider_notes,
        ]);

        // TODO: Envoyer notification au client

        return back()->with('success', 'Demande refusée.');
    }

    /**
     * Complete a booking (Provider only).
     */
    public function complete(Request $request, BookingRequest $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->canBeCompleted()) {
            return back()->withErrors([
                'booking' => 'Cette prestation ne peut pas être marquée comme terminée.'
            ]);
        }

        $request->validate([
            'final_price' => 'nullable|numeric|min:0',
            'provider_notes' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status' => 'completed',
            'completed_at' => now(),
            'final_price' => $request->final_price ?? $booking->quoted_price,
            'provider_notes' => $request->provider_notes,
        ]);

        // TODO: Envoyer notification au client

        return back()->with('success', 'Prestation marquée comme terminée.');
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, BookingRequest $booking)
    {
        $user = Auth::user();

        // Vérifier les permissions
        if ($booking->client_id !== $user->id && $booking->provider_id !== $user->id) {
            abort(403, 'Vous n\'êtes pas autorisé à annuler cette réservation.');
        }

        if (!$booking->canBeCancelled()) {
            return back()->withErrors([
                'booking' => 'Cette réservation ne peut plus être annulée.'
            ]);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->cancellation_reason,
            'cancelled_by' => $user->id,
        ]);

        // TODO: Envoyer notification à l'autre partie

        return back()->with('success', 'Réservation annulée.');
    }

    /**
     * Update provider quote for a booking.
     */
    public function quote(Request $request, BookingRequest $booking)
    {
        $this->authorize('update', $booking);

        if (!$booking->isPending()) {
            return back()->withErrors([
                'booking' => 'Vous ne pouvez plus modifier le devis pour cette demande.'
            ]);
        }

        $request->validate([
            'quoted_price' => 'required|numeric|min:0',
            'estimated_duration' => 'nullable|integer|min:15',
            'confirmed_datetime' => 'nullable|date|after:now',
            'provider_notes' => 'nullable|string|max:500',
        ]);

        $booking->update([
            'quoted_price' => $request->quoted_price,
            'estimated_duration' => $request->estimated_duration,
            'confirmed_datetime' => $request->confirmed_datetime,
            'provider_notes' => $request->provider_notes,
        ]);

        // TODO: Envoyer notification au client

        return back()->with('success', 'Devis mis à jour avec succès.');
    }
}