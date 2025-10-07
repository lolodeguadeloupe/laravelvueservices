<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServicePackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
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

        $packages = $service->packages()
            ->ordered()
            ->get();

        return response()->json([
            'packages' => $packages,
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
            'price' => ['required', 'numeric', 'min:0'],
            'sessions_count' => ['required', 'integer', 'min:1'],
            'validity_days' => ['nullable', 'integer', 'min:1'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'conditions' => ['nullable', 'array'],
        ]);

        $validated['service_id'] = $service->id;

        $package = ServicePackage::create($validated);

        return response()->json([
            'message' => 'Forfait créé avec succès.',
            'package' => $package,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service, ServicePackage $servicePackage): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $servicePackage->service_id !== $service->id) {
            abort(403);
        }

        return response()->json([
            'package' => $servicePackage,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service, ServicePackage $servicePackage): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $servicePackage->service_id !== $service->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'sessions_count' => ['sometimes', 'integer', 'min:1'],
            'validity_days' => ['nullable', 'integer', 'min:1'],
            'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'conditions' => ['nullable', 'array'],
        ]);

        $servicePackage->update($validated);

        return response()->json([
            'message' => 'Forfait mis à jour avec succès.',
            'package' => $servicePackage->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service, ServicePackage $servicePackage): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $servicePackage->service_id !== $service->id) {
            abort(403);
        }

        $servicePackage->delete();

        return response()->json([
            'message' => 'Forfait supprimé avec succès.',
        ]);
    }

    /**
     * Toggle package status.
     */
    public function toggleStatus(Service $service, ServicePackage $servicePackage): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $servicePackage->service_id !== $service->id) {
            abort(403);
        }

        $servicePackage->update([
            'is_active' => ! $servicePackage->is_active,
        ]);

        $status = $servicePackage->is_active ? 'activé' : 'désactivé';

        return response()->json([
            'message' => "Forfait {$status} avec succès.",
            'package' => $servicePackage,
        ]);
    }

    /**
     * Reorder packages.
     */
    public function reorder(Request $request, Service $service): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'packages' => ['required', 'array'],
            'packages.*.id' => ['required', 'exists:service_packages,id'],
            'packages.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['packages'] as $packageData) {
            ServicePackage::where('id', $packageData['id'])
                ->where('service_id', $service->id)
                ->update(['sort_order' => $packageData['sort_order']]);
        }

        return response()->json([
            'message' => 'Ordre des forfaits mis à jour avec succès.',
        ]);
    }
}
