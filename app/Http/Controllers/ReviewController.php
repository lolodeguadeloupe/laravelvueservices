<?php

namespace App\Http\Controllers;

use App\Models\BookingRequest;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReviewController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Review::approved()
            ->with(['reviewer', 'reviewed', 'bookingRequest.service'])
            ->recent();

        // Filtres
        if ($request->user_id) {
            $query->forUser($request->user_id);
        }

        if ($request->rating) {
            $query->byRating($request->rating);
        }

        if ($request->verified) {
            $query->verified();
        }

        if ($request->featured) {
            $query->featured();
        }

        $reviews = $query->paginate(12);

        return Inertia::render('Reviews/Index', [
            'reviews' => $reviews,
            'filters' => $request->only(['user_id', 'rating', 'verified', 'featured']),
        ]);
    }

    public function show(Review $review): Response
    {
        $this->authorize('view', $review);

        $review->load(['reviewer', 'reviewed', 'bookingRequest.service', 'reactions']);

        return Inertia::render('Reviews/Show', [
            'review' => $review,
        ]);
    }

    public function create(BookingRequest $booking): Response
    {
        $this->authorize('review', $booking);

        $booking->load(['service', 'provider', 'client']);

        // Vérifier s'il existe déjà un avis
        $existingReview = Review::where('booking_request_id', $booking->id)
            ->where('reviewer_id', auth()->id())
            ->first();

        if ($existingReview) {
            return redirect()->route('reviews.show', $existingReview)
                ->with('info', 'Vous avez déjà laissé un avis pour cette réservation.');
        }

        return Inertia::render('Reviews/Create', [
            'booking' => $booking,
            'reviewerType' => auth()->user()->isClient() ? 'client' : 'provider',
        ]);
    }

    public function store(Request $request, BookingRequest $booking): JsonResponse
    {
        $this->authorize('review', $booking);

        $validated = $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'quality_rating' => 'nullable|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'punctuality_rating' => 'nullable|integer|min:1|max:5',
            'professionalism_rating' => 'nullable|integer|min:1|max:5',
            'value_rating' => 'nullable|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|max:1000',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Vérifier qu'il n'existe pas déjà un avis
        $existingReview = Review::where('booking_request_id', $booking->id)
            ->where('reviewer_id', auth()->id())
            ->first();

        if ($existingReview) {
            return response()->json([
                'error' => 'Vous avez déjà laissé un avis pour cette réservation.',
            ], 422);
        }

        try {
            DB::transaction(function () use ($validated, $booking, $request) {
                $photos = [];
                
                if ($request->hasFile('photos')) {
                    foreach ($request->file('photos') as $photo) {
                        $path = $photo->store('reviews', 'public');
                        $photos[] = $path;
                    }
                }

                $reviewed_id = auth()->user()->isClient() 
                    ? $booking->provider_id 
                    : $booking->client_id;

                Review::create([
                    'booking_request_id' => $booking->id,
                    'reviewer_id' => auth()->id(),
                    'reviewed_id' => $reviewed_id,
                    'reviewer_type' => auth()->user()->isClient() ? 'client' : 'provider',
                    'overall_rating' => $validated['overall_rating'],
                    'quality_rating' => $validated['quality_rating'] ?? null,
                    'communication_rating' => $validated['communication_rating'] ?? null,
                    'punctuality_rating' => $validated['punctuality_rating'] ?? null,
                    'professionalism_rating' => $validated['professionalism_rating'] ?? null,
                    'value_rating' => $validated['value_rating'] ?? null,
                    'title' => $validated['title'] ?? null,
                    'comment' => $validated['comment'],
                    'photos' => $photos,
                    'status' => 'approved', // Auto-approuvé pour l'instant
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Votre avis a été publié avec succès.',
                'redirect_url' => route('bookings.show', $booking),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue lors de la publication de votre avis.',
            ], 500);
        }
    }

    public function respond(Request $request, Review $review): JsonResponse
    {
        $this->authorize('respond', $review);

        $validated = $request->validate([
            'response' => 'required|string|max:500',
        ]);

        try {
            $review->addResponse($validated['response'], auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'Votre réponse a été ajoutée avec succès.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function helpful(Review $review): JsonResponse
    {
        $this->authorize('react', $review);

        try {
            $review->markAsHelpful(auth()->user());

            return response()->json([
                'success' => true,
                'helpful_count' => $review->fresh()->helpful_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    public function notHelpful(Review $review): JsonResponse
    {
        $this->authorize('react', $review);

        try {
            $review->markAsNotHelpful(auth()->user());

            return response()->json([
                'success' => true,
                'not_helpful_count' => $review->fresh()->not_helpful_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    public function report(Request $request, Review $review): JsonResponse
    {
        $this->authorize('react', $review);

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $review->report(auth()->user(), $validated['reason']);

            return response()->json([
                'success' => true,
                'message' => 'L\'avis a été signalé. Merci pour votre vigilance.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    // Routes d'administration
    public function moderate(): Response
    {
        $this->authorize('moderate-reviews');

        $pendingReviews = Review::where('status', 'pending')
            ->with(['reviewer', 'reviewed', 'bookingRequest.service'])
            ->recent()
            ->paginate(10, ['*'], 'pending');

        $reportedReviews = Review::where('status', 'reported')
            ->with(['reviewer', 'reviewed', 'bookingRequest.service', 'reactions' => function($query) {
                $query->where('type', 'report');
            }])
            ->recent()
            ->paginate(10, ['*'], 'reported');

        return Inertia::render('Admin/Reviews/Moderate', [
            'pendingReviews' => $pendingReviews,
            'reportedReviews' => $reportedReviews,
        ]);
    }

    public function approve(Review $review): JsonResponse
    {
        $this->authorize('moderate-reviews');

        try {
            $review->approve(auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'L\'avis a été approuvé.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue.',
            ], 500);
        }
    }

    public function reject(Request $request, Review $review): JsonResponse
    {
        $this->authorize('moderate-reviews');

        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $review->reject($validated['reason'], auth()->user());

            return response()->json([
                'success' => true,
                'message' => 'L\'avis a été rejeté.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Une erreur est survenue.',
            ], 500);
        }
    }
}
