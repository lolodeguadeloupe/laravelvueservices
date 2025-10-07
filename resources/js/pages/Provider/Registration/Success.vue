<template>
  <AppLayout title="Inscription Réussie">
    <div class="py-12">
      <div class="mx-auto max-w-2xl">
        <div class="text-center">
          <!-- Icône de succès -->
          <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-green-100">
            <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>

          <!-- Titre et message -->
          <h1 class="mt-6 text-3xl font-bold text-gray-900">
            Inscription soumise avec succès !
          </h1>
          
          <p class="mt-4 text-lg text-gray-600">
            Merci d'avoir rejoint notre réseau de prestataires de services.
          </p>

          <!-- Étapes suivantes -->
          <div class="mt-8 bg-gray-50 rounded-lg p-6 text-left">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Que se passe-t-il maintenant ?</h2>
            
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white text-sm font-medium">
                    1
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="font-medium text-gray-900">Vérification de vos documents</h3>
                  <p class="text-sm text-gray-600">
                    Notre équipe va examiner votre dossier et vérifier les documents fournis.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white text-sm font-medium">
                    2
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="font-medium text-gray-900">Notification par email</h3>
                  <p class="text-sm text-gray-600">
                    Vous recevrez un email de confirmation sous 48 heures maximum.
                  </p>
                </div>
              </div>

              <div class="flex items-start">
                <div class="flex-shrink-0">
                  <div class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-white text-sm font-medium">
                    3
                  </div>
                </div>
                <div class="ml-4">
                  <h3 class="font-medium text-gray-900">Accès à votre tableau de bord</h3>
                  <p class="text-sm text-gray-600">
                    Une fois validé, vous pourrez accéder à votre espace prestataire et commencer à recevoir des demandes.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Informations de contact -->
          <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-center">
              <svg class="h-5 w-5 text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <div class="text-left">
                <h3 class="text-sm font-medium text-blue-800">
                  Une question ?
                </h3>
                <p class="text-sm text-blue-700">
                  N'hésitez pas à nous contacter à 
                  <a href="mailto:support@notre-plateforme.fr" class="font-medium underline">
                    support@notre-plateforme.fr
                  </a>
                </p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="mt-8 flex justify-center space-x-4">
            <Link
              :href="home.url()"
              class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              Retour à l'accueil
            </Link>
            
            <a
              href="mailto:support@notre-plateforme.fr?subject=Question sur mon inscription prestataire"
              class="inline-flex items-center justify-center rounded-md bg-primary px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
            >
              Nous contacter
            </a>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { home } from '@/routes'
</script>