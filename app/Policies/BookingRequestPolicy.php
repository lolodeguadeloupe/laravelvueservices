<?php

namespace App\Policies;

use App\Models\BookingRequest;
use App\Models\User;

class BookingRequestPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['client', 'provider', 'admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BookingRequest $bookingRequest): bool
    {
        return $bookingRequest->client_id === $user->id ||
               $bookingRequest->provider_id === $user->id ||
               $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole('client');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BookingRequest $bookingRequest): bool
    {
        // Le prestataire peut modifier ses propres réservations
        if ($user->hasRole('provider') && $bookingRequest->provider_id === $user->id) {
            return true;
        }

        // Le client peut modifier ses propres réservations (pour annulation)
        if ($user->hasRole('client') && $bookingRequest->client_id === $user->id) {
            return true;
        }

        // Les admins peuvent tout modifier
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BookingRequest $bookingRequest): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BookingRequest $bookingRequest): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BookingRequest $bookingRequest): bool
    {
        return false;
    }
}
