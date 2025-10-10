<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const props = defineProps({
  user: Object,
  bookings: {
    type: Array,
    default: () => []
  },
  favoriteServices: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({
      totalBookings: 0,
      activeBookings: 0,
      completedBookings: 0,
      totalSpent: 0
    })
  }
})

const activeTab = ref('bookings')

// Filtrer les réservations par statut
const activeBookings = computed(() => 
  props.bookings.filter(booking => ['pending', 'confirmed', 'in_progress'].includes(booking.status))
)

const pastBookings = computed(() => 
  props.bookings.filter(booking => ['completed', 'cancelled'].includes(booking.status))
)

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

// Fonction pour supprimer un favori
const removeFavorite = (serviceId) => {
  router.delete(route('favorites.destroy', serviceId), {
    preserveScroll: true,
    onSuccess: () => {
      // Mise à jour locale pour une meilleure UX
      const index = props.favoriteServices.findIndex(service => service.id === serviceId)
      if (index > -1) {
        props.favoriteServices.splice(index, 1)
      }
    }
  })
}
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
                Mon Dashboard
              </h1>
              <p class="mt-1 text-sm text-gray-600">
                Bienvenue {{ user.name }}, gérez vos réservations et préférences
              </p>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="mt-4 sm:mt-0 grid grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ stats.totalBookings }}</div>
                <div class="text-xs opacity-90">Réservations</div>
              </div>
              <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ stats.activeBookings }}</div>
                <div class="text-xs opacity-90">En cours</div>
              </div>
              <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ stats.completedBookings }}</div>
                <div class="text-xs opacity-90">Terminées</div>
              </div>
              <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg p-4 text-white text-center">
                <div class="text-2xl font-bold">{{ formatPrice(stats.totalSpent) }}</div>
                <div class="text-xs opacity-90">Dépensé</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Navigation par onglets -->
        <div class="mb-8">
          <nav class="flex space-x-8" aria-label="Tabs">
            <button
              @click="activeTab = 'bookings'"
              :class="[
                activeTab === 'bookings'
                  ? 'border-green-500 text-green-600 bg-green-50'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors'
              ]"
            >
              <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              Mes Réservations
            </button>
            
            <button
              @click="activeTab = 'favorites'"
              :class="[
                activeTab === 'favorites'
                  ? 'border-green-500 text-green-600 bg-green-50'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors'
              ]"
            >
              <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              Mes Favoris
            </button>
            
            <button
              @click="activeTab = 'profile'"
              :class="[
                activeTab === 'profile'
                  ? 'border-green-500 text-green-600 bg-green-50'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors'
              ]"
            >
              <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Mon Profil
            </button>
          </nav>
        </div>

        <!-- Contenu des onglets -->
        <div class="tab-content">
          <!-- Onglet Réservations -->
          <div v-if="activeTab === 'bookings'" class="space-y-6">
            <!-- Réservations actives -->
            <div v-if="activeBookings.length > 0">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Réservations en cours</h2>
              <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="booking in activeBookings"
                  :key="booking.id"
                  class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
                >
                  <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                      <h3 class="font-semibold text-gray-900 text-lg">{{ booking.service.title }}</h3>
                      <p class="text-sm text-gray-600">{{ booking.service.provider.profile.company_name }}</p>
                    </div>
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusColor(booking.status)]">
                      {{ getStatusText(booking.status) }}
                    </span>
                  </div>
                  
                  <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                      </svg>
                      {{ formatDate(booking.preferred_datetime) }}
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                      </svg>
                      {{ formatPrice(booking.final_price) }}
                    </div>
                  </div>
                  
                  <div class="mt-4 pt-4 border-t border-gray-100">
                    <Link
                      :href="route('bookings.show', booking.id)"
                      class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-700"
                    >
                      Voir les détails
                      <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                      </svg>
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Historique des réservations -->
            <div v-if="pastBookings.length > 0">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Historique</h2>
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                  <h3 class="text-lg font-medium text-gray-900">Réservations passées</h3>
                </div>
                <div class="divide-y divide-gray-200">
                  <div
                    v-for="booking in pastBookings"
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
                        <p class="text-sm text-gray-600 mb-1">{{ booking.service.provider.profile.company_name }}</p>
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                          <span>{{ formatDate(booking.preferred_datetime) }}</span>
                          <span>{{ formatPrice(booking.final_price) }}</span>
                        </div>
                      </div>
                      <div class="ml-4 flex items-center space-x-2">
                        <Link
                          :href="route('bookings.show', booking.id)"
                          class="text-sm text-green-600 hover:text-green-700"
                        >
                          Détails
                        </Link>
                        <button
                          v-if="booking.status === 'completed'"
                          class="text-sm text-blue-600 hover:text-blue-700"
                        >
                          Reserver à nouveau
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Message si aucune réservation -->
            <div v-if="bookings.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune réservation</h3>
              <p class="mt-1 text-sm text-gray-500">Commencez par rechercher un service qui vous intéresse.</p>
              <div class="mt-6">
                <Link
                  :href="route('services.index')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  Parcourir les services
                </Link>
              </div>
            </div>
          </div>

          <!-- Onglet Favoris -->
          <div v-if="activeTab === 'favorites'" class="space-y-6">
            <div v-if="favoriteServices.length > 0">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Mes services favoris</h2>
              <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="service in favoriteServices"
                  :key="service.id"
                  class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow"
                >
                  <div class="relative">
                    <img
                      :src="service.images?.[0]?.url || '/placeholder-service.jpg'"
                      :alt="service.title"
                      class="w-full h-48 object-cover"
                    >
                    <button
                      @click="removeFavorite(service.id)"
                      class="absolute top-2 right-2 p-2 bg-white rounded-full shadow-md hover:bg-red-50 transition-colors"
                    >
                      <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
                  
                  <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                      <h3 class="font-semibold text-gray-900 text-lg">{{ service.title }}</h3>
                      <span class="text-lg font-bold text-green-600">{{ formatPrice(service.price) }}</span>
                    </div>
                    
                    <p class="text-sm text-gray-600 mb-2">{{ service.provider.profile.company_name }}</p>
                    <p class="text-sm text-gray-700 mb-4 line-clamp-2">{{ service.description }}</p>
                    
                    <div class="flex items-center justify-between">
                      <div class="flex items-center text-sm text-yellow-500">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{ service.average_rating || '5.0' }}
                        <span class="text-gray-500 ml-1">({{ service.reviews_count || 0 }})</span>
                      </div>
                      
                      <Link
                        :href="route('services.show', service.id)"
                        class="inline-flex items-center px-3 py-1 border border-green-600 text-sm font-medium rounded-md text-green-600 hover:bg-green-50 transition-colors"
                      >
                        Voir
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Message si aucun favori -->
            <div v-if="favoriteServices.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun service favori</h3>
              <p class="mt-1 text-sm text-gray-500">Sauvegardez vos services préférés pour les retrouver facilement.</p>
              <div class="mt-6">
                <Link
                  :href="route('services.index')"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  Découvrir les services
                </Link>
              </div>
            </div>
          </div>

          <!-- Onglet Profil -->
          <div v-if="activeTab === 'profile'" class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6">Mon Profil</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900">{{ user.name }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900">{{ user.email }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900">{{ user.phone || 'Non renseigné' }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900">{{ user.address || 'Non renseignée' }}</div>
                </div>
              </div>
              
              <div class="mt-6 pt-6 border-t border-gray-200">
                <Link
                  href="/profile"
                  class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  Modifier mon profil
                </Link>
              </div>
            </div>

            <!-- Préférences -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Préférences de notification</h3>
              
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Notifications par email</h4>
                    <p class="text-sm text-gray-500">Recevoir les confirmations et mises à jour</p>
                  </div>
                  <button class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 bg-green-600">
                    <span class="translate-x-5 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                  </button>
                </div>
                
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Notifications push</h4>
                    <p class="text-sm text-gray-500">Alertes sur appareil mobile</p>
                  </div>
                  <button class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 bg-gray-200">
                    <span class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                  </button>
                </div>
                
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-sm font-medium text-gray-900">Offres promotionnelles</h4>
                    <p class="text-sm text-gray-500">Recevoir les offres spéciales</p>
                  </div>
                  <button class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 bg-green-600">
                    <span class="translate-x-5 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>