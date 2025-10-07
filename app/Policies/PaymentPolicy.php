<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;
use App\Models\BookingRequest;

class PaymentPolicy
{
    public function pay(User $user, BookingRequest $booking): bool
    {
        // Seul le client peut payer sa réservation
        if ($user->id !== $booking->client_id) {
            return false;
        }

        // La réservation doit être acceptée et avoir un prix final
        if ($booking->status !== 'accepted' || !$booking->final_price) {
            return false;
        }

        // Vérifier qu'il n'y a pas déjà un paiement pour cette réservation
        return !Payment::where('booking_request_id', $booking->id)->exists();
    }

    public function view(User $user, Payment $payment): bool
    {
        // Le client, le prestataire ou un admin peuvent voir le paiement
        return $user->id === $payment->client_id 
            || $user->id === $payment->provider_id 
            || $user->hasRole('admin');
    }

    public function refund(User $user, Payment $payment): bool
    {
        // Seuls les admins peuvent faire des remboursements
        if (!$user->hasRole('admin')) {
            return false;
        }

        // Le paiement doit être complété pour pouvoir être remboursé
        return $payment->isCompleted();
    }
}