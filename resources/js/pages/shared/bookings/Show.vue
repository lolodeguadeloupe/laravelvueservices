<template>
  <component :is="isProvider ? ProviderLayout : AppLayout">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- En-tête -->
      <div class="mb-8">
        <Link :href="route('bookings.index')" class="inline-flex items-center text-sm text-gray-500 hover:text-primary mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Retour aux réservations
        </Link>
        <div class="flex items-center justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Détails de la réservation</h1>
            <p class="text-gray-600">Référence : {{ booking.uuid }}</p>
          </div>
          <span
            :class="{
              'bg-yellow-100 text-yellow-800': booking.status === 'pending',
              'bg-blue-100 text-blue-800': booking.status === 'accepted',
              'bg-green-100 text-green-800': booking.status === 'completed',
              'bg-red-100 text-red-800': ['cancelled', 'rejected'].includes(booking.status),
            }"
            class="px-4 py-2 rounded-full text-sm font-medium"
          >
            {{ booking.status_label }}
          </span>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Détails de la réservation -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Service -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Service demandé</h2>
            <div class="flex items-start">
              <img
                :src="booking.service.images?.[0]?.url || '/default-service.jpg'"
                :alt="booking.service.title"
                class="w-20 h-20 object-cover rounded-lg mr-4"
              />
              <div class="flex-1">
                <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ booking.service.title }}</h3>
                <p class="text-gray-600 mb-2">{{ booking.service.description }}</p>
                <div class="flex items-center text-sm text-gray-500">
                  <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-medium">
                    {{ booking.service.category.name }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Informations de la réservation -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations de la réservation</h2>
            <div class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <p class="text-sm text-gray-500 mb-1">Date souhaitée</p>
                  <p class="font-medium text-gray-900">
                    {{ new Date(booking.preferred_datetime).toLocaleString('fr-FR') }}
                  </p>
                </div>
                
                <div v-if="booking.confirmed_datetime">
                  <p class="text-sm text-gray-500 mb-1">Date confirmée</p>
                  <p class="font-medium text-gray-900">
                    {{ new Date(booking.confirmed_datetime).toLocaleString('fr-FR') }}
                  </p>
                </div>
              </div>
              
              <div>
                <p class="text-sm text-gray-500 mb-1">Adresse d'intervention</p>
                <p class="font-medium text-gray-900">{{ booking.formatted_address }}</p>
                <p v-if="booking.client_address.additional_info" class="text-sm text-gray-600 mt-1">
                  {{ booking.client_address.additional_info }}
                </p>
              </div>
              
              <div v-if="booking.client_notes">
                <p class="text-sm text-gray-500 mb-1">Notes du client</p>
                <p class="text-gray-900">{{ booking.client_notes }}</p>
              </div>
              
              <div v-if="booking.provider_notes">
                <p class="text-sm text-gray-500 mb-1">Notes du prestataire</p>
                <p class="text-gray-900">{{ booking.provider_notes }}</p>
              </div>
            </div>
          </div>

          <!-- Historique des statuts -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Historique</h2>
            <div class="space-y-4">
              <div class="flex items-start">
                <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-4 mt-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900">Demande créée</p>
                  <p class="text-sm text-gray-500">{{ new Date(booking.created_at).toLocaleString('fr-FR') }}</p>
                </div>
              </div>
              
              <div v-if="booking.accepted_at" class="flex items-start">
                <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-4 mt-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900">Demande acceptée</p>
                  <p class="text-sm text-gray-500">{{ new Date(booking.accepted_at).toLocaleString('fr-FR') }}</p>
                </div>
              </div>
              
              <div v-if="booking.completed_at" class="flex items-start">
                <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center mr-4 mt-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900">Service terminé</p>
                  <p class="text-sm text-gray-500">{{ new Date(booking.completed_at).toLocaleString('fr-FR') }}</p>
                </div>
              </div>
              
              <div v-if="booking.cancelled_at" class="flex items-start">
                <div class="w-8 h-8 bg-red-100 text-red-600 rounded-full flex items-center justify-center mr-4 mt-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </div>
                <div>
                  <p class="font-medium text-gray-900">Réservation annulée</p>
                  <p class="text-sm text-gray-500">{{ new Date(booking.cancelled_at).toLocaleString('fr-FR') }}</p>
                  <p v-if="booking.cancellation_reason" class="text-sm text-gray-600 mt-1">
                    Raison : {{ booking.cancellation_reason }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Actions et informations -->
        <div class="space-y-6">
          <!-- Informations de contact -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
              {{ isProvider ? 'Client' : 'Prestataire' }}
            </h3>
            
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium mr-4">
                {{ isProvider 
                  ? (booking.client.profile?.first_name?.charAt(0) + booking.client.profile?.last_name?.charAt(0))
                  : (booking.provider.profile?.first_name?.charAt(0) + booking.provider.profile?.last_name?.charAt(0))
                }}
              </div>
              <div>
                <p class="font-medium text-gray-900">
                  {{ isProvider 
                    ? (booking.client.profile?.first_name + ' ' + booking.client.profile?.last_name)
                    : (booking.provider.profile?.first_name + ' ' + booking.provider.profile?.last_name)
                  }}
                </p>
                <p class="text-sm text-gray-500">
                  {{ isProvider 
                    ? booking.client.profile?.city
                    : booking.provider.profile?.city
                  }}
                </p>
              </div>
            </div>
            
            <div class="space-y-2">
              <p class="text-sm">
                <span class="text-gray-500">Email :</span>
                <span class="ml-2 text-gray-900">
                  {{ isProvider ? booking.client.email : booking.provider.email }}
                </span>
              </p>
              <p v-if="booking.provider.profile?.phone && !isProvider" class="text-sm">
                <span class="text-gray-500">Téléphone :</span>
                <span class="ml-2 text-gray-900">{{ booking.provider.profile.phone }}</span>
              </p>
            </div>
          </div>

          <!-- Prix -->
          <div v-if="booking.quoted_price || booking.final_price" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tarification</h3>
            <div class="space-y-3">
              <div v-if="booking.quoted_price" class="flex items-center justify-between">
                <span class="text-gray-600">Prix estimé</span>
                <span class="font-semibold text-gray-900">{{ booking.quoted_price }}€</span>
              </div>
              
              <div v-if="booking.final_price" class="flex items-center justify-between border-t border-gray-100 pt-3">
                <span class="text-gray-600">Prix final</span>
                <span class="text-xl font-bold text-primary">{{ booking.final_price }}€</span>
              </div>
              
              <div v-if="booking.estimated_duration" class="flex items-center justify-between">
                <span class="text-gray-600">Durée estimée</span>
                <span class="text-gray-900">{{ Math.floor(booking.estimated_duration / 60) }}h{{ booking.estimated_duration % 60 > 0 ? (booking.estimated_duration % 60).toString().padStart(2, '0') : '' }}</span>
              </div>
            </div>
          </div>

          <!-- Actions pour prestataires -->
          <div v-if="isProvider" class="space-y-3">
            <!-- Accepter -->
            <button
              v-if="canAccept"
              @click="showAcceptModal = true"
              class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
            >
              Accepter la demande
            </button>
            
            <!-- Refuser -->
            <button
              v-if="canReject"
              @click="showRejectModal = true"
              class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
            >
              Refuser la demande
            </button>
            
            <!-- Faire un devis -->
            <button
              v-if="booking.isPending"
              @click="showQuoteModal = true"
              class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              {{ booking.quoted_price ? 'Modifier le devis' : 'Faire un devis' }}
            </button>
            
            <!-- Marquer comme terminé -->
            <button
              v-if="canComplete"
              @click="showCompleteModal = true"
              class="w-full px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
            >
              Marquer comme terminé
            </button>
          </div>

          <!-- Actions pour clients -->
          <div v-if="isClient" class="space-y-3">
            <!-- Payer -->
            <Link
              v-if="canPay"
              :href="route('payments.create', booking.id)"
              class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-center block"
            >
              Payer maintenant ({{ booking.final_price || booking.quoted_price }}€)
            </Link>
          </div>

          <!-- Actions communes -->
          <div class="space-y-3">
            <!-- Annuler -->
            <button
              v-if="canCancel"
              @click="showCancelModal = true"
              class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
            >
              Annuler la réservation
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <!-- Modal Accepter -->
    <div v-if="showAcceptModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Accepter la demande</h3>
        <p class="text-gray-600 mb-6">Êtes-vous sûr de vouloir accepter cette demande de service ?</p>
        
        <div class="flex space-x-3">
          <button
            @click="showAcceptModal = false"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
          >
            Annuler
          </button>
          <Form
            :action="route('bookings.accept', booking.id)"
            method="patch"
            class="flex-1"
          >
            <button
              type="submit"
              class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
            >
              Accepter
            </button>
          </Form>
        </div>
      </div>
    </div>

    <!-- Modal Annuler -->
    <div v-if="showCancelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Annuler la réservation</h3>
        
        <Form
          :action="route('bookings.cancel', booking.id)"
          method="patch"
          #default="{ processing }"
        >
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Raison de l'annulation *
            </label>
            <textarea
              name="cancellation_reason"
              rows="3"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
              placeholder="Expliquez brièvement la raison de l'annulation..."
            ></textarea>
          </div>
          
          <div class="flex space-x-3">
            <button
              type="button"
              @click="showCancelModal = false"
              class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
            >
              Fermer
            </button>
            <button
              type="submit"
              :disabled="processing"
              class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
            >
              {{ processing ? 'Annulation...' : 'Confirmer l\'annulation' }}
            </button>
          </div>
        </Form>
      </div>
    </div>
  </component>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Form } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ProviderLayout from '@/layouts/ProviderLayout.vue'

const props = defineProps({
  booking: Object,
  isProvider: Boolean,
  isClient: Boolean,
  canAccept: Boolean,
  canReject: Boolean,
  canComplete: Boolean,
  canCancel: Boolean,
  canPay: Boolean
})

// Modal state
const showAcceptModal = ref(false)
const showRejectModal = ref(false)
const showCompleteModal = ref(false)
const showQuoteModal = ref(false)
const showCancelModal = ref(false)
</script>