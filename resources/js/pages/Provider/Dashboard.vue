<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const props = defineProps({
  provider: Object,
  stats: {
    type: Object,
    default: () => ({
      totalRevenue: 0,
      thisMonthRevenue: 0,
      totalBookings: 0,
      pendingBookings: 0,
      completedBookings: 0,
      averageRating: 0,
      totalReviews: 0,
      activeServices: 0
    })
  },
  recentBookings: {
    type: Array,
    default: () => []
  },
  services: {
    type: Array,
    default: () => []
  },
  upcomingBookings: {
    type: Array,
    default: () => []
  },
  revenueData: {
    type: Array,
    default: () => []
  },
  notifications: {
    type: Array,
    default: () => []
  }
})

// Fonction pour obtenir la couleur de statut
const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    confirmed: 'bg-blue-100 text-blue-800 border-blue-200',
    in_progress: 'bg-green-100 text-green-800 border-green-200',
    completed: 'bg-emerald-100 text-emerald-800 border-emerald-200',
    cancelled: 'bg-red-100 text-red-800 border-red-200'
  }
  return colors[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

// Fonction pour obtenir le texte de statut
const getStatusText = (status) => {
  const texts = {
    pending: 'En attente',
    confirmed: 'Confirmé',
    in_progress: 'En cours',
    completed: 'Terminé',
    cancelled: 'Annulé'
  }
  return texts[status] || status
}

// Fonction pour formater le prix
const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

// Fonction pour formater la date
const formatDate = (date) => {
  return new Intl.DateTimeFormat('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

// Fonction pour formater la date courte
const formatShortDate = (date) => {
  return new Intl.DateTimeFormat('fr-FR', {
    day: 'numeric',
    month: 'short',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

// Actions rapides pour les réservations
const acceptBooking = (bookingId) => {
  router.patch(route('bookings.accept', bookingId), {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Mise à jour locale pour une meilleure UX
    }
  })
}

const rejectBooking = (bookingId) => {
  router.patch(route('bookings.reject', bookingId), {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Mise à jour locale pour une meilleure UX
    }
  })
}

// Calculer l'évolution du chiffre d'affaires
const revenueGrowth = computed(() => {
  if (props.revenueData.length < 2) return 0
  const current = props.revenueData[props.revenueData.length - 1]?.revenue || 0
  const previous = props.revenueData[props.revenueData.length - 2]?.revenue || 0
  if (previous === 0) return 0
  return ((current - previous) / previous * 100).toFixed(1)
})
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-amber-50">
      <!-- Header du dashboard -->
      <div class="bg-white border-b border-green-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">
                Dashboard Prestataire
              </h1>
              <p class="mt-1 text-sm text-gray-600">
                Bienvenue {{ provider.profile?.company_name || provider.name }}, gérez votre activité
              </p>
            </div>
            
            <!-- Actions rapides -->
            <div class="mt-4 sm:mt-0 flex space-x-3">
              <Link
                :href="route('provider.services.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nouveau Service
              </Link>
              
              <Link
                :href="route('provider.profile')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Mon Profil
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistiques principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <!-- Revenus du mois -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-green-100">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Revenus ce mois</p>
                <p class="text-2xl font-bold text-gray-900">{{ formatPrice(stats.thisMonthRevenue) }}</p>
                <p class="text-sm text-green-600" v-if="revenueGrowth > 0">
                  +{{ revenueGrowth }}% vs mois dernier
                </p>
                <p class="text-sm text-red-600" v-else-if="revenueGrowth < 0">
                  {{ revenueGrowth }}% vs mois dernier
                </p>
              </div>
            </div>
          </div>

          <!-- Réservations en attente -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-yellow-100">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">En attente</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.pendingBookings }}</p>
                <p class="text-sm text-gray-500">réservations</p>
              </div>
            </div>
          </div>

          <!-- Note moyenne -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-amber-100">
                <svg class="w-6 h-6 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Note moyenne</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.averageRating || '5.0' }}/5</p>
                <p class="text-sm text-gray-500">{{ stats.totalReviews }} avis</p>
              </div>
            </div>
          </div>

          <!-- Services actifs -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="p-3 rounded-full bg-blue-100">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Services actifs</p>
                <p class="text-2xl font-bold text-gray-900">{{ stats.activeServices }}</p>
                <Link :href="route('provider.services.index')" class="text-sm text-blue-600 hover:text-blue-700">
                  Gérer mes services
                </Link>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Colonne principale -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Réservations récentes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">Réservations récentes</h3>
                <Link
                  :href="route('provider.bookings')"
                  class="text-sm text-green-600 hover:text-green-700 font-medium"
                >
                  Voir tout
                </Link>
              </div>
              
              <div class="divide-y divide-gray-200">
                <div
                  v-for="booking in recentBookings.slice(0, 5)"
                  :key="booking.id"
                  class="p-6 hover:bg-gray-50 transition-colors"
                >
                  <div class="flex items-center justify-between">
                    <div class="flex-1">
                      <div class="flex items-center justify-between mb-2">
                        <h4 class="font-medium text-gray-900">{{ booking.service.title }}</h4>
                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusColor(booking.status)]">
                          {{ getStatusText(booking.status) }}
                        </span>
                      </div>
                      <p class="text-sm text-gray-600 mb-1">Client: {{ booking.client.name }}</p>
                      <div class="flex items-center space-x-4 text-sm text-gray-500">
                        <span>{{ formatShortDate(booking.scheduled_date) }}</span>
                        <span>{{ formatPrice(booking.price) }}</span>
                      </div>
                    </div>
                    <div class="ml-4 flex items-center space-x-2">
                      <button
                        v-if="booking.status === 'pending'"
                        @click="acceptBooking(booking.id)"
                        class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                      >
                        Accepter
                      </button>
                      <button
                        v-if="booking.status === 'pending'"
                        @click="rejectBooking(booking.id)"
                        class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                      >
                        Refuser
                      </button>
                      <Link
                        :href="route('bookings.show', booking.id)"
                        class="text-sm text-blue-600 hover:text-blue-700"
                      >
                        Détails
                      </Link>
                    </div>
                  </div>
                </div>
                
                <div v-if="recentBookings.length === 0" class="p-6 text-center text-gray-500">
                  Aucune réservation récente
                </div>
              </div>
            </div>

            <!-- Graphique des revenus -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Évolution des revenus</h3>
              <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <div class="text-center">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  <p class="mt-2 text-sm text-gray-600">Graphique des revenus</p>
                  <p class="text-xs text-gray-500">À implémenter avec Chart.js</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar droite -->
          <div class="space-y-8">
            <!-- Prochaines interventions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Prochaines interventions</h3>
              </div>
              
              <div class="p-6">
                <div v-if="upcomingBookings.length > 0" class="space-y-4">
                  <div
                    v-for="booking in upcomingBookings.slice(0, 3)"
                    :key="booking.id"
                    class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg"
                  >
                    <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900">{{ booking.service.title }}</p>
                      <p class="text-xs text-gray-600">{{ booking.client.name }}</p>
                      <p class="text-xs text-gray-500 mt-1">{{ formatShortDate(booking.scheduled_date) }}</p>
                    </div>
                  </div>
                </div>
                
                <div v-else class="text-center text-gray-500 text-sm">
                  Aucune intervention programmée
                </div>
              </div>
            </div>

            <!-- Notifications -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
              </div>
              
              <div class="p-6">
                <div v-if="notifications.length > 0" class="space-y-4">
                  <div
                    v-for="notification in notifications.slice(0, 4)"
                    :key="notification.id"
                    class="flex items-start space-x-3"
                  >
                    <div class="flex-shrink-0">
                      <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5V12h5l-5-5v10z" />
                        </svg>
                      </div>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm text-gray-900">{{ notification.title }}</p>
                      <p class="text-xs text-gray-500 mt-1">{{ notification.created_at }}</p>
                    </div>
                  </div>
                </div>
                
                <div v-else class="text-center text-gray-500 text-sm">
                  Aucune notification
                </div>
              </div>
            </div>

            <!-- Accès rapide -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Accès rapide</h3>
              </div>
              
              <div class="p-6 space-y-3">
                <Link
                  :href="route('provider.services.index')"
                  class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                  </svg>
                  <span class="text-sm font-medium text-gray-900">Mes Services</span>
                </Link>
                
                <Link
                  :href="route('provider.earnings')"
                  class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                  </svg>
                  <span class="text-sm font-medium text-gray-900">Revenus</span>
                </Link>
                
                <button class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors w-full text-left">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                  </svg>
                  <span class="text-sm font-medium text-gray-900">Calendrier</span>
                </button>
                
                <button class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors w-full text-left">
                  <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                  </svg>
                  <span class="text-sm font-medium text-gray-900">Statistiques</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>