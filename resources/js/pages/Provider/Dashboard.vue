<template>
  <AppLayout title="Dashboard Prestataire">
    <div class="py-6">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- En-tête du dashboard -->
        <div class="mb-8">
          <h1 class="text-3xl font-bold text-gray-900">Dashboard Prestataire</h1>
          <p class="mt-2 text-gray-600">
            Bienvenue {{ provider.profile?.full_name || provider.name }}, voici un aperçu de votre activité.
          </p>
        </div>

        <!-- Cartes de statistiques -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
          <!-- Services actifs -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-primary rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6.894"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Services actifs</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.active_services }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Demandes en attente -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">En attente</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.pending_requests }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Réservations terminées -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Terminées</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ stats.completed_bookings }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <!-- Gains totaux -->
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-secondary rounded-md flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Gains totaux</dt>
                    <dd class="text-lg font-medium text-gray-900">{{ formatCurrency(stats.total_earnings) }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Contenu principal -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Colonne principale -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Demandes récentes -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-medium text-gray-900">Demandes récentes</h3>
                  <Link :href="providerBookings.url()" class="text-sm text-primary hover:text-primary/80">
                    Voir toutes
                  </Link>
                </div>
                
                <div v-if="recentRequests.length === 0" class="text-center py-6">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                  <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune demande</h3>
                  <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de demandes de réservation.</p>
                </div>

                <div v-else class="space-y-3">
                  <div v-for="request in recentRequests" :key="request.id" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                      <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                          <span class="text-sm font-medium text-gray-700">
                            {{ request.client?.profile?.first_name?.charAt(0) || 'C' }}
                          </span>
                        </div>
                      </div>
                      <div>
                        <p class="text-sm font-medium text-gray-900">
                          {{ request.client?.profile?.full_name || 'Client' }}
                        </p>
                        <p class="text-sm text-gray-500">{{ request.service?.title }}</p>
                      </div>
                    </div>
                    <div class="flex items-center space-x-2">
                      <span :class="getStatusBadgeClass(request.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ getStatusText(request.status) }}
                      </span>
                      <span class="text-sm font-medium text-gray-900">
                        {{ formatCurrency(request.total_amount) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Services populaires -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-medium text-gray-900">Vos services</h3>
                  <Link :href="providerServices.url()" class="text-sm text-primary hover:text-primary/80">
                    Gérer les services
                  </Link>
                </div>
                
                <div v-if="services.length === 0" class="text-center py-6">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                  </svg>
                  <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun service</h3>
                  <p class="mt-1 text-sm text-gray-500">Créez votre premier service pour commencer à recevoir des demandes.</p>
                </div>

                <div v-else class="space-y-3">
                  <div v-for="service in services" :key="service.id" class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ service.title }}</p>
                      <p class="text-sm text-gray-500">{{ service.category?.name }}</p>
                    </div>
                    <div class="text-right">
                      <p class="text-sm font-medium text-gray-900">{{ formatCurrency(service.price) }}</p>
                      <p class="text-sm text-gray-500">{{ service.is_active ? 'Actif' : 'Inactif' }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-8">
            <!-- Profil du prestataire -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Mon profil</h3>
                
                <div class="flex items-center space-x-3 mb-4">
                  <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center">
                      <span class="text-lg font-medium text-white">
                        {{ provider.profile?.first_name?.charAt(0) || provider.name?.charAt(0) || 'P' }}
                      </span>
                    </div>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">
                      {{ provider.profile?.full_name || provider.name }}
                    </p>
                    <p class="text-sm text-gray-500">{{ provider.email }}</p>
                  </div>
                </div>

                <!-- Note et avis -->
                <div class="flex items-center mb-4">
                  <div class="flex items-center">
                    <template v-for="i in 5" :key="i">
                      <svg 
                        :class="i <= Math.floor(stats.rating) ? 'text-yellow-400' : 'text-gray-300'"
                        class="w-4 h-4" 
                        fill="currentColor" 
                        viewBox="0 0 20 20"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                      </svg>
                    </template>
                  </div>
                  <span class="ml-2 text-sm text-gray-600">
                    {{ stats.rating.toFixed(1) }} ({{ stats.reviews_count }} avis)
                  </span>
                </div>

                <Link :href="providerProfile.url()" class="w-full inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                  Modifier le profil
                </Link>
              </div>
            </div>

            <!-- Actions rapides -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions rapides</h3>
                
                <div class="space-y-3">
                  <Link :href="providerServices.url()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                    Gérer mes services
                  </Link>
                  <Link :href="providerBookings.url()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                    Voir les réservations
                  </Link>
                  <Link :href="providerEarnings.url()" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                    Consulter les gains
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
import { 
  dashboard as providerDashboard,
  services as providerServices,
  bookings as providerBookings,
  earnings as providerEarnings,
  profile as providerProfile
} from '@/routes/provider'

defineProps({
  stats: Object,
  recentRequests: Array,
  services: Array,
  provider: Object,
})

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount || 0)
}

const getStatusBadgeClass = (status) => {
  const classes = {
    'pending': 'bg-yellow-100 text-yellow-800',
    'accepted': 'bg-blue-100 text-blue-800',
    'in_progress': 'bg-indigo-100 text-indigo-800',
    'completed': 'bg-green-100 text-green-800',
    'cancelled': 'bg-red-100 text-red-800',
    'rejected': 'bg-gray-100 text-gray-800',
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getStatusText = (status) => {
  const texts = {
    'pending': 'En attente',
    'accepted': 'Acceptée',
    'in_progress': 'En cours',
    'completed': 'Terminée',
    'cancelled': 'Annulée',
    'rejected': 'Refusée',
  }
  return texts[status] || status
}
</script>