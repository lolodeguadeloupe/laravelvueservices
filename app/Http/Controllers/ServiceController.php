<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        $this->middleware('role:provider');
    }

    /**
     * Display a listing of the provider's services.
     */
    public function index(Request $request)
    {
        $provider = Auth::user();

        $query = $provider->providedServices()
            ->with(['category', 'bookingRequests'])
            ->withCount('bookingRequests');

        // Filtres
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%')
                  ->orWhere('description', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $services = $query->latest()->paginate(12);

        $categories = Category::all();

        return Inertia::render('Provider/Services/Index', [
            'services' => $services,
            'categories' => $categories,
            'filters' => [
                'search' => $request->search ?? '',
                'category' => $request->category ?? '',
                'status' => $request->status ?? '',
            ],
        ]);
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Provider/Services/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created service in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,hourly,custom',
            'duration' => 'nullable|integer|min:1',
            'location_type' => 'required|in:client_location,provider_location,both',
            'service_area' => 'nullable|json',
            'requirements' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'is_active' => 'boolean',
        ]);

        $service = Auth::user()->providedServices()->create([
            ...$validated,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Upload des images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('services/'.$service->id, 'public');
                $images[] = $path;
            }
            $service->update(['images' => $images]);
        }

        return redirect()->route('provider.services.index')
            ->with('success', 'Service créé avec succès.');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $this->authorize('view', $service);

        $service->load(['category', 'provider.profile', 'bookingRequests']);

        return Inertia::render('Provider/Services/Show', [
            'service' => $service,
        ]);
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $this->authorize('update', $service);

        $categories = Category::orderBy('name')->get();

        return Inertia::render('Provider/Services/Edit', [
            'service' => $service,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified service in storage.
     */
    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'price_type' => 'required|in:fixed,hourly,custom',
            'duration' => 'nullable|integer|min:1',
            'location_type' => 'required|in:client_location,provider_location,both',
            'service_area' => 'nullable|json',
            'requirements' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'existing_images' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        // Gestion des images existantes
        $currentImages = $service->images ?? [];
        $keptImages = $validated['existing_images'] ?? [];

        // Supprimer les images non conservées
        $imagesToDelete = array_diff($currentImages, $keptImages);
        foreach ($imagesToDelete as $image) {
            Storage::disk('public')->delete($image);
        }

        // Upload de nouvelles images
        $newImages = $keptImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('services/'.$service->id, 'public');
                $newImages[] = $path;
            }
        }

        $service->update([
            ...$validated,
            'images' => $newImages,
        ]);

        return redirect()->route('provider.services.index')
            ->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);

        // Vérifier qu'il n'y a pas de réservations en cours
        if ($service->bookingRequests()->whereIn('status', ['pending', 'accepted', 'in_progress'])->exists()) {
            return back()->withErrors([
                'error' => 'Impossible de supprimer un service avec des réservations en cours.'
            ]);
        }

        // Supprimer les images
        if ($service->images) {
            foreach ($service->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $service->delete();

        return redirect()->route('provider.services.index')
            ->with('success', 'Service supprimé avec succès.');
    }

    /**
     * Toggle the active status of a service.
     */
    public function toggleStatus(Service $service)
    {
        $this->authorize('update', $service);

        $service->update([
            'is_active' => !$service->is_active,
        ]);

        $status = $service->is_active ? 'activé' : 'désactivé';

        return back()->with('success', "Service {$status} avec succès.");
    }
}
