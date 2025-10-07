<template>
  <div class="flex items-start justify-between">
    <div class="flex-1">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
          <img
            :src="booking.service.images?.[0]?.url || '/default-service.jpg'"
            :alt="booking.service.title"
            class="w-16 h-16 object-cover rounded-lg mr-4"
          />
          <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">
              {{ booking.service.title }}
            </h3>
            <p class="text-sm text-gray-600">
              {{ booking.service.category.name }}
            </p>
          </div>
        </div>
        
        <span
          :class="{
            'bg-yellow-100 text-yellow-800': booking.status === 'pending',
            'bg-blue-100 text-blue-800': booking.status === 'accepted',
            'bg-green-100 text-green-800': booking.status === 'completed',
            'bg-red-100 text-red-800': ['cancelled', 'rejected'].includes(booking.status),
          }"
          class="px-3 py-1 rounded-full text-sm font-medium"
        >
          {{ booking.status_label }}
        </span>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <p class="text-sm text-gray-500 mb-1">Date souhaitée</p>
          <p class="text-sm font-medium text-gray-900">
            {{ new Date(booking.preferred_datetime).toLocaleDateString('fr-FR', {
              weekday: 'short',
              year: 'numeric',
              month: 'short',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            }) }}
          </p>
        </div>
        
        <div v-if="booking.confirmed_datetime">
          <p class="text-sm text-gray-500 mb-1">Date confirmée</p>
          <p class="text-sm font-medium text-gray-900">
            {{ new Date(booking.confirmed_datetime).toLocaleDateString('fr-FR', {
              weekday: 'short',
              year: 'numeric',
              month: 'short',
              day: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            }) }}
          </p>
        </div>
        
        <div>
          <p class="text-sm text-gray-500 mb-1">Adresse</p>
          <p class="text-sm font-medium text-gray-900">
            {{ booking.formatted_address }}
          </p>
        </div>
      </div>
      
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="flex items-center">
            <div class="w-8 h-8 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium mr-2">
              {{ booking.client.profile?.first_name?.charAt(0) }}{{ booking.client.profile?.last_name?.charAt(0) }}
            </div>
            <span class="text-sm text-gray-700">
              {{ booking.client.profile?.first_name }} {{ booking.client.profile?.last_name }}
            </span>
          </div>
          
          <div v-if="booking.quoted_price" class="text-sm">
            <span class="text-gray-500">Devis :</span>
            <span class="font-semibold text-gray-900 ml-1">{{ booking.quoted_price }}€</span>
          </div>
          
          <div v-if="booking.final_price && booking.final_price !== booking.quoted_price" class="text-sm">
            <span class="text-gray-500">Final :</span>
            <span class="font-semibold text-primary ml-1">{{ booking.final_price }}€</span>
          </div>
        </div>
        
        <div class="flex items-center space-x-2">
          <!-- Actions rapides pour les demandes en attente -->
          <div v-if="showActions && booking.status === 'pending'" class="flex space-x-2">
            <Form
              :action="route('bookings.accept', booking.id)"
              method="patch"
              #default="{ processing }"
            >
              <button
                type="submit"
                :disabled="processing"
                class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors disabled:opacity-50"
              >
                {{ processing ? '...' : 'Accepter' }}
              </button>
            </Form>
            
            <button
              @click="showRejectModal = true"
              class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors"
            >
              Refuser
            </button>
            
            <button
              @click="showQuoteModal = true"
              class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors"
            >
              Devis
            </button>
          </div>
          
          <Link
            :href="route('bookings.show', booking.uuid)"
            class="px-4 py-2 text-primary border border-primary rounded-lg hover:bg-primary/5 transition-colors text-sm"
          >
            Voir détails
          </Link>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Refuser -->
  <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Refuser la demande</h3>
      
      <Form
        :action="route('bookings.reject', booking.id)"
        method="patch"
        #default="{ processing }"
        @success="showRejectModal = false"
      >
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Motif du refus (optionnel)
          </label>
          <textarea
            name="provider_notes"
            rows="3"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
            placeholder="Expliquez brièvement pourquoi vous refusez cette demande..."
          ></textarea>
        </div>
        
        <div class="flex space-x-3">
          <button
            type="button"
            @click="showRejectModal = false"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
          >
            Annuler
          </button>
          <button
            type="submit"
            :disabled="processing"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50"
          >
            {{ processing ? 'Refus...' : 'Confirmer le refus' }}
          </button>
        </div>
      </Form>
    </div>
  </div>

  <!-- Modal Devis -->
  <div v-if="showQuoteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">
        {{ booking.quoted_price ? 'Modifier le devis' : 'Établir un devis' }}
      </h3>
      
      <Form
        :action="route('bookings.quote', booking.id)"
        method="patch"
        #default="{ processing }"
        @success="showQuoteModal = false"
      >
        <div class="space-y-4 mb-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Prix proposé *
            </label>
            <div class="relative">
              <input
                type="number"
                name="quoted_price"
                :value="booking.quoted_price || ''"
                step="0.01"
                min="0"
                required
                class="w-full px-3 py-2 pr-8 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                placeholder="0.00"
              />
              <span class="absolute right-3 top-2 text-gray-500">€</span>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Durée estimée (minutes)
            </label>
            <input
              type="number"
              name="estimated_duration"
              :value="booking.estimated_duration || ''"
              min="15"
              step="15"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
              placeholder="60"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Date et heure confirmées
            </label>
            <input
              type="datetime-local"
              name="confirmed_datetime"
              :value="booking.confirmed_datetime || ''"
              :min="new Date().toISOString().slice(0, 16)"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Notes complémentaires
            </label>
            <textarea
              name="provider_notes"
              :value="booking.provider_notes || ''"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
              placeholder="Précisions sur le service, matériel nécessaire..."
            ></textarea>
          </div>
        </div>
        
        <div class="flex space-x-3">
          <button
            type="button"
            @click="showQuoteModal = false"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
          >
            Annuler
          </button>
          <button
            type="submit"
            :disabled="processing"
            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            {{ processing ? 'Envoi...' : 'Envoyer le devis' }}
          </button>
        </div>
      </Form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Form } from '@inertiajs/vue3'

defineProps({
  booking: Object,
  showActions: {
    type: Boolean,
    default: false
  }
})

// Modal state
const showRejectModal = ref(false)
const showQuoteModal = ref(false)
</script>