<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Service;
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
            ->with(['category', 'provider.profile', 'media', 'reviews'])
            ->where('is_active', true)
            ->whereHas('provider', function ($q) {
                $q->where('verification_status', 'verified');
            })
            ->withAvg('reviews', 'overall_rating')
            ->withCount('reviews')
            ->withCount('bookingRequests');

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
            $query->where('price_min', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price_max', '<=', $request->price_max);
        }

        // Tri
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price_min', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price_max', 'desc');
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating')
                    ->orderByDesc('reviews_count');
                break;
            case 'popular':
                $query->orderByDesc('booking_requests_count');
                break;
            default: // recent
                $query->orderBy('created_at', 'desc');
                break;
        }

        $services = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get();

        // Catégories principales avec leurs couleurs et icônes
        $featuredCategories = [
            [
                'name' => 'Ménage',
                'subtitle' => 'et nettoyage',
                'cssClass' => 'service-menage',
                'icon' => '🧽',
                'slug' => 'menage-nettoyage',
            ],
            [
                'name' => 'Garde d\'enfants',
                'subtitle' => 'et babysitting',
                'cssClass' => 'service-garde-enfants',
                'icon' => '👶',
                'slug' => 'garde-enfants',
            ],
            [
                'name' => 'Coiffure',
                'subtitle' => 'à domicile',
                'cssClass' => 'service-coiffure',
                'icon' => '✂️',
                'slug' => 'coiffure',
            ],
            [
                'name' => 'Beauté',
                'subtitle' => 'et soins',
                'cssClass' => 'service-beaute',
                'icon' => '💅',
                'slug' => 'beaute-soins',
            ],
            [
                'name' => 'Massage',
                'subtitle' => 'et bien-être',
                'cssClass' => 'service-massage',
                'icon' => '💆',
                'slug' => 'massage-bien-etre',
            ],
            [
                'name' => 'Coach sportif',
                'subtitle' => 'à domicile',
                'cssClass' => 'service-coach-sportif',
                'icon' => '🏋️',
                'slug' => 'coaching-sportif',
            ],
        ];

        return Inertia::render('Services/Index', [
            'services' => $services,
            'categories' => $categories,
            'featuredCategories' => $featuredCategories,
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
        if (! $service->is_active || $service->provider->verification_status !== 'verified') {
            abort(404);
        }

        $service->load([
            'category',
            'provider.profile',
            'media',
        ]);

        // Services similaires
        $similarServices = Service::query()
            ->with(['category', 'provider.profile', 'media'])
            ->where('is_active', true)
            ->whereHas('provider', function ($q) {
                $q->where('verification_status', 'verified');
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
