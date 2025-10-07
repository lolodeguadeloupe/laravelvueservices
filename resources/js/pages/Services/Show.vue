<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50">
      <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Navigation -->
        <div class="mb-6">
          <Link :href="route('services.index')" class="inline-flex items-center text-sm text-gray-500 hover:text-primary">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Retour aux services
          </Link>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Contenu principal -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Images -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
              <div class="aspect-w-16 aspect-h-9">
                <img
                  :src="service.images?.[0]?.url || '/default-service.jpg'"
                  :alt="service.title"
                  class="w-full h-80 object-cover"
                />
              </div>
              
              <!-- Galerie miniatures -->
              <div v-if="service.images && service.images.length > 1" class="p-4">
                <div class="flex space-x-2 overflow-x-auto">
                  <button
                    v-for="(image, index) in service.images.slice(1, 5)"
                    :key="index"
                    class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border border-gray-200 hover:border-primary transition-colors"
                  >
                    <img
                      :src="image.url"
                      :alt="`${service.title} - Image ${index + 2}`"
                      class="w-full h-full object-cover"
                    />
                  </button>
                  <div
                    v-if="service.images.length > 5"
                    class="flex-shrink-0 w-20 h-20 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-sm text-gray-500"
                  >
                    +{{ service.images.length - 4 }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Détails du service -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="flex items-start justify-between mb-4">
                <div>
                  <span class="inline-block bg-primary/10 text-primary text-sm font-medium px-3 py-1 rounded-full mb-3">
                    {{ service.category.name }}
                  </span>
                  <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ service.title }}</h1>
                </div>
              </div>
              
              <div class="prose prose-gray max-w-none">
                <p class="text-gray-700 text-lg leading-relaxed">{{ service.description }}</p>
              </div>
              
              <!-- Détails supplémentaires -->
              <div v-if="service.requirements || service.duration_estimate" class="mt-8 border-t border-gray-100 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations complémentaires</h3>
                <div class="space-y-3">
                  <div v-if="service.duration_estimate" class="flex items-center">
                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-gray-700">Durée estimée : {{ service.duration_estimate }}</span>
                  </div>
                  
                  <div v-if="service.requirements">
                    <div class="flex items-start">
                      <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                      </svg>
                      <div>
                        <p class="text-gray-700 font-medium mb-1">Prérequis</p>
                        <p class="text-gray-600 text-sm">{{ service.requirements }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Carte de réservation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
              <div class="text-center mb-6">
                <p class="text-sm text-gray-500 mb-1">À partir de</p>
                <p class="text-4xl font-bold text-primary">{{ service.price }}€</p>
              </div>
              
              <div class="space-y-4">
                <Link
                  v-if="$page.props.auth.user && $page.props.auth.user.hasRole && $page.props.auth.user.hasRole('client')"
                  :href="route('bookings.create', service.id)"
                  class="w-full bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-dark transition-colors text-center block"
                >
                  Faire une demande
                </Link>
                
                <Link
                  v-else-if="$page.props.auth.user"
                  :href="route('login')"
                  class="w-full bg-gray-100 text-gray-500 py-3 px-6 rounded-lg font-semibold text-center block cursor-not-allowed"
                >
                  Réservé aux clients
                </Link>
                
                <Link
                  v-else
                  :href="route('login')"
                  class="w-full bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-primary-dark transition-colors text-center block"
                >
                  Se connecter pour réserver
                </Link>
                
                <p class="text-xs text-gray-500 text-center">
                  Aucun frais jusqu'à la confirmation du prestataire
                </p>
              </div>
            </div>

            <!-- Prestataire -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Prestataire</h3>
              
              <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium mr-4">
                  {{ service.provider.profile?.first_name?.charAt(0) }}{{ service.provider.profile?.last_name?.charAt(0) }}
                </div>
                <div>
                  <p class="font-semibold text-gray-900">
                    {{ service.provider.profile?.first_name }} {{ service.provider.profile?.last_name }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ service.provider.profile?.city }}
                  </p>
                </div>
              </div>
              
              <div class="space-y-3">
                <div v-if="service.provider.profile?.company_name" class="flex items-center text-sm">
                  <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h3M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  <span class="text-gray-700">{{ service.provider.profile.company_name }}</span>
                </div>
                
                <div v-if="service.provider.profile?.experience_years" class="flex items-center text-sm">
                  <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                  </svg>
                  <span class="text-gray-700">{{ service.provider.profile.experience_years }} ans d'expérience</span>
                </div>
                
                <div class="flex items-center text-sm">
                  <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span class="text-gray-700">
                    {{ service.provider.profile?.city }} {{ service.provider.profile?.postal_code }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Garanties -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Nos garanties</h3>
              
              <div class="space-y-4">
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <div>
                    <p class="font-medium text-gray-900 text-sm">Prestataires vérifiés</p>
                    <p class="text-gray-600 text-xs">Tous nos prestataires sont contrôlés et approuvés</p>
                  </div>
                </div>
                
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <div>
                    <p class="font-medium text-gray-900 text-sm">Satisfaction garantie</p>
                    <p class="text-gray-600 text-xs">Service client réactif en cas de problème</p>
                  </div>
                </div>
                
                <div class="flex items-start">
                  <svg class="w-5 h-5 text-green-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  <div>
                    <p class="font-medium text-gray-900 text-sm">Annulation gratuite</p>
                    <p class="text-gray-600 text-xs">Jusqu'à l'acceptation de votre demande</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Services similaires -->
        <div v-if="similarServices.length > 0" class="mt-16">
          <h2 class="text-2xl font-bold text-gray-900 mb-8">Services similaires</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <ServiceCard
              v-for="similarService in similarServices"
              :key="similarService.id"
              :service="similarService"
            />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ServiceCard from '@/components/ServiceCard.vue'

defineProps({
  service: Object,
  similarServices: Array
})
</script>