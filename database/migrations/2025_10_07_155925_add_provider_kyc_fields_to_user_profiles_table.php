<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Informations KYC pour les prestataires
            $table->string('business_name')->nullable();
            $table->string('business_type')->nullable(); // 'individual', 'company', 'auto_entrepreneur'
            $table->string('siret_number', 14)->nullable();
            $table->string('vat_number')->nullable();
            $table->text('business_address')->nullable();
            $table->string('business_phone')->nullable();

            // Documents KYC
            $table->string('identity_document_path')->nullable();
            $table->string('business_registration_path')->nullable();
            $table->string('insurance_certificate_path')->nullable();
            $table->string('professional_certification_path')->nullable();
            $table->string('bank_statement_path')->nullable();

            // Informations bancaires
            $table->string('iban', 34)->nullable();
            $table->string('bic', 11)->nullable();
            $table->string('bank_account_holder')->nullable();

            // Contact d'urgence
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();

            // Statut de validation KYC
            $table->enum('kyc_status', ['pending', 'under_review', 'approved', 'rejected'])->default('pending');
            $table->text('kyc_rejection_reason')->nullable();
            $table->timestamp('kyc_submitted_at')->nullable();
            $table->timestamp('kyc_reviewed_at')->nullable();
            $table->unsignedBigInteger('kyc_reviewed_by')->nullable();

            // Consentements
            $table->boolean('gdpr_consent')->default(false);
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('marketing_consent')->default(false);
            $table->timestamp('consent_date')->nullable();

            // Spécialités et zones d'intervention
            $table->json('specialties')->nullable();
            $table->json('intervention_zones')->nullable();
            $table->json('availability_hours')->nullable();
            $table->decimal('hourly_rate_min', 8, 2)->nullable();
            $table->decimal('hourly_rate_max', 8, 2)->nullable();
            $table->text('professional_description')->nullable();
            $table->integer('years_experience')->nullable();

            // Index pour optimiser les recherches
            $table->index('kyc_status');
            $table->index('business_type');

            // Clé étrangère pour qui a validé le KYC
            $table->foreign('kyc_reviewed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            // Supprimer les clés étrangères en premier
            $table->dropForeign(['kyc_reviewed_by']);

            // Supprimer les index
            $table->dropIndex(['kyc_status']);
            $table->dropIndex(['business_type']);

            // Supprimer toutes les colonnes KYC
            $table->dropColumn([
                'business_name',
                'business_type',
                'siret_number',
                'vat_number',
                'business_address',
                'business_phone',
                'identity_document_path',
                'business_registration_path',
                'insurance_certificate_path',
                'professional_certification_path',
                'bank_statement_path',
                'iban',
                'bic',
                'bank_account_holder',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relation',
                'kyc_status',
                'kyc_rejection_reason',
                'kyc_submitted_at',
                'kyc_reviewed_at',
                'kyc_reviewed_by',
                'gdpr_consent',
                'terms_accepted',
                'marketing_consent',
                'consent_date',
                'specialties',
                'intervention_zones',
                'availability_hours',
                'hourly_rate_min',
                'hourly_rate_max',
                'professional_description',
                'years_experience',
            ]);
        });
    }
};
