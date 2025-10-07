<template>
  <ProviderLayout>
    <div class="max-w-6xl mx-auto p-6">
      <!-- En-tête -->
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Mes Services</h1>
          <p class="text-gray-600 mt-1">Gérez vos services et leurs disponibilités</p>
        </div>
        <Link
          :href="route('provider.services.create')"
          class="bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Ajouter un service
        </Link>
      </div>

      <!-- Filtres -->
      <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
            <input
              v-model="form.search"
              type="text"
              placeholder="Titre ou description..."
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
            >
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
            <select
              v-model="form.category"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
            >
              <option value="">Toutes les catégories</option>
              <option
                v-for="category in categories"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
            <select
              v-model="form.status"
              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
            >
              <option value="">Tous les statuts</option>
              <option value="active">Actif</option>
              <option value="inactive">Inactif</option>
            </select>
          </div>
          <div class="flex items-end">
            <button
              type="submit"
              class="w-full bg-secondary hover:bg-secondary/90 text-white px-4 py-2 rounded-lg font-medium transition-colors"
            >
              Filtrer
            </button>
          </div>
        </form>
      </div>

      <!-- Liste des services -->
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div
          v-for="service in services.data"
          :key="service.id"
          class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow"
        >
          <!-- Image -->
          <div class="h-48 bg-gray-100 relative">
            <img
              v-if="service.images && service.images.length > 0"
              :src="`/storage/${service.images[0]}`"
              :alt="service.title"
              class="w-full h-full object-cover"
            >
            <div v-else class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
              <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
            </div>
            
            <!-- Badge de statut -->
            <div class="absolute top-3 right-3">
              <span
                :class="service.is_active
                  ? 'bg-green-100 text-green-800'
                  : 'bg-red-100 text-red-800'"
                class="px-2 py-1 rounded-full text-xs font-medium"
              >
                {{ service.is_active ? 'Actif' : 'Inactif' }}
              </span>
            </div>
          </div>

          <!-- Contenu -->
          <div class="p-6">
            <div class="flex justify-between items-start mb-3">
              <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">{{ service.title }}</h3>
              <span class="text-lg font-bold text-primary">{{ formatPrice(service.price, service.price_type) }}</span>
            </div>
            
            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ service.description }}</p>
            
            <div class="flex items-center gap-2 mb-4">
              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary/20 text-secondary">
                {{ service.category?.name }}
              </span>
              <span class="text-xs text-gray-500">{{ service.booking_requests_count }} demande(s)</span>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
              <div class="flex gap-2">
                <Link
                  :href="route('provider.services.show', service.id)"
                  class="text-gray-500 hover:text-gray-700 p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
                  title="Voir"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                  </svg>
                </Link>
                <Link
                  :href="route('provider.services.edit', service.id)"
                  class="text-blue-500 hover:text-blue-700 p-1.5 rounded-lg hover:bg-blue-50 transition-colors"
                  title="Modifier"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                  </svg>
                </Link>
              </div>
              
              <button
                @click="toggleStatus(service)"
                :class="service.is_active
                  ? 'text-red-500 hover:text-red-700 hover:bg-red-50'
                  : 'text-green-500 hover:text-green-700 hover:bg-green-50'"
                class="p-1.5 rounded-lg transition-colors"
                :title="service.is_active ? 'Désactiver' : 'Activer'"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path
                    v-if="service.is_active"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L5.636 5.636"
                  />
                  <path
                    v-else
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Message si aucun service -->
      <div v-if="services.data.length === 0" class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun service trouvé</h3>
        <p class="text-gray-600 mb-4">Commencez par créer votre premier service</p>
        <Link
          :href="route('provider.services.create')"
          class="bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-lg font-medium inline-flex items-center gap-2 transition-colors"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
          </svg>
          Créer mon premier service
        </Link>
      </div>

      <!-- Pagination -->
      <div v-if="services.data.length > 0" class="mt-8">
        <div class="flex justify-between items-center">
          <p class="text-sm text-gray-600">
            Affichage de {{ services.from }} à {{ services.to }} sur {{ services.total }} résultats
          </p>
          <div class="flex gap-2">
            <Link
              v-if="services.prev_page_url"
              :href="services.prev_page_url"
              class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Précédent
            </Link>
            <Link
              v-if="services.next_page_url"
              :href="services.next_page_url"
              class="px-3 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90"
            >
              Suivant
            </Link>
          </div>
        </div>
      </div>
    </div>
  </ProviderLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import ProviderLayout from '@/layouts/ProviderLayout.vue'

// Props
defineProps({
  services: {
    type: Object,
    required: true
  },
  categories: {
    type: Array,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Form pour les filtres
const form = reactive({
  search: usePage().props.filters?.search || '',
  category: usePage().props.filters?.category || '',
  status: usePage().props.filters?.status || ''
})

// Méthodes
const applyFilters = () => {
  router.get(route('provider.services.index'), form, {
    preserveState: true,
    replace: true
  })
}

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

const toggleStatus = (service) => {
  router.patch(route('provider.services.toggle-status', service.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      service.is_active = !service.is_active
    }
  })
}
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>