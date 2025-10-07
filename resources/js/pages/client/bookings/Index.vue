<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- En-tête -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes demandes de service</h1>
        <p class="text-gray-600">Suivez l'état de vos demandes et gérez vos réservations</p>
      </div>

      <!-- Statistiques -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
              <p class="text-sm text-gray-600">Total</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-lg flex items-center justify-center mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.pending }}</p>
              <p class="text-sm text-gray-600">En attente</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.accepted }}</p>
              <p class="text-sm text-gray-600">Acceptées</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
          <div class="flex items-center">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-4">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <div>
              <p class="text-2xl font-bold text-gray-900">{{ stats.completed }}</p>
              <p class="text-sm text-gray-600">Terminées</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Filtres -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <Form
          :action="route('bookings.index')"
          method="get"
          #default="{ processing }"
          preserve-scroll
          preserve-state
        >
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <input
                type="text"
                name="search"
                :value="filters.search"
                placeholder="Rechercher par service..."
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
              />
            </div>
            
            <div>
              <select
                name="status"
                :value="filters.status"
                class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
              >
                <option value="">Tous les statuts</option>
                <option value="pending">En attente</option>
                <option value="accepted">Acceptées</option>
                <option value="completed">Terminées</option>
                <option value="cancelled">Annulées</option>
                <option value="rejected">Refusées</option>
              </select>
            </div>
            
            <button
              type="submit"
              :disabled="processing"
              class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50"
            >
              Filtrer
            </button>
          </div>
        </Form>
      </div>

      <!-- Liste des réservations -->
      <div class="space-y-4">
        <div v-if="bookings.data.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune demande trouvée</h3>
          <p class="text-gray-600 mb-4">Vous n'avez pas encore fait de demande de service.</p>
          <Link
            :href="route('home')"
            class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors"
          >
            Découvrir les services
          </Link>
        </div>

        <div
          v-for="booking in bookings.data"
          :key="booking.id"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
        >
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
                    {{ new Date(booking.preferred_datetime).toLocaleString('fr-FR') }}
                  </p>
                </div>
                
                <div v-if="booking.confirmed_datetime">
                  <p class="text-sm text-gray-500 mb-1">Date confirmée</p>
                  <p class="text-sm font-medium text-gray-900">
                    {{ new Date(booking.confirmed_datetime).toLocaleString('fr-FR') }}
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
                      {{ booking.provider.profile?.first_name?.charAt(0) }}{{ booking.provider.profile?.last_name?.charAt(0) }}
                    </div>
                    <span class="text-sm text-gray-700">
                      {{ booking.provider.profile?.first_name }} {{ booking.provider.profile?.last_name }}
                    </span>
                  </div>
                  
                  <div v-if="booking.quoted_price" class="text-sm">
                    <span class="text-gray-500">Devis :</span>
                    <span class="font-semibold text-gray-900 ml-1">{{ booking.quoted_price }}€</span>
                  </div>
                </div>
                
                <div class="flex items-center space-x-2">
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
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="bookings.links.length > 3" class="mt-8">
        <nav class="flex justify-center">
          <div class="flex space-x-2">
            <Link
              v-for="(link, index) in bookings.links"
              :key="index"
              :href="link.url"
              v-html="link.label"
              :class="{
                'bg-primary text-white': link.active,
                'bg-white text-gray-700 hover:bg-gray-50': !link.active && link.url,
                'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url
              }"
              class="px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg transition-colors"
            />
          </div>
        </nav>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Form } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps({
  bookings: Object,
  stats: Object,
  filters: Object
})
</script>