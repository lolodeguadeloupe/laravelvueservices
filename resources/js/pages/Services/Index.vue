<template>
  <AppLayout>
    <!-- Header avec recherche -->
    <section class="bg-gradient-to-br from-primary/5 to-secondary/5 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
          <h1 class="text-4xl font-bold text-gray-900 mb-4">
            Découvrez nos services
          </h1>
          <p class="text-xl text-gray-600">
            {{ services.total }} services disponibles près de chez vous
          </p>
        </div>

        <!-- Barre de recherche -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-4 max-w-4xl mx-auto">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Recherche service -->
            <div class="relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              <input
                v-model="searchForm.search"
                type="text"
                placeholder="Service recherché..."
                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                @keyup.enter="applyFilters"
              >
            </div>

            <!-- Catégorie -->
            <select
              v-model="searchForm.category"
              class="w-full py-3 px-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
              @change="applyFilters"
            >
              <option value="">Toutes catégories</option>
              <option v-for="category in categories" :key="category.id" :value="category.slug">
                {{ category.name }}
              </option>
            </select>

            <!-- Localisation -->
            <div class="relative">
              <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              </svg>
              <input
                v-model="searchForm.location"
                type="text"
                placeholder="Ville, code postal..."
                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent"
                @keyup.enter="applyFilters"
              >
            </div>

            <!-- Bouton recherche -->
            <button
              @click="applyFilters"
              class="bg-primary text-white py-3 px-6 rounded-xl font-semibold hover:bg-primary-dark transition-colors"
            >
              Rechercher
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Contenu principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar filtres -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sticky top-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Filtres</h3>

            <!-- Catégories populaires -->
            <div class="mb-8">
              <h4 class="text-md font-medium text-gray-700 mb-4">Catégories populaires</h4>
              <div class="space-y-2">
                <div
                  v-for="featuredCategory in featuredCategories"
                  :key="featuredCategory.slug"
                  @click="filterByCategory(featuredCategory.slug)"
                  class="flex items-center p-3 rounded-lg hover:bg-primary/5 cursor-pointer transition-colors group"
                  :class="{ 'bg-primary/10 border border-primary/20': searchForm.category === featuredCategory.slug }"
                >
                  <span class="text-2xl mr-3">{{ featuredCategory.icon }}</span>
                  <div>
                    <div class="font-medium text-gray-900 group-hover:text-primary">
                      {{ featuredCategory.name }}
                    </div>
                    <div class="text-sm text-gray-500">{{ featuredCategory.subtitle }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Filtres prix -->
            <div class="mb-6">
              <h4 class="text-md font-medium text-gray-700 mb-4">Prix</h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Prix minimum</label>
                  <input
                    v-model.number="searchForm.price_min"
                    type="number"
                    placeholder="0"
                    min="0"
                    class="w-full py-2 px-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    @change="applyFilters"
                  >
                </div>
                <div>
                  <label class="block text-sm text-gray-600 mb-1">Prix maximum</label>
                  <input
                    v-model.number="searchForm.price_max"
                    type="number"
                    placeholder="1000"
                    min="0"
                    class="w-full py-2 px-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                    @change="applyFilters"
                  >
                </div>
              </div>
            </div>

            <!-- Bouton reset filtres -->
            <button
              @click="resetFilters"
              class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Réinitialiser les filtres
            </button>
          </div>
        </div>

        <!-- Grille des services -->
        <div class="lg:col-span-3">
          <!-- En-tête avec compteur -->
          <div class="flex justify-between items-center mb-6">
            <div>
              <h2 class="text-2xl font-semibold text-gray-900">
                Services disponibles
              </h2>
              <p class="text-gray-600">
                {{ services.total }} résultat{{ services.total > 1 ? 's' : '' }} trouvé{{ services.total > 1 ? 's' : '' }}
              </p>
            </div>
            
            <!-- Tri -->
            <div class="flex items-center space-x-2">
              <label class="text-sm text-gray-600">Trier par :</label>
              <select
                v-model="sortBy"
                @change="applyFilters"
                class="border border-gray-200 rounded-lg py-2 px-3 focus:ring-2 focus:ring-primary"
              >
                <option value="recent">Plus récents</option>
                <option value="popular">Plus populaires</option>
                <option value="price_asc">Prix croissant</option>
                <option value="price_desc">Prix décroissant</option>
                <option value="rating">Mieux notés</option>
              </select>
            </div>
          </div>

          <!-- Grille des services -->
          <div v-if="services.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <div
              v-for="service in services.data"
              :key="service.id"
              class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-200 overflow-hidden group cursor-pointer"
              @click="goToService(service.id)"
            >
              <!-- Image du service -->
              <div class="relative h-48 bg-gradient-to-br from-primary/10 to-secondary/10">
                <img
                  v-if="service.images && service.images.length > 0"
                  :src="service.images[0].path"
                  :alt="service.title"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                >
                <div v-else class="w-full h-full flex items-center justify-center">
                  <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>

                <!-- Badge catégorie -->
                <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
                  <span class="text-xs font-medium text-primary">{{ service.category?.name }}</span>
                </div>

                <!-- Badge prix -->
                <div class="absolute top-3 right-3 bg-primary text-white rounded-full px-3 py-1">
                  <span class="text-sm font-semibold">{{ formatPriceRange(service) }}</span>
                </div>
              </div>

              <!-- Contenu de la carte -->
              <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2 group-hover:text-primary transition-colors">
                  {{ service.title }}
                </h3>
                
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                  {{ service.description }}
                </p>

                <!-- Informations prestataire -->
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-primary/20 rounded-full flex items-center justify-center">
                      <span class="text-primary font-semibold text-sm">
                        {{ service.provider?.profile?.first_name?.charAt(0) }}{{ service.provider?.profile?.last_name?.charAt(0) }}
                      </span>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">
                        {{ service.provider?.profile?.first_name }} {{ service.provider?.profile?.last_name }}
                      </p>
                      <p class="text-xs text-gray-500">
                        {{ service.provider?.profile?.city }}
                      </p>
                    </div>
                  </div>

                  <!-- Rating -->
                  <div v-if="service.reviews_count > 0" class="flex items-center">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-sm text-gray-600 ml-1">
                      {{ (service.reviews_avg_rating || 0).toFixed(1) }}
                      <span class="text-xs text-gray-400">({{ service.reviews_count }})</span>
                    </span>
                  </div>
                  <div v-else class="text-xs text-gray-400">
                    Nouveau
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- État vide -->
          <div v-else class="text-center py-16">
            <svg class="w-24 h-24 text-gray-300 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Aucun service trouvé</h3>
            <p class="text-gray-600 mb-6">
              Essayez de modifier vos critères de recherche ou de supprimer certains filtres.
            </p>
            <button
              @click="resetFilters"
              class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary-dark transition-colors"
            >
              Réinitialiser les filtres
            </button>
          </div>

          <!-- Pagination -->
          <div v-if="services.data.length > 0 && services.last_page > 1" class="mt-8">
            <nav class="flex justify-center">
              <div class="flex items-center space-x-2">
                <!-- Page précédente -->
                <Link
                  v-if="services.current_page > 1"
                  :href="services.prev_page_url"
                  class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Précédent
                </Link>

                <!-- Numéros de pages -->
                <div class="flex items-center space-x-1">
                  <Link
                    v-for="page in getPaginationPages"
                    :key="page"
                    :href="getPaginationUrl(page)"
                    class="px-4 py-2 rounded-lg transition-colors"
                    :class="{
                      'bg-primary text-white': page === services.current_page,
                      'border border-gray-200 hover:bg-gray-50': page !== services.current_page
                    }"
                  >
                    {{ page }}
                  </Link>
                </div>

                <!-- Page suivante -->
                <Link
                  v-if="services.current_page < services.last_page"
                  :href="services.next_page_url"
                  class="px-4 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  Suivant
                </Link>
              </div>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from '@/utils/routes'
