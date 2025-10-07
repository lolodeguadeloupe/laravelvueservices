<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\StoreServiceRequest;
use App\Http\Requests\Provider\UpdateServiceRequest;
use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Service::query()
            ->where('provider_id', $request->user()->id)
            ->with(['category'])
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $services = $query->paginate(12);
        $categories = Category::active()->ordered()->get();

        return Inertia::render('Provider/Services/Index', [
            'services' => $services,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $categories = Category::active()->ordered()->get();

        return Inertia::render('Provider/Services/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Gestion des images
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('services/images', 'public');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        $service = Service::create($validated);

        return response()->json([
            'message' => 'Service créé avec succès.',
            'service' => $service->load('category'),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): Response
    {
        // Vérifier que le service appartient au prestataire connecté
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $service->load(['category', 'bookingRequests' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return Inertia::render('Provider/Services/Show', [
            'service' => $service,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): Response
    {
        // Vérifier que le service appartient au prestataire connecté
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $categories = Category::active()->ordered()->get();

        return Inertia::render('Provider/Services/Edit', [
            'service' => $service->load('category'),
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $validated = $request->validated();

        // Gestion des nouvelles images
        if ($request->hasFile('images')) {
            // Supprimer les anciennes images
            if ($service->images) {
                foreach ($service->images as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('services/images', 'public');
                $imagePaths[] = $path;
            }
            $validated['images'] = $imagePaths;
        }

        $service->update($validated);

        return response()->json([
            'message' => 'Service mis à jour avec succès.',
            'service' => $service->load('category'),
        ]);
    }

    /**
     * Toggle service status.
     */
    public function toggleStatus(Service $service): JsonResponse
    {
        // Vérifier que le service appartient au prestataire connecté
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        $status = $service->is_active ? 'activé' : 'désactivé';

        return response()->json([
            'message' => "Service {$status} avec succès.",
            'service' => $service,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): JsonResponse
    {
        // Vérifier que le service appartient au prestataire connecté
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        // Vérifier qu'il n'y a pas de réservations actives
        $activeBookings = $service->bookingRequests()
            ->whereIn('status', ['pending', 'accepted', 'in_progress'])
            ->count();

        if ($activeBookings > 0) {
            return response()->json([
                'message' => 'Impossible de supprimer un service avec des réservations actives.',
            ], 422);
        }

        // Supprimer les images
        if ($service->images) {
            foreach ($service->images as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $service->delete();

        return response()->json([
            'message' => 'Service supprimé avec succès.',
        ]);
    }

    /**
     * Upload media for a service.
     */
    public function uploadMedia(Request $request, Service $service): JsonResponse
    {
        // Vérifier que le service appartient au prestataire connecté
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'media' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $path = $request->file('media')->store('services/images', 'public');

        $images = $service->images ?? [];
        $images[] = $path;

        $service->update(['images' => $images]);

        return response()->json([
            'message' => 'Image ajoutée avec succès.',
            'path' => $path,
            'service' => $service,
        ]);
    }

    /**
     * Delete media from a service.
     */
    public function deleteMedia(Service $service, string $media): JsonResponse
    {
        // Vérifier que le service appartient au prestataire connecté
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $images = $service->images ?? [];
        $mediaIndex = array_search($media, $images);

        if ($mediaIndex === false) {
            return response()->json([
                'message' => 'Image non trouvée.',
            ], 404);
        }

        // Supprimer l'image du stockage
        Storage::disk('public')->delete($media);

        // Retirer l'image de la liste
        unset($images[$mediaIndex]);
        $images = array_values($images); // Réindexer le tableau

        $service->update(['images' => $images]);

        return response()->json([
            'message' => 'Image supprimée avec succès.',
            'service' => $service,
        ]);
    }

    /**
     * Set primary media for a service.
     */
    public function setPrimaryMedia(Service $service, string $media): JsonResponse
    {
        // Vérifier que le service appartient au prestataire connecté
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $images = $service->images ?? [];
        $mediaIndex = array_search($media, $images);

        if ($mediaIndex === false) {
            return response()->json([
                'message' => 'Image non trouvée.',
            ], 404);
        }

        // Déplacer l'image en première position
        $primaryImage = $images[$mediaIndex];
        unset($images[$mediaIndex]);
        array_unshift($images, $primaryImage);

        $service->update(['images' => array_values($images)]);

        return response()->json([
            'message' => 'Image principale définie avec succès.',
            'service' => $service,
        ]);
    }
}
