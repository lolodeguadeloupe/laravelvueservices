<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicServiceController extends Controller
{
    /**
     * Display a listing of active services.
     */
    public function index(Request $request)
    {
        $query = Service::query()
            ->with(['category', 'provider.profile', 'images'])
            ->where('is_active', true)
            ->whereHas('provider', function ($q) {
                $q->where('is_approved', true);
            })
            ->orderBy('created_at', 'desc');

        // Filtres
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('location')) {
            $query->whereHas('provider.profile', function ($q) use ($request) {
                $q->where('city', 'like', '%'.$request->location.'%')
                  ->orWhere('postal_code', 'like', '%'.$request->location.'%');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                  ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        $services = $query->paginate(12);
        $categories = Category::orderBy('name')->get();

        return Inertia::render('Services/Index', [
            'services' => $services,
            'categories' => $categories,
            'filters' => [
                'search' => $request->search ?? '',
                'category' => $request->category ?? '',
                'location' => $request->location ?? '',
                'price_min' => $request->price_min ?? '',
                'price_max' => $request->price_max ?? '',
            ],
        ]);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        // Vérifier que le service est actif et que le prestataire est approuvé
        if (!$service->is_active || !$service->provider->is_approved) {
            abort(404);
        }

        $service->load([
            'category',
            'provider.profile',
            'images'
        ]);

        // Services similaires
        $similarServices = Service::query()
            ->with(['category', 'provider.profile', 'images'])
            ->where('is_active', true)
            ->whereHas('provider', function ($q) {
                $q->where('is_approved', true);
            })
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->limit(4)
            ->get();

        return Inertia::render('Services/Show', [
            'service' => $service,
            'similarServices' => $similarServices,
        ]);
    }
}