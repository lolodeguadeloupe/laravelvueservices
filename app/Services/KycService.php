<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KycService
{
    public function submitKycDocuments(User $user, array $documents): bool
    {
        try {
            $profile = $user->profile ?? $user->profile()->create([]);
            $documentPaths = [];

            // Upload des documents
            foreach ($documents as $field => $file) {
                if ($file instanceof UploadedFile) {
                    $documentPaths[$field] = $this->uploadDocument($file, $user->id, $field);
                }
            }

            // Mise à jour du profil avec les chemins des documents
            $profile->update(array_merge($documentPaths, [
                'kyc_status' => 'under_review',
                'kyc_submitted_at' => now(),
            ]));

            return true;
        } catch (\Exception $e) {
            \Log::error('KYC submission failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function approveKyc(User $user, User $reviewer): bool
    {
        try {
            $user->profile->update([
                'kyc_status' => 'approved',
                'kyc_reviewed_at' => now(),
                'kyc_reviewed_by' => $reviewer->id,
                'kyc_rejection_reason' => null,
            ]);

            // Activer le compte prestataire
            $user->update(['is_verified' => true]);

            // TODO: Envoyer notification d'approbation

            return true;
        } catch (\Exception $e) {
            \Log::error('KYC approval failed', [
                'user_id' => $user->id,
                'reviewer_id' => $reviewer->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function rejectKyc(User $user, User $reviewer, string $reason): bool
    {
        try {
            $user->profile->update([
                'kyc_status' => 'rejected',
                'kyc_reviewed_at' => now(),
                'kyc_reviewed_by' => $reviewer->id,
                'kyc_rejection_reason' => $reason,
            ]);

            // TODO: Envoyer notification de rejet avec les raisons

            return true;
        } catch (\Exception $e) {
            \Log::error('KYC rejection failed', [
                'user_id' => $user->id,
                'reviewer_id' => $reviewer->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function getKycStatus(User $user): string
    {
        return $user->profile?->kyc_status ?? 'pending';
    }

    public function canProvideServices(User $user): bool
    {
        return $user->user_type === 'provider' &&
               $user->is_verified &&
               $this->getKycStatus($user) === 'approved';
    }

    public function getPendingKycRequests()
    {
        return User::whereHas('profile', function ($query) {
            $query->where('kyc_status', 'under_review');
        })
            ->with(['profile'])
            ->where('user_type', 'provider')
            ->get();
    }

    public function getKycDocuments(User $user): array
    {
        $profile = $user->profile;
        if (! $profile) {
            return [];
        }

        $documents = [];
        $documentFields = [
            'identity_document_path' => 'Pièce d\'identité',
            'business_registration_path' => 'Extrait Kbis/Auto-entrepreneur',
            'insurance_certificate_path' => 'Assurance professionnelle',
            'professional_certification_path' => 'Certifications professionnelles',
            'bank_statement_path' => 'RIB',
        ];

        foreach ($documentFields as $field => $label) {
            if ($profile->$field) {
                $documents[] = [
                    'field' => $field,
                    'label' => $label,
                    'path' => $profile->$field,
                    'url' => Storage::url($profile->$field),
                    'exists' => Storage::exists($profile->$field),
                ];
            }
        }

        return $documents;
    }

    private function uploadDocument(UploadedFile $file, int $userId, string $field): string
    {
        // Générer un nom unique pour le fichier
        $filename = Str::uuid().'_'.time().'.'.$file->getClientOriginalExtension();

        // Stocker dans un dossier sécurisé par utilisateur
        $path = "kyc/{$userId}/{$field}";

        return $file->storeAs($path, $filename, 'private');
    }

    public function deleteDocument(User $user, string $field): bool
    {
        try {
            $profile = $user->profile;
            if (! $profile || ! $profile->$field) {
                return false;
            }

            // Supprimer le fichier du stockage
            Storage::disk('private')->delete($profile->$field);

            // Mettre à jour le profil
            $profile->update([$field => null]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Document deletion failed', [
                'user_id' => $user->id,
                'field' => $field,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function validateBusinessData(array $data): array
    {
        $errors = [];

        // Validation SIRET
        if (isset($data['siret_number']) && $data['siret_number']) {
            if (! $this->isValidSiret($data['siret_number'])) {
                $errors['siret_number'] = 'Le numéro SIRET n\'est pas valide';
            }
        }

        // Validation IBAN
        if (isset($data['iban']) && $data['iban']) {
            if (! $this->isValidIban($data['iban'])) {
                $errors['iban'] = 'L\'IBAN n\'est pas valide';
            }
        }

        return $errors;
    }

    private function isValidSiret(string $siret): bool
    {
        // Supprimer les espaces
        $siret = str_replace(' ', '', $siret);

        // Vérifier la longueur
        if (strlen($siret) !== 14) {
            return false;
        }

        // Vérifier que ce sont bien des chiffres
        if (! ctype_digit($siret)) {
            return false;
        }

        // Algorithme de validation SIRET (algorithme de Luhn)
        $sum = 0;
        for ($i = 0; $i < 14; $i++) {
            $digit = (int) $siret[$i];
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = $digit % 10 + intval($digit / 10);
                }
            }
            $sum += $digit;
        }

        return $sum % 10 === 0;
    }

    private function isValidIban(string $iban): bool
    {
        // Supprimer les espaces et convertir en majuscules
        $iban = strtoupper(str_replace(' ', '', $iban));

        // Vérifier la longueur (entre 15 et 34 caractères)
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }

        // Déplacer les 4 premiers caractères à la fin
        $rearranged = substr($iban, 4).substr($iban, 0, 4);

        // Remplacer les lettres par des chiffres (A=10, B=11, etc.)
        $numeric = '';
        for ($i = 0; $i < strlen($rearranged); $i++) {
            $char = $rearranged[$i];
            if (ctype_alpha($char)) {
                $numeric .= ord($char) - ord('A') + 10;
            } else {
                $numeric .= $char;
            }
        }

        // Calculer le modulo 97
        return bcmod($numeric, '97') === '1';
    }
}
