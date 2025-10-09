<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const props = defineProps({
  booking: {
    type: Object,
    required: true
  },
  messages: {
    type: Array,
    default: () => []
  },
  canCancel: {
    type: Boolean,
    default: false
  },
  canReview: {
    type: Boolean,
    default: false
  }
})

// État de la messagerie
const showMessages = ref(false)
const newMessage = useForm({
  message: '',
  attachment: null
})

// Fonction pour obtenir la couleur de statut
const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    confirmed: 'bg-blue-100 text-blue-800 border-blue-200',
    in_progress: 'bg-green-100 text-green-800 border-green-200',
    completed: 'bg-emerald-100 text-emerald-800 border-emerald-200',
    cancelled: 'bg-red-100 text-red-800 border-red-200',
    rejected: 'bg-red-100 text-red-800 border-red-200'
  }
  return colors[status] || 'bg-gray-100 text-gray-800 border-gray-200'
}

// Fonction pour obtenir le texte de statut
const getStatusText = (status) => {
  const texts = {
    pending: 'En attente de validation',
    confirmed: 'Confirmé',
    in_progress: 'Intervention en cours',
    completed: 'Terminé',
    cancelled: 'Annulé',
    rejected: 'Refusé'
  }
  return texts[status] || status
}

// Timeline des étapes
const timelineSteps = computed(() => {
  const steps = [
    {
      id: 'created',
      name: 'Demande envoyée',
      description: 'Votre demande a été transmise au prestataire',
      date: props.booking.created_at,
      status: 'completed',
      icon: 'calendar'
    }
  ]

  if (props.booking.status !== 'pending') {
    if (props.booking.status === 'rejected') {
      steps.push({
        id: 'rejected',
        name: 'Demande refusée',
        description: 'Le prestataire a décliné votre demande',
        date: props.booking.updated_at,
        status: 'error',
        icon: 'x-circle'
      })
    } else {
      steps.push({
        id: 'confirmed',
        name: 'Réservation confirmée',
        description: 'Le prestataire a accepté votre demande',
        date: props.booking.confirmed_at,
        status: props.booking.status === 'cancelled' ? 'cancelled' : 'completed',
        icon: 'check-circle'
      })

      if (['in_progress', 'completed'].includes(props.booking.status)) {
        steps.push({
          id: 'in_progress',
          name: 'Intervention en cours',
          description: 'Le prestataire est arrivé sur place',
          date: props.booking.started_at,
          status: props.booking.status === 'completed' ? 'completed' : 'current',
          icon: 'clock'
        })
      }

      if (props.booking.status === 'completed') {
        steps.push({
          id: 'completed',
          name: 'Service terminé',
          description: 'L\'intervention a été terminée avec succès',
          date: props.booking.completed_at,
          status: 'completed',
          icon: 'check'
        })

        if (props.booking.paid_at) {
          steps.push({
            id: 'paid',
            name: 'Paiement effectué',
            description: 'Le paiement a été traité',
            date: props.booking.paid_at,
            status: 'completed',
            icon: 'credit-card'
          })
        }
      }

      if (props.booking.status === 'cancelled') {
        steps.push({
          id: 'cancelled',
          name: 'Réservation annulée',
          description: 'La réservation a été annulée',
          date: props.booking.cancelled_at,
          status: 'error',
          icon: 'x-circle'
        })
      }
    }
  }

  return steps
})

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

// Actions sur la réservation
const cancelBooking = () => {
  if (confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')) {
    router.patch(route('bookings.cancel', props.booking.id), {}, {
      preserveScroll: true
    })
  }
}

// Envoi de message
const sendMessage = () => {
  newMessage.post(route('bookings.messages.store', props.booking.id), {
    preserveScroll: true,
    onSuccess: () => {
      newMessage.reset()
    }
  })
}

// Icônes pour la timeline
const getTimelineIcon = (iconType, status) => {
  const icons = {
    calendar: 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z',
    'check-circle': 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'x-circle': 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    clock: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
    check: 'M5 13l4 4L19 7',
    'credit-card': 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'
  }
  return icons[iconType] || icons.calendar
}

