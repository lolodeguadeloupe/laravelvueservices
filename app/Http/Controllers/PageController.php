<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class PageController extends Controller
{
    /**
     * Display the about page.
     */
    public function about()
    {
        return Inertia::render('Pages/About');
    }

    /**
     * Display the how it works page.
     */
    public function howItWorks()
    {
        return Inertia::render('Pages/HowItWorks');
    }

    /**
     * Display the contact page.
     */
    public function contact()
    {
        return Inertia::render('Pages/Contact');
    }

    /**
     * Display the help center page.
     */
    public function help()
    {
        return Inertia::render('Pages/Help');
    }

    /**
     * Display the terms of service page.
     */
    public function terms()
    {
        return Inertia::render('Pages/Terms');
    }

    /**
     * Display the privacy policy page.
     */
    public function privacy()
    {
        return Inertia::render('Pages/Privacy');
    }
}