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
  filters: Object
})
</script>