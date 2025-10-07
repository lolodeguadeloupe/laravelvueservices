<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\ProviderRegistrationStep1Request;
use App\Http\Requests\Provider\ProviderRegistrationStep2Request;
use App\Http\Requests\Provider\ProviderRegistrationStep3Request;
use App\Models\Category;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\KycService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    protected KycService $kycService;

    public function __construct(KycService $kycService)
    {
        $this->kycService = $kycService;
    }

    /**
     * Afficher le formulaire d'inscription prestataire
     */
    public function show(): Response
    {
        $categories = Category::where('status', 'active')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'icon']);

        return Inertia::render('Provider/Registration/Index', [
            'categories' => $categories,
        ]);
    }

    /**
     * Étape 1: Informations personnelles de base
     */
    public function step1(ProviderRegistrationStep1Request $request): JsonResponse
    {
        $data = $request->validated();

        // Stocker temporairement en session
        session()->put('provider_registration.step1', $data);

        return response()->json([
            'message' => 'Étape 1 sauvegardée avec succès',
            'next_step' => 2,
        ]);
    }

    /**
     * Étape 2: Informations professionnelles
     */
    public function step2(ProviderRegistrationStep2Request $request): JsonResponse
    {
        $data = $request->validated();

        // Stocker temporairement en session
        session()->put('provider_registration.step2', $data);

        return response()->json([
            'message' => 'Étape 2 sauvegardée avec succès',
            'next_step' => 3,
        ]);
    }

    /**
     * Étape 3: Documents et finalisation
     */
    public function step3(ProviderRegistrationStep3Request $request): JsonResponse
    {
        $data = $request->validated();

        try {
            DB::beginTransaction();

            // Récupérer les données des étapes précédentes
            $step1Data = session('provider_registration.step1');
            $step2Data = session('provider_registration.step2');

            if (! $step1Data || ! $step2Data) {
                return response()->json([
                    'error' => 'Données d\'inscription incomplètes. Veuillez recommencer.',
                ], 400);
            }

            // Créer l'utilisateur
            $user = User::create([
                'name' => $step1Data['first_name'].' '.$step1Data['last_name'],
                'email' => $step1Data['email'],
                'password' => Hash::make($step1Data['password']),
                'user_type' => 'provider',
                'phone' => $step1Data['phone'],
                'is_active' => false, // Désactivé en attendant validation admin
                'verification_status' => 'pending',
            ]);

            // Uploader les documents
            $documents = [];
            if ($request->hasFile('identity_document')) {
                $documents['identity'] = $this->uploadDocument($request->file('identity_document'), 'identity', $user->id);
            }
            if ($request->hasFile('professional_license')) {
                $documents['license'] = $this->uploadDocument($request->file('professional_license'), 'license', $user->id);
            }
            if ($request->hasFile('insurance_certificate')) {
                $documents['insurance'] = $this->uploadDocument($request->file('insurance_certificate'), 'insurance', $user->id);
            }

            // Créer le profil avec les données KYC
            $profile = UserProfile::create([
                'user_id' => $user->id,
                'first_name' => $step1Data['first_name'],
                'last_name' => $step1Data['last_name'],
                'phone' => $step1Data['phone'],
                'date_of_birth' => $step1Data['date_of_birth'],
                'gender' => $step1Data['gender'],
                'bio' => $step2Data['bio'],
                'years_experience' => $step2Data['experience'],
                'business_name' => $data['business_name'],
                'business_type' => $data['business_type'],
                'siret_number' => $data['siret_number'] ?? null,
                'vat_number' => $data['vat_number'] ?? null,
                'business_address' => $step2Data['address'].', '.$step2Data['city'].' '.$step2Data['postal_code'],
                'business_phone' => $step1Data['phone'],
                'iban' => $data['iban'],
                'bic' => $data['bic'],
                'bank_account_holder' => $data['bank_account_holder'],
                'emergency_contact_name' => $data['emergency_contact_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],
                'emergency_contact_relation' => $data['emergency_contact_relation'],
                'specialties' => $step2Data['specialties'] ?? [],
                'intervention_zones' => $step2Data['intervention_zones'] ?? [],
                'availability_hours' => $step2Data['availability_hours'] ?? [],
                'languages' => $step2Data['languages'] ?? ['Français'],
                'hourly_rate_min' => $step2Data['hourly_rate_min'] ?? null,
                'hourly_rate_max' => $step2Data['hourly_rate_max'] ?? null,
                'professional_description' => $step2Data['professional_description'] ?? null,
                'gdpr_consent' => $data['gdpr_consent'],
                'terms_accepted' => $data['terms_accepted'],
                'marketing_consent' => $data['marketing_consent'] ?? false,
                'consent_date' => now(),
                'kyc_status' => 'pending',
                'kyc_submitted_at' => now(),
            ]);

            // Traiter les documents KYC via le service
            $kycDocuments = [];
            $documentFields = [
                'identity_document' => 'identity_document_path',
                'business_registration' => 'business_registration_path',
                'insurance_certificate' => 'insurance_certificate_path',
                'professional_certification' => 'professional_certification_path',
                'bank_statement' => 'bank_statement_path',
            ];

            foreach ($documentFields as $requestField => $profileField) {
                if ($request->hasFile($requestField)) {
                    $kycDocuments[$profileField] = $request->file($requestField);
                }
            }

            if (! empty($kycDocuments)) {
                $this->kycService->submitKycDocuments($user, $kycDocuments);
            }

            // Nettoyer la session
            session()->forget('provider_registration');

            DB::commit();

            // Envoyer email de confirmation et notification admin
            $this->sendWelcomeEmail($user);
            $this->notifyAdminNewProvider($user);

            return response()->json([
                'message' => 'Inscription soumise avec succès ! Votre compte sera activé après validation par notre équipe.',
                'user_id' => $user->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'error' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.',
                'details' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Récupérer les données de progression de l'inscription
     */
    public function getProgress(): JsonResponse
    {
        return response()->json([
            'step1' => session('provider_registration.step1'),
            'step2' => session('provider_registration.step2'),
            'current_step' => $this->getCurrentStep(),
        ]);
    }

    /**
     * Redémarrer l'inscription (nettoyer la session)
     */
    public function restart(): JsonResponse
    {
        session()->forget('provider_registration');

        return response()->json([
            'message' => 'Inscription redémarrée',
        ]);
    }

    /**
     * Uploader un document
     */
    private function uploadDocument($file, $type, $userId): string
    {
        $filename = $type.'_'.$userId.'_'.time().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs('provider-documents/'.$userId, $filename, 'private');

        return $path;
    }

    /**
     * Déterminer l'étape actuelle basée sur les données en session
     */
    private function getCurrentStep(): int
    {
        if (! session('provider_registration.step1')) {
            return 1;
        }
        if (! session('provider_registration.step2')) {
            return 2;
        }

        return 3;
    }

    /**
     * Envoyer l'email de bienvenue
     */
    private function sendWelcomeEmail(User $user): void
    {
        // TODO: Implémenter l'envoi d'email avec Laravel Mail
        // Mail::to($user->email)->send(new ProviderWelcomeMail($user));
    }

    /**
     * Notifier les admins d'un nouveau prestataire en attente
     */
    private function notifyAdminNewProvider(User $user): void
    {
        // TODO: Implémenter la notification admin
        // Notification::send(User::admins()->get(), new NewProviderRegistration($user));
    }
}
