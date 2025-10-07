<template>
  <AppLayout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- En-tête -->
      <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Avis clients</h1>
        <p class="text-gray-600">Découvrez les expériences de notre communauté</p>
      </div>

      <!-- Filtres -->
      <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Filtres</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <!-- Filtre par note -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Note minimum</label>
            <select
              v-model="filters.rating"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
            >
              <option value="">Toutes les notes</option>
              <option value="5">5 étoiles</option>
              <option value="4">4+ étoiles</option>
              <option value="3">3+ étoiles</option>
              <option value="2">2+ étoiles</option>
              <option value="1">1+ étoile</option>
            </select>
          </div>

          <!-- Filtre vérifié -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
            <select
              v-model="filters.verified"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
            >
              <option value="">Tous les avis</option>
              <option value="true">Avis vérifiés</option>
              <option value="false">Avis non vérifiés</option>
            </select>
          </div>

          <!-- Filtre mis en avant -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
            <select
              v-model="filters.featured"
              @change="applyFilters"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
            >
              <option value="">Tous</option>
              <option value="true">Mis en avant</option>
            </select>
          </div>

          <!-- Bouton reset -->
          <div class="flex items-end">
            <button
              @click="resetFilters"
              class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Réinitialiser
            </button>
          </div>
        </div>
      </div>

      <!-- Liste des avis -->
      <div class="space-y-6">
        <div
          v-for="review in reviews.data"
          :key="review.id"
          class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-shadow"
        >
          <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-6">
            <!-- Informations du service -->
            <div class="lg:w-1/4 mb-4 lg:mb-0">
              <h3 class="font-semibold text-gray-900 mb-2">{{ review.booking_request.service.title }}</h3>
              <p class="text-sm text-gray-600 mb-2">{{ review.booking_request.service.short_description }}</p>
              
              <!-- Badge vérifié -->
              <div v-if="review.is_verified" class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Vérifié
              </div>
            </div>

            <!-- Contenu de l'avis -->
            <div class="lg:flex-1">
              <!-- En-tête de l'avis -->
              <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                  <!-- Avatar du reviewer -->
                  <div class="w-10 h-10 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium">
                    {{ getInitials(review.reviewer) }}
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">
                      {{ review.reviewer.profile?.first_name || 'Utilisateur' }} 
                      {{ review.reviewer.profile?.last_name?.charAt(0) || '' }}.
                    </p>
                    <p class="text-sm text-gray-500">{{ formatDate(review.created_at) }}</p>
                  </div>
                </div>

                <!-- Note globale -->
                <div class="flex items-center space-x-2">
                  <div class="flex">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      class="w-5 h-5"
                      :class="star <= review.overall_rating ? 'text-yellow-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="text-sm font-medium text-gray-600">{{ review.overall_rating }}/5</span>
                </div>
              </div>

              <!-- Titre de l'avis -->
              <h4 v-if="review.title" class="font-semibold text-gray-900 mb-2">{{ review.title }}</h4>

              <!-- Commentaire -->
              <p class="text-gray-700 mb-4">{{ review.comment }}</p>

              <!-- Photos -->
              <div v-if="review.photos && review.photos.length" class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-4">
                <img
                  v-for="(photo, index) in review.photos.slice(0, 4)"
                  :key="index"
                  :src="`/storage/${photo}`"
                  :alt="`Photo ${index + 1}`"
                  class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                  @click="openPhotoModal(review.photos, index)"
                />
              </div>

              <!-- Notes détaillées -->
              <div v-if="hasDetailedRatings(review)" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-4">
                <div v-if="review.quality_rating" class="text-sm">
                  <span class="text-gray-600">Qualité:</span>
                  <span class="ml-1 font-medium">{{ review.quality_rating }}/5</span>
                </div>
                <div v-if="review.communication_rating" class="text-sm">
                  <span class="text-gray-600">Communication:</span>
                  <span class="ml-1 font-medium">{{ review.communication_rating }}/5</span>
                </div>
                <div v-if="review.punctuality_rating" class="text-sm">
                  <span class="text-gray-600">Ponctualité:</span>
                  <span class="ml-1 font-medium">{{ review.punctuality_rating }}/5</span>
                </div>
                <div v-if="review.professionalism_rating" class="text-sm">
                  <span class="text-gray-600">Professionnalisme:</span>
                  <span class="ml-1 font-medium">{{ review.professionalism_rating }}/5</span>
                </div>
                <div v-if="review.value_rating" class="text-sm">
                  <span class="text-gray-600">Rapport qualité-prix:</span>
                  <span class="ml-1 font-medium">{{ review.value_rating }}/5</span>
                </div>
              </div>

              <!-- Réponse du prestataire -->
              <div v-if="review.response" class="bg-gray-50 rounded-lg p-4 mb-4">
                <div class="flex items-center space-x-2 mb-2">
                  <div class="w-6 h-6 bg-primary/20 text-primary rounded-full flex items-center justify-center text-xs font-medium">
                    {{ getInitials(review.reviewed) }}
                  </div>
                  <span class="text-sm font-medium text-gray-900">Réponse du prestataire</span>
                </div>
                <p class="text-sm text-gray-700">{{ review.response }}</p>
              </div>

              <!-- Actions -->
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <!-- Bouton utile -->
                  <button
                    @click="markAsHelpful(review)"
                    :disabled="isReacting"
                    class="flex items-center space-x-1 text-sm text-gray-600 hover:text-primary transition-colors disabled:opacity-50"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L9 6v4m-2 4h5.5M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                    </svg>
                    <span>Utile ({{ review.helpful_count || 0 }})</span>
                  </button>

                  <!-- Bouton signaler -->
                  <button
                    @click="openReportModal(review)"
                    class="flex items-center space-x-1 text-sm text-gray-600 hover:text-red-600 transition-colors"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.19 2.5 1.732 2.5z" />
                    </svg>
                    <span>Signaler</span>
                  </button>
                </div>

                <!-- Lien vers détail -->
                <Link
                  :href="route('reviews.show', review.id)"
                  class="text-sm text-primary hover:text-primary-dark transition-colors"
                >
                  Voir le détail →
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="reviews.links" class="mt-8">
        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
          <div class="flex flex-1 justify-between sm:hidden">
            <component
              :is="reviews.prev_page_url ? Link : 'span'"
              :href="reviews.prev_page_url"
              class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
              :class="{ 'cursor-not-allowed opacity-50': !reviews.prev_page_url }"
            >
              Précédent
            </component>
            <component
              :is="reviews.next_page_url ? Link : 'span'"
              :href="reviews.next_page_url"
              class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
              :class="{ 'cursor-not-allowed opacity-50': !reviews.next_page_url }"
            >
              Suivant
            </component>
          </div>
          <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Affichage de
                <span class="font-medium">{{ reviews.from || 0 }}</span>
                à
                <span class="font-medium">{{ reviews.to || 0 }}</span>
                sur
                <span class="font-medium">{{ reviews.total || 0 }}</span>
                avis
              </p>
            </div>
            <div class="flex space-x-1">
              <component
                v-for="(link, index) in reviews.links"
                :key="index"
                :is="link.url ? Link : 'span'"
                :href="link.url"
                :class="{
                  'relative inline-flex items-center px-4 py-2 text-sm font-medium': true,
                  'bg-primary text-white border-primary': link.active,
                  'bg-white text-gray-500 border-gray-300 hover:bg-gray-50': !link.active && link.url,
                  'bg-white text-gray-300 border-gray-300 cursor-not-allowed': !link.url
                }"
                class="border rounded-md"
                v-html="link.label"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- État vide -->
      <div v-if="!reviews.data || reviews.data.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10m0 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V8m10 0H7m6 4h.01M13 16h.01" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun avis trouvé</h3>
        <p class="mt-1 text-sm text-gray-500">
          {{ hasFilters ? 'Essayez de modifier vos filtres.' : 'Les premiers avis apparaîtront ici bientôt.' }}
        </p>
      </div>
    </div>

    <!-- Modal de signalement -->
    <div v-if="showReportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-md w-full p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Signaler cet avis</h3>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Motif du signalement</label>
          <select
            v-model="reportForm.reason"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
          >
            <option value="">Choisir un motif</option>
            <option value="inappropriate">Contenu inapproprié</option>
            <option value="fake">Avis fictif</option>
            <option value="spam">Spam</option>
            <option value="offensive">Contenu offensant</option>
            <option value="other">Autre</option>
          </select>
        </div>
        <div class="flex space-x-3">
          <button
            @click="submitReport"
            :disabled="!reportForm.reason || isReporting"
            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ isReporting ? 'Signalement...' : 'Signaler' }}
          </button>
          <button
            @click="closeReportModal"
            class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
          >
            Annuler
          </button>
        </div>
      </div>
    </div>

    <!-- Modal photos -->
    <div v-if="showPhotoModal" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-50 p-4" @click="closePhotoModal">
      <div class="max-w-4xl max-h-full">
        <img
          :src="`/storage/${selectedPhotos[currentPhotoIndex]}`"
          :alt="`Photo ${currentPhotoIndex + 1}`"
          class="max-w-full max-h-full object-contain"
        />
        <div v-if="selectedPhotos.length > 1" class="flex justify-center mt-4 space-x-2">
          <button
            v-for="(photo, index) in selectedPhotos"
            :key="index"
            @click.stop="currentPhotoIndex = index"
            class="w-3 h-3 rounded-full"
            :class="index === currentPhotoIndex ? 'bg-white' : 'bg-white/50'"
          />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
  reviews: Object,
  filters: Object,
})

