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
            <!-- Graphique des gains -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Évolution des gains (6 derniers mois)</h3>
                <div class="h-64">
                  <div class="h-full flex items-end justify-between space-x-2">
                    <div v-for="(month, index) in monthlyEarnings" :key="index" class="flex-1 flex flex-col items-center">
                      <div 
                        class="w-full bg-gradient-to-t from-primary to-primary/70 rounded-t-md transition-all duration-300 hover:from-primary/80 hover:to-primary/50"
                        :style="{ height: Math.max((month.earnings / 1200 * 200), 10) + 'px' }"
                        :title="`${month.month}: ${formatCurrency(month.earnings)}`"
                      ></div>
                      <span class="text-xs text-gray-600 mt-2 transform -rotate-45 origin-top-left">
                        {{ month.month.split(' ')[0] }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Répartition des réservations -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Réservations par statut</h3>
                <div class="grid grid-cols-2 gap-4">
                  <div v-for="status in bookingsByStatus" :key="status.status" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                      <div 
                        class="w-3 h-3 rounded-full mr-3" 
                        :style="{ backgroundColor: status.color }"
                      ></div>
                      <span class="text-sm text-gray-700">{{ status.status }}</span>
                    </div>
                    <span class="text-lg font-semibold text-gray-900">{{ status.count }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Activité récente -->
            <div class="bg-white shadow rounded-lg">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Activité récente</h3>
                
                <div v-if="recentActivity.length === 0" class="text-center py-6">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune activité récente</h3>
                  <p class="mt-1 text-sm text-gray-500">Votre activité récente apparaîtra ici.</p>
                </div>

                <div v-else class="space-y-4">
                  <div v-for="activity in recentActivity" :key="activity.title" class="flex items-start space-x-3">
                    <div 
                      class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center"
                      :class="`bg-${activity.color}-100`"
                    >
                      <svg class="w-4 h-4" :class="`text-${activity.color}-600`" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="activity.icon === 'calendar'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        <path v-if="activity.icon === 'star'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        <path v-if="activity.icon === 'edit'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        <path v-if="activity.icon === 'credit-card'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900">{{ activity.title }}</p>
                      <p class="text-sm text-gray-600">{{ activity.description }}</p>
                      <p class="text-xs text-gray-500 mt-1">{{ formatRelativeTime(activity.date) }}</p>
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
  monthlyEarnings: Array,
  bookingsByStatus: Array,
  services: Array,
  recentActivity: Array,
  upcomingBookings: Array,
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

const formatRelativeTime = (date) => {
  const now = new Date()
  const past = new Date(date)
  const diffInHours = Math.floor((now - past) / (1000 * 60 * 60))
  
  if (diffInHours < 1) {
    return 'Il y a moins d\'une heure'
  } else if (diffInHours < 24) {
    return `Il y a ${diffInHours} heure${diffInHours > 1 ? 's' : ''}`
  } else {
    const diffInDays = Math.floor(diffInHours / 24)
    return `Il y a ${diffInDays} jour${diffInDays > 1 ? 's' : ''}`
  }
}
</script>