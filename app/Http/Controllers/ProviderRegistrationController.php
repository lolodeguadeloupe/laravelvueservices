<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProviderRegistrationStep1Request;
use App\Http\Requests\ProviderRegistrationStep2Request;
use App\Http\Requests\ProviderRegistrationStep3Request;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ProviderRegistrationController extends Controller
{
    /**
     * Afficher le formulaire d'inscription prestataire - Étape 1
     */
    public function showStep1()
    {
        return Inertia::render('Provider/Registration/Step1');
    }

    /**
     * Traiter l'étape 1 - Informations personnelles
     */
    public function processStep1(ProviderRegistrationStep1Request $request)
    {
        $validated = $request->validated();

        // Stocker les données en session pour l'étape suivante
        $request->session()->put('provider_registration.step1', $validated);

        return redirect()->route('provider.registration.step2');
    }

    /**
     * Afficher le formulaire d'inscription prestataire - Étape 2
     */
    public function showStep2()
    {
        if (! session()->has('provider_registration.step1')) {
            return redirect()->route('provider.registration.step1')
                ->with('error', 'Veuillez commencer par l\'étape 1.');
        }

        return Inertia::render('Provider/Registration/Step2');
    }

    /**
     * Traiter l'étape 2 - Informations professionnelles
     */
    public function processStep2(ProviderRegistrationStep2Request $request)
    {
        $validated = $request->validated();

        $request->session()->put('provider_registration.step2', $validated);

        return redirect()->route('provider.registration.step3');
    }

    /**
     * Afficher le formulaire d'inscription prestataire - Étape 3
     */
    public function showStep3()
    {
        if (! session()->has('provider_registration.step2')) {
            return redirect()->route('provider.registration.step1')
                ->with('error', 'Veuillez compléter toutes les étapes précédentes.');
        }

        return Inertia::render('Provider/Registration/Step3');
    }

    /**
     * Traiter l'étape 3 - Documents et finalisation
     */
    public function processStep3(ProviderRegistrationStep3Request $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Récupérer toutes les données des étapes précédentes
            $step1Data = session('provider_registration.step1');
            $step2Data = session('provider_registration.step2');

            // Créer l'utilisateur
            $user = User::create([
                'name' => $step1Data['first_name'].' '.$step1Data['last_name'],
                'email' => $step1Data['email'],
                'password' => bcrypt($validated['password']),
                'user_type' => 'provider',
                'phone' => $step1Data['phone'],
                'address' => $step1Data['address'],
                'verification_status' => 'pending',
                'is_active' => false, // Désactivé jusqu'à validation admin
            ]);

            // Assigner le rôle prestataire
            $user->assignRole('provider');

            // Upload des documents
            $documentsPath = [];
            if ($request->hasFile('identity_document')) {
                $documentsPath['identity_document'] = $request->file('identity_document')
                    ->store('provider-documents/'.$user->id, 'private');
            }

            if ($request->hasFile('professional_insurance')) {
                $documentsPath['professional_insurance'] = $request->file('professional_insurance')
                    ->store('provider-documents/'.$user->id, 'private');
            }

            // Upload des certifications
            if ($request->hasFile('certifications_files')) {
                $certificationFiles = [];
                foreach ($request->file('certifications_files') as $file) {
                    $certificationFiles[] = $file->store('provider-documents/'.$user->id.'/certifications', 'private');
                }
                $documentsPath['certifications_files'] = $certificationFiles;
            }

            // Créer le profil utilisateur
            UserProfile::create([
                'user_id' => $user->id,
                'first_name' => $step1Data['first_name'],
                'last_name' => $step1Data['last_name'],
                'bio' => $step2Data['bio'],
                'experience' => $step2Data['experience'],
                'certifications' => $step2Data['certifications'] ?? [],
                'date_of_birth' => $step1Data['date_of_birth'],
                'company_name' => $step2Data['company_name'],
                'documents' => $documentsPath,
            ]);

            DB::commit();

            // Nettoyer la session
            $request->session()->forget('provider_registration');

            return redirect()->route('provider.registration.success')
                ->with('success', 'Votre inscription a été soumise avec succès. Elle sera examinée par notre équipe.');

        } catch (\Exception $e) {
            DB::rollback();

            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.']);
        }
    }

    /**
     * Afficher la page de confirmation d'inscription
     */
    public function success()
    {
        return Inertia::render('Provider/Registration/Success');
    }

    /**
     * Afficher la liste des prestataires en attente (Admin)
     */
    public function pendingProviders()
    {
        $this->authorize('verify providers');

        $pendingProviders = User::with('profile')
            ->where('user_type', 'provider')
            ->where('verification_status', 'pending')
            ->paginate(20);

        return Inertia::render('Admin/Providers/Pending', [
            'providers' => $pendingProviders,
        ]);
    }

    /**
     * Approuver un prestataire (Admin)
     */
    public function approve(User $provider)
    {
        $this->authorize('verify providers');

        $provider->update([
            'verification_status' => 'verified',
            'is_active' => true,
        ]);

        return back()->with('success', 'Prestataire approuvé avec succès.');
    }

    /**
     * Rejeter un prestataire (Admin)
     */
    public function reject(User $provider, Request $request)
    {
        $this->authorize('verify providers');

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $provider->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Prestataire rejeté.');
    }
}
