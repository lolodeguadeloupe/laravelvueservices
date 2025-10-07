<template>
  <ProviderLayout>
    <div class="max-w-4xl mx-auto p-6">
      <!-- En-tête -->
      <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
          <Link
            :href="route('provider.services.index')"
            class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <div class="flex-1">
            <div class="flex items-center justify-between">
              <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ service.title }}</h1>
                <div class="flex items-center gap-4 mt-2">
                  <span
                    :class="service.is_active
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'"
                    class="px-3 py-1 rounded-full text-sm font-medium"
                  >
                    {{ service.is_active ? 'Service actif' : 'Service inactif' }}
                  </span>
                  <span class="text-gray-600">{{ service.category?.name }}</span>
                </div>
              </div>
              <div class="flex gap-3">
                <Link
                  :href="route('bookings.index') + '?service=' + service.id"
                  class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-primary-dark transition-colors inline-flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V6a2 2 0 112 0v1m-2 0h4m-4 0a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V9a2 2 0 00-2-2H10z" />
                  </svg>
                  Voir les réservations
                </Link>
                <Link
                  :href="route('provider.services.edit', service.id)"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                  Modifier
                </Link>
                <button
                  @click="toggleStatus"
                  :class="service.is_active
                    ? 'bg-red-100 text-red-700 hover:bg-red-200'
                    : 'bg-green-100 text-green-700 hover:bg-green-200'"
                  class="px-4 py-2 rounded-lg font-medium transition-colors"
                >
                  {{ service.is_active ? 'Désactiver' : 'Activer' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Contenu principal -->
        <div class="lg:col-span-2 space-y-8">
          <!-- Images -->
          <div v-if="service.images && service.images.length > 0" class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Galerie</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div
                v-for="(image, index) in service.images"
                :key="index"
                class="aspect-video rounded-lg overflow-hidden bg-gray-100"
              >
                <img
                  :src="`/storage/${image}`"
                  :alt="`Image ${index + 1}`"
                  class="w-full h-full object-cover hover:scale-105 transition-transform cursor-pointer"
                  @click="openImageModal(image)"
                >
              </div>
            </div>
          </div>

          <!-- Description -->
          <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Description</h2>
            <div class="prose prose-gray max-w-none">
              <p class="text-gray-700 whitespace-pre-line">{{ service.description }}</p>
            </div>
          </div>

          <!-- Prérequis -->
          <div v-if="service.requirements" class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Prérequis et conditions</h2>
            <div class="prose prose-gray max-w-none">
              <p class="text-gray-700 whitespace-pre-line">{{ service.requirements }}</p>
            </div>
          </div>

          <!-- Demandes de réservation récentes -->
          <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Demandes récentes</h2>
            <div v-if="service.booking_requests && service.booking_requests.length > 0" class="space-y-4">
              <div
                v-for="request in service.booking_requests.slice(0, 5)"
                :key="request.id"
                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg"
              >
                <div>
                  <h3 class="font-medium text-gray-900">{{ request.client_name || 'Client' }}</h3>
                  <p class="text-sm text-gray-600">{{ formatDate(request.created_at) }}</p>
                  <div class="mt-1">
                    <span
                      :class="getStatusClass(request.status)"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    >
                      {{ getStatusLabel(request.status) }}
                    </span>
                  </div>
                </div>
                <div class="text-right">
                  <p class="text-lg font-semibold text-gray-900">{{ formatPrice(request.total_amount) }}</p>
                  <p class="text-sm text-gray-600">{{ request.preferred_date ? formatDate(request.preferred_date) : 'Date flexible' }}</p>
                </div>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8a2 2 0 012 2v9a2 2 0 01-2 2H6a2 2 0 01-2-2v-9a2 2 0 012-2z" />
              </svg>
              <p class="text-gray-600">Aucune demande de réservation pour ce service</p>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Informations du service -->
          <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
            <dl class="space-y-4">
              <div>
                <dt class="text-sm font-medium text-gray-700">Prix</dt>
                <dd class="text-lg font-bold text-primary">{{ formatPrice(service.price, service.price_type) }}</dd>
              </div>
              <div v-if="service.duration">
                <dt class="text-sm font-medium text-gray-700">Durée estimée</dt>
                <dd class="text-gray-900">{{ formatDuration(service.duration) }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-700">Lieu d'intervention</dt>
                <dd class="text-gray-900">{{ formatLocationType(service.location_type) }}</dd>
              </div>
              <div v-if="service.service_area">
                <dt class="text-sm font-medium text-gray-700">Zone de service</dt>
                <dd class="text-gray-900">{{ service.service_area }}</dd>
              </div>
            </dl>
          </div>

          <!-- Statistiques -->
          <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
            <dl class="space-y-4">
              <div class="flex justify-between">
                <dt class="text-sm text-gray-600">Total des demandes</dt>
                <dd class="text-sm font-medium text-gray-900">{{ service.booking_requests?.length || 0 }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm text-gray-600">Créé le</dt>
                <dd class="text-sm text-gray-900">{{ formatDate(service.created_at) }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-sm text-gray-600">Dernière modification</dt>
                <dd class="text-sm text-gray-900">{{ formatDate(service.updated_at) }}</dd>
              </div>
            </dl>
          </div>

          <!-- Actions rapides -->
          <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions rapides</h3>
            <div class="space-y-3">
              <Link
                :href="route('provider.services.edit', service.id)"
                class="w-full bg-blue-50 text-blue-700 border border-blue-200 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 transition-colors text-center block"
              >
                Modifier le service
              </Link>
              <button
                @click="duplicateService"
                class="w-full bg-secondary/20 text-secondary border border-secondary/30 px-4 py-2 rounded-lg font-medium hover:bg-secondary/30 transition-colors"
              >
                Dupliquer ce service
              </button>
              <button
                @click="toggleStatus"
                :class="service.is_active
                  ? 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100'
                  : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100'"
                class="w-full border px-4 py-2 rounded-lg font-medium transition-colors"
              >
                {{ service.is_active ? 'Désactiver' : 'Activer' }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal d'image -->
      <div v-if="selectedImage" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50" @click="closeImageModal">
        <div class="max-w-4xl max-h-4xl mx-4">
          <img
            :src="`/storage/${selectedImage}`"
            alt="Image agrandie"
            class="max-w-full max-h-full object-contain rounded-lg"
          >
        </div>
        <button
          @click="closeImageModal"
          class="absolute top-4 right-4 text-white p-2 rounded-full bg-black/50 hover:bg-black/70"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
    </div>
  </ProviderLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import ProviderLayout from '@/layouts/ProviderLayout.vue'

// Props
const props = defineProps({
  service: {
    type: Object,
    required: true
  }
})

// State
const selectedImage = ref(null)

// Méthodes
const formatPrice = (price, priceType) => {
  const formattedPrice = new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)

  switch (priceType) {
    case 'hourly':
      return `${formattedPrice}/h`
    case 'custom':
      return 'Sur devis'
    default:
      return formattedPrice
  }
}

const formatDuration = (minutes) => {
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  
  if (hours > 0 && mins > 0) {
    return `${hours}h ${mins}min`
  } else if (hours > 0) {
    return `${hours}h`
  } else {
    return `${mins}min`
  }
}

const formatLocationType = (type) => {
  switch (type) {
    case 'client_location':
      return 'Chez le client uniquement'
    case 'provider_location':
      return 'Dans mes locaux uniquement'
    case 'both':
      return 'Chez le client ou dans mes locaux'
    default:
      return 'Non spécifié'
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getStatusClass = (status) => {
  switch (status) {
    case 'pending':
      return 'bg-yellow-100 text-yellow-800'
    case 'accepted':
      return 'bg-blue-100 text-blue-800'
    case 'in_progress':
      return 'bg-purple-100 text-purple-800'
    case 'completed':
      return 'bg-green-100 text-green-800'
    case 'cancelled':
      return 'bg-red-100 text-red-800'
    default:
      return 'bg-gray-100 text-gray-800'
  }
}

const getStatusLabel = (status) => {
  switch (status) {
    case 'pending':
      return 'En attente'
    case 'accepted':
      return 'Acceptée'
    case 'in_progress':
      return 'En cours'
    case 'completed':
      return 'Terminée'
    case 'cancelled':
      return 'Annulée'
    default:
      return status
  }
}

const toggleStatus = () => {
  router.patch(route('provider.services.toggle-status', props.service.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      props.service.is_active = !props.service.is_active
    }
  })
}

const duplicateService = () => {
  router.get(route('provider.services.create'), {
    duplicate: props.service.id
  })
}

const openImageModal = (image) => {
  selectedImage.value = image
}

const closeImageModal = () => {
  selectedImage.value = null
}
</script>