// État local
const filters = ref({
  rating: props.filters?.rating || '',
  verified: props.filters?.verified || '',
  featured: props.filters?.featured || '',
})

const isReacting = ref(false)
const showReportModal = ref(false)
const selectedReview = ref(null)
const reportForm = ref({
  reason: '',
})
const isReporting = ref(false)

const showPhotoModal = ref(false)
const selectedPhotos = ref([])
const currentPhotoIndex = ref(0)

// Computed
const hasFilters = computed(() => {
  return filters.value.rating || filters.value.verified || filters.value.featured
})

// Méthodes
const getInitials = (user) => {
  if (!user?.profile) return 'U'
  const first = user.profile.first_name?.charAt(0) || ''
  const last = user.profile.last_name?.charAt(0) || ''
  return (first + last).toUpperCase() || 'U'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const hasDetailedRatings = (review) => {
  return review.quality_rating || review.communication_rating || 
         review.punctuality_rating || review.professionalism_rating || 
         review.value_rating
}

const applyFilters = () => {
  router.get(route('reviews.index'), filters.value, {
    preserveState: true,
    preserveScroll: true,
  })
}

const resetFilters = () => {
  filters.value = {
    rating: '',
    verified: '',
    featured: '',
  }
  applyFilters()
}

const markAsHelpful = async (review) => {
  if (isReacting.value) return
  
  isReacting.value = true
  
  try {
    const response = await fetch(`/reviews/${review.id}/helpful`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })
    
    const result = await response.json()
    
    if (response.ok && result.success) {
      // Mettre à jour le compteur
      review.helpful_count = result.helpful_count
    } else {
      alert(result.error || 'Une erreur est survenue')
    }
  } catch (error) {
    console.error('Erreur:', error)
    alert('Une erreur est survenue')
  } finally {
    isReacting.value = false
  }
}

const openReportModal = (review) => {
  selectedReview.value = review
  showReportModal.value = true
}

const closeReportModal = () => {
  showReportModal.value = false
  selectedReview.value = null
  reportForm.value.reason = ''
}

const submitReport = async () => {
  if (!reportForm.value.reason || isReporting.value) return
  
  isReporting.value = true
  
  try {
    const response = await fetch(`/reviews/${selectedReview.value.id}/report`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify(reportForm.value),
    })
    
    const result = await response.json()
    
    if (response.ok && result.success) {
      alert(result.message)
      closeReportModal()
    } else {
      alert(result.error || 'Une erreur est survenue')
    }
  } catch (error) {
    console.error('Erreur:', error)
    alert('Une erreur est survenue')
  } finally {
    isReporting.value = false
  }
}

const openPhotoModal = (photos, index) => {
  selectedPhotos.value = photos
  currentPhotoIndex.value = index
  showPhotoModal.value = true
}

const closePhotoModal = () => {
  showPhotoModal.value = false
  selectedPhotos.value = []
  currentPhotoIndex.value = 0
}
</script>