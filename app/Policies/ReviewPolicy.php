<?php

namespace App\Policies;

use App\Models\BookingRequest;
use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Tout le monde peut voir la liste des avis
    }

    public function view(User $user, Review $review): bool
    {
        // Les avis approuvés sont visibles par tous
        if ($review->isApproved()) {
            return true;
        }

        // Sinon, seuls les participants ou admins peuvent voir
        return $user->id === $review->reviewer_id 
            || $user->id === $review->reviewed_id 
            || $user->hasRole('admin');
    }

    public function review(User $user, BookingRequest $booking): bool
    {
        // La réservation doit être terminée
        if ($booking->status !== 'completed') {
            return false;
        }

        // L'utilisateur doit être soit le client soit le prestataire
        if ($user->id !== $booking->client_id && $user->id !== $booking->provider_id) {
            return false;
        }

        // Vérifier qu'il n'y a pas déjà un avis de cet utilisateur
        $existingReview = Review::where('booking_request_id', $booking->id)
            ->where('reviewer_id', $user->id)
            ->exists();

        return !$existingReview;
    }

    public function respond(User $user, Review $review): bool
    {
        // Seule la personne évaluée peut répondre
        if ($user->id !== $review->reviewed_id) {
            return false;
        }

        // L'avis doit être approuvé et sans réponse
        return $review->isApproved() && !$review->hasResponse();
    }

    public function react(User $user, Review $review): bool
    {
        // Tout utilisateur connecté peut réagir (sauf l'auteur de l'avis)
        if ($user->id === $review->reviewer_id) {
            return false;
        }

        // L'avis doit être approuvé
        return $review->isApproved();
    }

    public function update(User $user, Review $review): bool
    {
        // Seul l'auteur peut modifier son avis (dans les 24h)
        if ($user->id !== $review->reviewer_id) {
            return false;
        }

        // Seulement si l'avis est encore en attente ou dans les 24h
        return $review->isPending() || $review->created_at->diffInHours() < 24;
    }

    public function delete(User $user, Review $review): bool
    {
        // L'auteur peut supprimer son avis ou un admin
        return $user->id === $review->reviewer_id || $user->hasRole('admin');
    }

    // Permissions d'administration
    public function moderate(User $user): bool
    {
        return $user->hasRole('admin') || $user->can('moderate-reviews');
    }
}
