<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ServiceMediaController extends Controller
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

        $query = $service->media()->ordered();

        // Filtrer par type si spécifié
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filtrer par visibilité
        if ($request->filled('visibility')) {
            $isPublic = $request->input('visibility') === 'public';
            $query->where('is_public', $isPublic);
        }

        $media = $query->get();

        return response()->json([
            'media' => $media,
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
            'files' => ['required', 'array', 'max:10'],
            'files.*' => [
                'required',
                File::types(['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mov', 'avi', 'pdf', 'doc', 'docx'])
                    ->max(10 * 1024), // 10MB max
            ],
            'alt_text' => ['nullable', 'array'],
            'alt_text.*' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
            'is_public' => ['boolean'],
        ]);

        $uploadedMedia = [];

        foreach ($validated['files'] as $index => $file) {
            // Déterminer le type de média
            $mimeType = $file->getMimeType();
            $type = $this->determineMediaType($mimeType);

            // Générer un nom de fichier unique
            $fileName = time().'_'.$index.'_'.$file->getClientOriginalName();
            $filePath = $file->storeAs('services/media', $fileName, 'public');

            // Extraire les métadonnées
            $metadata = $this->extractMetadata($file, $type);

            // Créer l'enregistrement de média
            $media = ServiceMedia::create([
                'service_id' => $service->id,
                'type' => $type,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $filePath,
                'mime_type' => $mimeType,
                'file_size' => $file->getSize(),
                'alt_text' => $validated['alt_text'][$index] ?? null,
                'description' => $validated['description'][$index] ?? null,
                'is_primary' => false,
                'is_public' => $validated['is_public'] ?? true,
                'sort_order' => $service->media()->count() + $index,
                'metadata' => $metadata,
            ]);

            $uploadedMedia[] = $media;
        }

        return response()->json([
            'message' => count($uploadedMedia).' fichier(s) uploadé(s) avec succès.',
            'media' => $uploadedMedia,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service, ServiceMedia $serviceMedia): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceMedia->service_id !== $service->id) {
            abort(403);
        }

        return response()->json([
            'media' => $serviceMedia,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service, ServiceMedia $serviceMedia): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceMedia->service_id !== $service->id) {
            abort(403);
        }

        $validated = $request->validate([
            'alt_text' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_public' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $serviceMedia->update($validated);

        return response()->json([
            'message' => 'Média mis à jour avec succès.',
            'media' => $serviceMedia->fresh(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service, ServiceMedia $serviceMedia): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceMedia->service_id !== $service->id) {
            abort(403);
        }

        // Supprimer le fichier du stockage
        Storage::disk('public')->delete($serviceMedia->file_path);

        // Supprimer l'enregistrement
        $serviceMedia->delete();

        return response()->json([
            'message' => 'Média supprimé avec succès.',
        ]);
    }

    /**
     * Set media as primary.
     */
    public function setPrimary(Service $service, ServiceMedia $serviceMedia): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id() || $serviceMedia->service_id !== $service->id) {
            abort(403);
        }

        // Retirer le statut primaire de tous les autres médias
        $service->media()->update(['is_primary' => false]);

        // Définir ce média comme primaire
        $serviceMedia->update(['is_primary' => true]);

        return response()->json([
            'message' => 'Média défini comme principal avec succès.',
            'media' => $serviceMedia->fresh(),
        ]);
    }

    /**
     * Reorder media.
     */
    public function reorder(Request $request, Service $service): JsonResponse
    {
        // Vérifier que le service appartient au prestataire
        if ($service->provider_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'media' => ['required', 'array'],
            'media.*.id' => ['required', 'exists:service_media,id'],
            'media.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        foreach ($validated['media'] as $mediaData) {
            ServiceMedia::where('id', $mediaData['id'])
                ->where('service_id', $service->id)
                ->update(['sort_order' => $mediaData['sort_order']]);
        }

        return response()->json([
            'message' => 'Ordre des médias mis à jour avec succès.',
        ]);
    }

    /**
     * Determine media type from MIME type.
     */
    private function determineMediaType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if (str_starts_with($mimeType, 'video/')) {
            return 'video';
        }

        return 'document';
    }

    /**
     * Extract metadata from uploaded file.
     */
    private function extractMetadata($file, string $type): array
    {
        $metadata = [];

        if ($type === 'image') {
            $imagePath = $file->getPathname();
            $imageSize = getimagesize($imagePath);

            if ($imageSize) {
                $metadata['width'] = $imageSize[0];
                $metadata['height'] = $imageSize[1];
            }
        }

        // Pour les vidéos, on pourrait utiliser FFmpeg pour extraire la durée
        // Mais cela nécessiterait des dépendances supplémentaires

        return $metadata;
    }
}
