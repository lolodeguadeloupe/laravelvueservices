<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- En-tête -->
      <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Services disponibles</h1>
              <p class="text-gray-600 mt-2">Découvrez tous les services proposés par nos prestataires qualifiés</p>
            </div>
            <div class="text-sm text-gray-500">
              {{ services.total }} service{{ services.total > 1 ? 's' : '' }} disponible{{ services.total > 1 ? 's' : '' }}
            </div>
          </div>

          <!-- Grille des catégories principales -->
          <div class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
              <Link
                v-for="category in featuredCategories"
                :key="category.slug"
                :href="route('services.index', { category: category.slug })"
                :class="category.cssClass"
                class="relative overflow-hidden rounded-2xl h-32 flex flex-col justify-between p-6 transition-transform duration-200 hover:scale-105 hover:shadow-lg group"
              >
                <div class="flex items-start justify-between">
                  <div>
                    <h3 class="text-xl font-bold leading-tight">{{ category.name }}</h3>
                    <p class="text-sm opacity-90 mt-1">{{ category.subtitle }}</p>
                  </div>
                  <span class="text-2xl">{{ category.icon }}</span>
                </div>
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-opacity duration-200"></div>
              </Link>
            </div>

            <!-- Section Services populaires -->
            <div class="mt-12">
              <h2 class="text-2xl font-bold text-gray-900 mb-6">Services à domicile populaires</h2>
              <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-green-100 to-green-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-green-300">
                  <div class="h-full flex items-center justify-center text-green-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">🌱</div>
                      <p class="font-semibold text-lg">Jardinage</p>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-blue-300">
                  <div class="h-full flex items-center justify-center text-blue-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">💅</div>
                      <p class="font-semibold text-lg">Manucure</p>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-purple-300">
                  <div class="h-full flex items-center justify-center text-purple-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">🎨</div>
                      <p class="font-semibold text-lg">Art thérapie</p>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-pink-100 to-pink-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-pink-300">
                  <div class="h-full flex items-center justify-center text-pink-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">🧹</div>
                      <p class="font-semibold text-lg">Ménage et repassage</p>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-yellow-300">
                  <div class="h-full flex items-center justify-center text-yellow-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">👶</div>
                      <p class="font-semibold text-lg">Garde d'enfants</p>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-teal-100 to-teal-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-teal-300">
                  <div class="h-full flex items-center justify-center text-teal-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">✂️</div>
                      <p class="font-semibold text-lg">Coiffure à domicile</p>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-rose-100 to-rose-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-rose-300">
                  <div class="h-full flex items-center justify-center text-rose-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">💄</div>
                      <p class="font-semibold text-lg">Beauté à domicile</p>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-indigo-300">
                  <div class="h-full flex items-center justify-center text-indigo-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">💆</div>
                      <p class="font-semibold text-lg">Massage à domicile</p>
                    </div>
                  </div>
                </div>
                <div class="bg-gradient-to-br from-amber-100 to-amber-200 rounded-2xl h-40 relative overflow-hidden group cursor-pointer transition-transform duration-200 hover:scale-105 border border-amber-300">
                  <div class="h-full flex items-center justify-center text-amber-800">
                    <div class="text-center">
                      <div class="text-4xl mb-2">🏃</div>
                      <p class="font-semibold text-lg">Coach à domicile</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Filtres -->
          <div class="bg-gray-50 rounded-lg p-6">
            <Form
              :action="route('services.index')"
              method="get"
              #default="{ processing }"
              preserve-scroll
              preserve-state
            >
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <!-- Recherche -->
                <div class="lg:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                  <input
                    type="text"
                    name="search"
                    :value="filters.search"
                    placeholder="Nom du service, description..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                  />
                </div>
                
                <!-- Catégorie -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Catégorie</label>
                  <select
                    name="category"
                    :value="filters.category"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                  >
                    <option value="">Toutes les catégories</option>
                    <option
                      v-for="category in categories"
                      :key="category.id"
                      :value="category.slug"
                    >
                      {{ category.name }}
                    </option>
                  </select>
                </div>
                
                <!-- Localisation -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Localisation</label>
                  <input
                    type="text"
                    name="location"
                    :value="filters.location"
                    placeholder="Ville, code postal..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                  />
                </div>
                
                <!-- Bouton recherche -->
                <div class="flex items-end">
                  <button
                    type="submit"
                    :disabled="processing"
                    class="w-full px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50"
                  >
                    Filtrer
                  </button>
                </div>
              </div>
              
              <!-- Filtres de prix -->
              <div class="mt-4 flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <label class="text-sm font-medium text-gray-700">Prix min :</label>
                  <input
                    type="number"
                    name="price_min"
                    :value="filters.price_min"
                    placeholder="0"
                    min="0"
                    step="5"
                    class="w-24 px-3 py-1 border border-gray-300 rounded focus:ring-primary focus:border-primary text-sm"
                  />
                  <span class="text-sm text-gray-500">€</span>
                </div>
                
                <div class="flex items-center gap-2">
                  <label class="text-sm font-medium text-gray-700">Prix max :</label>
                  <input
                    type="number"
                    name="price_max"
                    :value="filters.price_max"
                    placeholder="1000"
                    min="0"
                    step="5"
                    class="w-24 px-3 py-1 border border-gray-300 rounded focus:ring-primary focus:border-primary text-sm"
                  />
                  <span class="text-sm text-gray-500">€</span>
                </div>
              </div>
            </Form>
          </div>
        </div>
      </div>

      <!-- Liste des services -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div v-if="services.data.length === 0" class="text-center py-16">
          <svg class="w-24 h-24 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <h3 class="text-xl font-medium text-gray-900 mb-2">Aucun service trouvé</h3>
          <p class="text-gray-600 mb-6">Essayez de modifier vos critères de recherche</p>
          <Link
            :href="route('services.index')"
            class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors"
          >
            Voir tous les services
          </Link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <ServiceCard
            v-for="service in services.data"
            :key="service.id"
            :service="service"
          />
        </div>

        <!-- Pagination -->
        <div v-if="services.links.length > 3" class="mt-12">
          <nav class="flex justify-center">
            <div class="flex space-x-2">
              <Link
                v-for="(link, index) in services.links"
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
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Form } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ServiceCard from '@/components/ServiceCard.vue'
import { route } from '@/utils/routes'

defineProps({
  services: Object,
  categories: Array,
  featuredCategories: Array,
  filters: Object
})
</script>