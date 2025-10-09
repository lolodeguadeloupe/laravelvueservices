<template>
  <AppLayout>
    <div class="min-h-screen bg-gray-50">
      <!-- Header avec breadcrumb -->
      <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <nav class="flex items-center space-x-2 text-sm">
            <Link :href="route('home')" class="text-gray-500 hover:text-primary">Accueil</Link>
            <span class="text-gray-400">></span>
            <Link :href="route('services.index')" class="text-gray-500 hover:text-primary">Services</Link>
            <span class="text-gray-400">></span>
            <span class="text-gray-900">{{ service.title }}</span>
          </nav>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          
          <!-- Contenu principal -->
          <div class="lg:col-span-2 space-y-8">
            
            <!-- Galerie d'images -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
              <div class="relative">
                <div class="aspect-w-16 aspect-h-9 bg-gradient-to-br from-primary/10 to-secondary/10">
                  <img
                    v-if="service.images && service.images.length > 0"
                    :src="selectedImage"
                    :alt="service.title"
                    class="w-full h-96 object-cover"
                  >
                  <div v-else class="w-full h-96 flex items-center justify-center">
                    <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                  
                  <!-- Badge catégorie -->
                  <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-full px-4 py-2">
                    <span class="text-sm font-medium text-primary">{{ service.category?.name }}</span>
                  </div>
                </div>
                
                <!-- Miniatures -->
                <div v-if="service.images && service.images.length > 1" class="p-4 bg-gray-50">
                  <div class="flex space-x-3 overflow-x-auto">
                    <button
                      v-for="(image, index) in service.images"
                      :key="index"
                      @click="selectedImage = image.path"
                      class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden border-2 transition-colors"
                      :class="{ 'border-primary': selectedImage === image.path, 'border-gray-200': selectedImage !== image.path }"
                    >
                      <img :src="image.path" :alt="`Image ${index + 1}`" class="w-full h-full object-cover">
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Détails du service -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
              <div class="flex items-start justify-between mb-6">
                <div>
                  <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ service.title }}</h1>
                  <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      </svg>
                      {{ service.provider?.profile?.city }}
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      4.8 (156 avis)
                    </div>
                  </div>
                </div>
                
                <div class="text-right">
                  <div class="text-3xl font-bold text-primary">{{ formatPrice(service.price) }}</div>
                  <div class="text-sm text-gray-500">{{ service.price_type === 'hourly' ? 'par heure' : 'forfait' }}</div>
                </div>
              </div>

              <!-- Description -->
              <div class="prose max-w-none mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Description</h3>
                <p class="text-gray-700 leading-relaxed">{{ service.description }}</p>
              </div>

              <!-- Caractéristiques -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                  <h4 class="text-md font-semibold text-gray-900 mb-3">Détails du service</h4>
                  <ul class="space-y-2 text-sm text-gray-600">
                    <li class="flex items-center">
                      <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      Durée estimée : {{ service.duration || '2 heures' }}
                    </li>
                    <li class="flex items-center">
                      <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      Matériel fourni
                    </li>
                    <li class="flex items-center">
                      <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      Service assuré
                    </li>
                    <li class="flex items-center">
                      <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                      </svg>
                      Disponible en urgence
                    </li>
                  </ul>
                </div>
                
                <div>
                  <h4 class="text-md font-semibold text-gray-900 mb-3">Zone d'intervention</h4>
                  <div class="text-sm text-gray-600">
                    <p>{{ service.provider?.profile?.city }} et environs (15km)</p>
                    <p class="mt-2 text-xs">Frais de déplacement inclus dans le prix</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Avis clients -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">Avis clients</h3>
                <div class="flex items-center space-x-2">
                  <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-lg font-semibold text-gray-900 ml-1">4.8</span>
                  </div>
                  <span class="text-gray-500">(156 avis)</span>
                </div>
              </div>

              <!-- Exemple d'avis -->
              <div class="space-y-6">
                <div class="border-b border-gray-100 pb-6">
                  <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center">
                      <span class="text-primary font-semibold">S</span>
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center justify-between mb-2">
                        <div>
                          <p class="font-medium text-gray-900">Sophie Martin</p>
                          <div class="flex items-center mt-1">
                            <div class="flex">
                              <svg v-for="i in 5" :key="i" class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                              </svg>
                            </div>
                          </div>
                        </div>
                        <span class="text-sm text-gray-500">Il y a 3 jours</span>
                      </div>
                      <p class="text-gray-700">"Service excellent ! Très professionnel et ponctuel. Le travail a été fait rapidement et parfaitement. Je recommande vivement !"</p>
                    </div>
                  </div>
                </div>

                <div class="border-b border-gray-100 pb-6">
                  <div class="flex items-start space-x-4">
                    <div class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center">
                      <span class="text-primary font-semibold">M</span>
                    </div>
                    <div class="flex-1">
                      <div class="flex items-center justify-between mb-2">
                        <div>
                          <p class="font-medium text-gray-900">Marc Dubois</p>
                          <div class="flex items-center mt-1">
                            <div class="flex">
                              <svg v-for="i in 5" :key="i" class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                              </svg>
                            </div>
                          </div>
                        </div>
                        <span class="text-sm text-gray-500">Il y a 1 semaine</span>
                      </div>
                      <p class="text-gray-700">"Prestation de qualité, rapport qualité/prix excellent. À recommander sans hésitation."</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mt-6 text-center">
                <button class="text-primary hover:text-primary-dark font-medium">
                  Voir tous les avis
                </button>
              </div>
            </div>
          </div>

          <!-- Sidebar réservation -->
          <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sticky top-8">
              <!-- Profil prestataire -->
              <div class="text-center mb-6 pb-6 border-b border-gray-100">
                <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                  <span class="text-2xl font-bold text-primary">
                    {{ service.provider?.profile?.first_name?.charAt(0) }}{{ service.provider?.profile?.last_name?.charAt(0) }}
                  </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">
                  {{ service.provider?.profile?.first_name }} {{ service.provider?.profile?.last_name }}
                </h3>
                <p class="text-gray-600">{{ service.category?.name }}</p>
                
                <!-- Badges -->
                <div class="flex justify-center space-x-2 mt-3">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Vérifié
                  </span>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    Pro
                  </span>
                </div>
              </div>

              <!-- Prix et bouton réservation -->
              <div class="mb-6">
                <div class="text-center mb-4">
                  <div class="text-3xl font-bold text-primary">{{ formatPrice(service.price) }}</div>
                  <div class="text-sm text-gray-500">{{ service.price_type === 'hourly' ? 'par heure' : 'forfait' }}</div>
                </div>

                <button
                  @click="startBooking"
                  class="w-full bg-primary text-white py-4 px-6 rounded-xl font-semibold text-lg hover:bg-primary-dark transition-colors shadow-lg hover:shadow-xl"
                >
                  Réserver maintenant
                </button>
                
                <button class="w-full mt-3 border-2 border-gray-200 text-gray-700 py-3 px-6 rounded-xl font-semibold hover:border-primary hover:text-primary transition-colors">
                  Demander un devis
                </button>
              </div>

              <!-- Informations rapides -->
              <div class="space-y-4 mb-6">
                <div class="flex items-center text-sm text-gray-600">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Réponse sous 2h en moyenne
                </div>
                
                <div class="flex items-center text-sm text-gray-600">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Disponible 7j/7
                </div>
                
                <div class="flex items-center text-sm text-gray-600">
                  <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Assurance incluse
                </div>
              </div>

              <!-- Contact rapide -->
              <div class="border-t border-gray-100 pt-6">
                <button class="w-full flex items-center justify-center text-gray-700 py-3 px-4 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                  <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.456l-3.063 1.021c-.413.137-.86-.11-.86-.554v-3.46C3.26 14.652 3 13.35 3 12c0-4.418 3.582-8 8-8s8 3.582 8 8z" />
                  </svg>
                  Contacter par message
                </button>
              </div>
            </div>

            <!-- Services similaires -->
            <div v-if="similarServices && similarServices.length > 0" class="mt-8">
              <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Services similaires</h3>
                <div class="space-y-4">
                  <div
                    v-for="similarService in similarServices"
                    :key="similarService.id"
                    @click="goToService(similarService.id)"
                    class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
                  >
                    <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-lg flex items-center justify-center">
                      <img
                        v-if="similarService.images && similarService.images.length > 0"
                        :src="similarService.images[0].path"
                        :alt="similarService.title"
                        class="w-full h-full object-cover rounded-lg"
                      >
                      <svg v-else class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">{{ similarService.title }}</p>
                      <p class="text-xs text-gray-500">{{ similarService.provider?.profile?.first_name }} {{ similarService.provider?.profile?.last_name }}</p>
                      <p class="text-sm font-semibold text-primary">{{ formatPrice(similarService.price) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from '@/utils/routes'
import AppLayout from '@/layouts/AppLayout.vue'

// Props
const props = defineProps({
  service: Object,
  similarServices: Array
})

// State
const selectedImage = ref('')

// Computed
onMounted(() => {
  if (props.service.images && props.service.images.length > 0) {
    selectedImage.value = props.service.images[0].path
  }
})

// Methods
const formatPrice = (price) => {
  if (!price) return 'Sur devis'
  return `${price}€`
}

const startBooking = () => {
  router.visit(route('bookings.create', { service: props.service.id }))
}

const goToService = (serviceId) => {
  router.visit(route('services.show', { service: serviceId }))
}
</script>