<template>
  <ProviderLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- En-tête -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Mes réservations</h1>
        <p class="text-gray-600">Gérez toutes vos demandes de service et réservations</p>
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
          :action="route('provider.bookings')"
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
                placeholder="Rechercher par service ou client..."
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

      <!-- Nouvelles demandes (prioritaires) -->
      <div v-if="pendingBookings.length > 0" class="mb-8">
        <div class="flex items-center mb-4">
          <h2 class="text-xl font-semibold text-gray-900">Nouvelles demandes</h2>
          <span class="ml-2 bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium">
            {{ pendingBookings.length }}
          </span>
        </div>
        
        <div class="space-y-4">
          <div
            v-for="booking in pendingBookings"
            :key="booking.id"
            class="bg-white rounded-lg shadow-sm border-l-4 border-l-yellow-400 border border-gray-200 p-6"
          >
            <BookingCard :booking="booking" show-actions />
          </div>
        </div>
      </div>

      <!-- Toutes les réservations -->
      <div class="space-y-4">
        <h2 v-if="pendingBookings.length > 0" class="text-xl font-semibold text-gray-900">Toutes les réservations</h2>
        
        <div v-if="bookings.data.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
          <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune réservation trouvée</h3>
          <p class="text-gray-600 mb-4">Vous n'avez pas encore reçu de demande de service.</p>
          <Link
            :href="route('provider.services.index')"
            class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors"
          >
            Gérer mes services
          </Link>
        </div>

        <div
          v-for="booking in bookings.data"
          :key="booking.id"
          class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
        >
          <BookingCard :booking="booking" :show-actions="booking.status === 'pending'" />
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
  </ProviderLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Form } from '@inertiajs/vue3'
import ProviderLayout from '@/layouts/ProviderLayout.vue'
import BookingCard from '@/components/BookingCard.vue'

const props = defineProps({
  bookings: Object,
  stats: Object,
  filters: Object
})

const pendingBookings = computed(() => {
  return props.bookings.data.filter(booking => booking.status === 'pending')
})
</script>