// Couleur des étapes de timeline
const getStepColor = (status) => {
  const colors = {
    completed: 'bg-green-500',
    current: 'bg-blue-500',
    pending: 'bg-gray-300',
    error: 'bg-red-500',
    cancelled: 'bg-red-500'
  }
  return colors[status] || colors.pending
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-amber-50">
      <!-- Header -->
      <div class="bg-white border-b border-green-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
              <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                  <li>
                    <Link :href="route('dashboard')" class="text-green-600 hover:text-green-700">
                      Dashboard
                    </Link>
                  </li>
                  <li>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </li>
                  <li>
                    <Link :href="route('bookings.index')" class="text-green-600 hover:text-green-700">
                      Réservations
                    </Link>
                  </li>
                  <li>
                    <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                  </li>
                  <li class="text-gray-500">Réservation #{{ booking.id }}</li>
                </ol>
              </nav>
              
              <h1 class="text-3xl font-bold text-gray-900">
                {{ booking.service.title }}
              </h1>
              <p class="mt-1 text-sm text-gray-600">
                Réservation du {{ formatDate(booking.scheduled_date) }}
              </p>
            </div>
            
            <!-- Statut -->
            <div>
              <span :class="['inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border', getStatusColor(booking.status)]">
                {{ getStatusText(booking.status) }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Contenu principal -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-6">Suivi de votre réservation</h3>
              
              <div class="flow-root">
                <ul class="-mb-8">
                  <li v-for="(step, stepIdx) in timelineSteps" :key="step.id" class="relative">
                    <div v-if="stepIdx !== timelineSteps.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></div>
                    
                    <div class="relative flex items-start space-x-3">
                      <div class="relative">
                        <div :class="['h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white', getStepColor(step.status)]">
                          <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getTimelineIcon(step.icon, step.status)" />
                          </svg>
                        </div>
                      </div>
                      
                      <div class="min-w-0 flex-1 py-1.5">
                        <div class="text-sm text-gray-500">
                          <span class="font-medium text-gray-900">{{ step.name }}</span>
                          {{ step.description }}
                          <span class="whitespace-nowrap">{{ formatShortDate(step.date) }}</span>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Détails de la réservation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-6">Détails de la réservation</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Service</h4>
                  <p class="text-sm text-gray-900">{{ booking.service.title }}</p>
                </div>
                
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Prestataire</h4>
                  <p class="text-sm text-gray-900">{{ booking.service.provider.profile?.company_name || booking.service.provider.name }}</p>
                </div>
                
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Date et heure</h4>
                  <p class="text-sm text-gray-900">{{ formatDate(booking.scheduled_date) }}</p>
                </div>
                
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Durée estimée</h4>
                  <p class="text-sm text-gray-900">{{ booking.duration || 2 }} heures</p>
                </div>
                
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Adresse d'intervention</h4>
                  <p class="text-sm text-gray-900">
                    {{ booking.address.street }}<br>
                    {{ booking.address.postal_code }} {{ booking.address.city }}
                  </p>
                  <p v-if="booking.address.additional_info" class="text-xs text-gray-500 mt-1">
                    {{ booking.address.additional_info }}
                  </p>
                </div>
                
                <div>
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Prix</h4>
                  <p class="text-sm text-gray-900 font-semibold">{{ formatPrice(booking.price) }}</p>
                </div>
              </div>
              
              <div v-if="booking.special_requests" class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Demandes spéciales</h4>
                <p class="text-sm text-gray-900 bg-gray-50 p-3 rounded-md">{{ booking.special_requests }}</p>
              </div>
              
              <div v-if="booking.service_options && booking.service_options.length > 0" class="mt-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Options sélectionnées</h4>
                <ul class="text-sm text-gray-900 space-y-1">
                  <li v-for="option in booking.service_options" :key="option.id" class="flex justify-between">
                    <span>{{ option.name }}</span>
                    <span>{{ formatPrice(option.price) }}</span>
                  </li>
                </ul>
              </div>
            </div>

            <!-- Actions -->
            <div v-if="booking.status !== 'completed' && booking.status !== 'cancelled'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
              
              <div class="flex flex-wrap gap-3">
                <button
                  v-if="canCancel"
                  @click="cancelBooking"
                  class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                  Annuler la réservation
                </button>
                
                <button
                  @click="showMessages = !showMessages"
                  class="inline-flex items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                  </svg>
                  {{ showMessages ? 'Masquer' : 'Afficher' }} la messagerie
                </button>
              </div>
            </div>

            <!-- Messagerie -->
            <div v-if="showMessages" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Messagerie</h3>
              
              <!-- Messages -->
              <div class="max-h-64 overflow-y-auto mb-4 space-y-4">
                <div
                  v-for="message in messages"
                  :key="message.id"
                  :class="[
                    'flex',
                    message.is_from_client ? 'justify-end' : 'justify-start'
                  ]"
                >
                  <div :class="[
                    'max-w-xs lg:max-w-md px-4 py-2 rounded-lg text-sm',
                    message.is_from_client 
                      ? 'bg-green-600 text-white' 
                      : 'bg-gray-100 text-gray-900'
                  ]">
                    <p>{{ message.content }}</p>
                    <p :class="[
                      'text-xs mt-1',
                      message.is_from_client ? 'text-green-100' : 'text-gray-500'
                    ]">
                      {{ formatShortDate(message.created_at) }}
                    </p>
                  </div>
                </div>
                
                <div v-if="messages.length === 0" class="text-center text-gray-500 py-8">
                  Aucun message pour le moment
                </div>
              </div>
              
              <!-- Nouveau message -->
              <form @submit.prevent="sendMessage" class="space-y-3">
                <textarea
                  v-model="newMessage.message"
                  rows="3"
                  placeholder="Tapez votre message..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  required
                ></textarea>
                
                <div class="flex justify-end">
                  <button
                    type="submit"
                    :disabled="newMessage.processing || !newMessage.message.trim()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                  >
                    <svg v-if="newMessage.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ newMessage.processing ? 'Envoi...' : 'Envoyer' }}
                  </button>
                </div>
              </form>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Informations du prestataire -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Votre prestataire</h3>
              
              <div class="flex items-center space-x-4 mb-4">
                <div class="flex-shrink-0">
                  <img
                    :src="booking.service.provider.profile?.avatar || '/default-avatar.jpg'"
                    :alt="booking.service.provider.name"
                    class="h-12 w-12 rounded-full"
                  >
                </div>
                <div>
                  <h4 class="text-sm font-medium text-gray-900">{{ booking.service.provider.name }}</h4>
                  <p class="text-sm text-gray-600">{{ booking.service.provider.profile?.company_name }}</p>
                </div>
              </div>
              
              <div class="space-y-3 text-sm">
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                  <span>{{ booking.service.average_rating || '5.0' }}/5 ({{ booking.service.reviews_count || 0 }} avis)</span>
                </div>
                
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  <span>{{ booking.service.provider.completed_bookings || 0 }} services réalisés</span>
                </div>
                
                <div class="flex items-center">
                  <svg class="w-4 h-4 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  <span>{{ booking.service.provider.profile?.city || 'Localisation non renseignée' }}</span>
                </div>
              </div>
              
              <div class="mt-4 pt-4 border-t border-gray-200">
                <Link
                  :href="route('services.show', booking.service.id)"
                  class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-700"
                >
                  Voir le profil complet
                  <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </div>
            </div>

            <!-- Facturation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Facturation</h3>
              
              <div class="space-y-3">
                <div class="flex justify-between text-sm">
                  <span>Service de base</span>
                  <span>{{ formatPrice(booking.service.price) }}</span>
                </div>
                
                <div v-for="option in booking.service_options" :key="option.id" class="flex justify-between text-sm">
                  <span>{{ option.name }}</span>
                  <span>{{ formatPrice(option.price) }}</span>
                </div>
                
                <div class="border-t pt-3">
                  <div class="flex justify-between font-semibold">
                    <span>Total</span>
                    <span class="text-green-600">{{ formatPrice(booking.price) }}</span>
                  </div>
                </div>
                
                <div v-if="booking.status === 'completed' && booking.paid_at" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-md">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm text-green-700">
                      Paiement effectué le {{ formatShortDate(booking.paid_at) }}
                    </span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Actions rapides -->
            <div v-if="canReview" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluation</h3>
              
              <p class="text-sm text-gray-600 mb-4">
                Votre service est terminé. Partagez votre expérience !
              </p>
              
              <Link
                :href="route('reviews.create', booking.id)"
                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                Laisser un avis
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>