import AppLayout from '@/layouts/AppLayout.vue'

// Props
const props = defineProps({
  services: Object,
  categories: Array,
  featuredCategories: Array,
  filters: Object
})

// State
const searchForm = ref({
  search: props.filters.search || '',
  category: props.filters.category || '',
  location: props.filters.location || '',
  price_min: props.filters.price_min || '',
  price_max: props.filters.price_max || ''
})

const sortBy = ref('recent')

// Computed
const getPaginationPages = computed(() => {
  const current = props.services.current_page
  const last = props.services.last_page
  const pages = []
  
  // Logique pour afficher les pages autour de la page courante
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    pages.push(i)
  }
  
  return pages
})

// Methods
const formatPrice = (price) => {
  if (!price) return 'Sur devis'
  return `${price}€`
}

const formatPriceRange = (service) => {
  if (!service.price_min && !service.price_max) return 'Sur devis'
  if (service.price_min === service.price_max) return `${service.price_min}€`
  if (!service.price_max) return `Dès ${service.price_min}€`
  return `${service.price_min}-${service.price_max}€`
}

const applyFilters = () => {
  const params = new URLSearchParams()
  
  if (searchForm.value.search) params.append('search', searchForm.value.search)
  if (searchForm.value.category) params.append('category', searchForm.value.category)
  if (searchForm.value.location) params.append('location', searchForm.value.location)
  if (searchForm.value.price_min) params.append('price_min', searchForm.value.price_min)
  if (searchForm.value.price_max) params.append('price_max', searchForm.value.price_max)
  if (sortBy.value !== 'recent') params.append('sort', sortBy.value)
  
  router.get(route('services.index'), Object.fromEntries(params), {
    preserveState: true,
    preserveScroll: true
  })
}

const filterByCategory = (slug) => {
  searchForm.value.category = searchForm.value.category === slug ? '' : slug
  applyFilters()
}

const resetFilters = () => {
  searchForm.value = {
    search: '',
    category: '',
    location: '',
    price_min: '',
    price_max: ''
  }
  sortBy.value = 'recent'
  router.get(route('services.index'))
}

const goToService = (serviceId) => {
  router.visit(route('services.show', { service: serviceId }))
}

const getPaginationUrl = (page) => {
  const params = new URLSearchParams()
  
  if (searchForm.value.search) params.append('search', searchForm.value.search)
  if (searchForm.value.category) params.append('category', searchForm.value.category)
  if (searchForm.value.location) params.append('location', searchForm.value.location)
  if (searchForm.value.price_min) params.append('price_min', searchForm.value.price_min)
  if (searchForm.value.price_max) params.append('price_max', searchForm.value.price_max)
  if (sortBy.value !== 'recent') params.append('sort', sortBy.value)
  params.append('page', page)
  
  return `${route('services.index')}?${params.toString()}`
}
</script>

<style scoped>
.line-clamp-2 {
  overflow: hidden;
  display: -webkit-box;
  -webkit-box-orient: vertical;
  -webkit-line-clamp: 2;
}
</style>