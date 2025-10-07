<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Service $service): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $zones = $service->zones()
            ->active()
            ->get();

        return response()->json([
            'zones' => $zones,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Service $service): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:radius,postal_codes,custom'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius_km' => ['nullable', 'integer', 'min:1', 'max:200'],
            'postal_codes' => ['nullable', 'array'],
            'postal_codes.*' => ['string', 'regex:/^[0-9]{5}$/'],
            'excluded_areas' => ['nullable', 'array'],
            'travel_cost_per_km' => ['nullable', 'numeric', 'min:0'],
            'min_travel_time' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        // Validation spécifique par type
        if ($validated['type'] === 'radius') {
            if (empty($validated['latitude']) || empty($validated['longitude']) || empty($validated['radius_km'])) {
                return response()->json([
                    'message' => 'La latitude, longitude et rayon sont requis pour une zone de rayon.',
                    'errors' => [
                        'latitude' => ['Latitude requise pour une zone de rayon.'],
                        'longitude' => ['Longitude requise pour une zone de rayon.'],
                        'radius_km' => ['Rayon requis pour une zone de rayon.'],
                    ],
                ], 422);
            }
        }

        if ($validated['type'] === 'postal_codes') {
            if (empty($validated['postal_codes'])) {
                return response()->json([
                    'message' => 'Au moins un code postal est requis pour une zone par codes postaux.',
                    'errors' => [
                        'postal_codes' => ['Au moins un code postal est requis.'],
                    ],
                ], 422);
            }
        }

        $validated['service_id'] = $service->id;

        $zone = ServiceZone::create($validated);

        return response()->json([
            'message' => 'Zone d\'intervention créée avec succès.',
            'zone' => $zone,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service, ServiceZone $serviceZone): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceZone->service_id !== $service->id) {
            abort(403);
        }

        return response()->json([
            'zone' => $serviceZone,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service, ServiceZone $serviceZone): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceZone->service_id !== $service->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['sometimes', 'in:radius,postal_codes,custom'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'radius_km' => ['nullable', 'integer', 'min:1', 'max:200'],
            'postal_codes' => ['nullable', 'array'],
            'postal_codes.*' => ['string', 'regex:/^[0-9]{5}$/'],
            'excluded_areas' => ['nullable', 'array'],
            'travel_cost_per_km' => ['nullable', 'numeric', 'min:0'],
            'min_travel_time' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        $serviceZone->update($validated);

        return response()->json([
            'message' => 'Zone d\'intervention mise à jour avec succès.',
            'zone' => $serviceZone->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service, ServiceZone $serviceZone): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceZone->service_id !== $service->id) {
            abort(403);
        }

        $serviceZone->delete();

        return response()->json([
            'message' => 'Zone d\'intervention supprimée avec succès.',
        ]);
    }

    /**
     * Toggle zone status.
     */
    public function toggleStatus(Service $service, ServiceZone $serviceZone): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceZone->service_id !== $service->id) {
            abort(403);
        }

        $serviceZone->update([
            'is_active' => ! $serviceZone->is_active,
        ]);

        $status = $serviceZone->is_active ? 'activée' : 'désactivée';

        return response()->json([
            'message' => "Zone d'intervention {$status} avec succès.",
            'zone' => $serviceZone,
        ]);
    }

    /**
     * Check if coordinates are within zone.
     */
    public function checkLocation(Request $request, Service $service, ServiceZone $serviceZone): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceZone->service_id !== $service->id) {
            abort(403);
        }

        $validated = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $isWithinZone = $serviceZone->isWithinZone(
            $validated['latitude'],
            $validated['longitude']
        );

        $travelCost = null;
        if ($isWithinZone && $serviceZone->travel_cost_per_km) {
            // Calculer la distance pour le coût de déplacement
            $distance = $serviceZone->calculateDistance(
                $validated['latitude'],
                $validated['longitude'],
                $serviceZone->latitude,
                $serviceZone->longitude
            );
            $travelCost = $serviceZone->calculateTravelCost($distance);
        }

        return response()->json([
            'is_within_zone' => $isWithinZone,
            'travel_cost' => $travelCost,
            'zone' => $serviceZone,
        ]);
    }
}
