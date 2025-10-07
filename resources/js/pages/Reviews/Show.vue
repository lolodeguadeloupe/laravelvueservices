<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Breadcrumb -->
      <div class="mb-6">
        <Link :href="route('reviews.index')" class="inline-flex items-center text-sm text-gray-500 hover:text-primary">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Retour aux avis
        </Link>
      </div>

      <!-- En-tête de l'avis -->
      <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
        <!-- Service concerné -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-xl font-semibold text-gray-900">{{ review.booking_request.service.title }}</h1>
              <p class="text-gray-600 mt-1">{{ review.booking_request.service.short_description }}</p>
            </div>
            <div v-if="review.is_verified" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
              <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              Avis vérifié
            </div>
          </div>
        </div>

        <!-- Contenu principal de l'avis -->
        <div class="p-6">
          <!-- Informations du reviewer -->
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium text-lg">
                {{ getInitials(review.reviewer) }}
              </div>
              <div>
                <h2 class="font-semibold text-gray-900">
                  {{ review.reviewer.profile?.first_name || 'Utilisateur' }} 
                  {{ review.reviewer.profile?.last_name?.charAt(0) || '' }}.
                </h2>
                <p class="text-gray-600">{{ formatDate(review.created_at) }}</p>
                <p v-if="review.reviewer_type === 'client'" class="text-sm text-gray-500">Client</p>
                <p v-else class="text-sm text-gray-500">Prestataire</p>
              </div>
            </div>

            <!-- Note globale -->
            <div class="text-center">
              <div class="flex items-center justify-center mb-1">
                <svg
                  v-for="star in 5"
                  :key="star"
                  class="w-6 h-6"
                  :class="star <= review.overall_rating ? 'text-yellow-400' : 'text-gray-300'"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </div>
              <span class="text-2xl font-bold text-gray-900">{{ review.overall_rating }}/5</span>
            </div>
          </div>

          <!-- Titre de l'avis -->
          <h3 v-if="review.title" class="text-xl font-semibold text-gray-900 mb-4">{{ review.title }}</h3>

          <!-- Commentaire -->
          <div class="prose max-w-none mb-6">
            <p class="text-gray-700 leading-relaxed">{{ review.comment }}</p>
          </div>

          <!-- Photos -->
          <div v-if="review.photos && review.photos.length" class="mb-6">
            <h4 class="font-medium text-gray-900 mb-3">Photos ({{ review.photos.length }})</h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
              <img
                v-for="(photo, index) in review.photos"
                :key="index"
                :src="`/storage/${photo}`"
                :alt="`Photo ${index + 1}`"
                class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity"
                @click="openPhotoModal(review.photos, index)"
              />
            </div>
          </div>

          <!-- Notes détaillées -->
          <div v-if="hasDetailedRatings" class="mb-6">
            <h4 class="font-medium text-gray-900 mb-4">Notes détaillées</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div v-if="review.quality_rating" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Qualité du service</span>
                <div class="flex items-center space-x-2">
                  <div class="flex">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      class="w-4 h-4"
                      :class="star <= review.quality_rating ? 'text-yellow-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="font-medium">{{ review.quality_rating }}</span>
                </div>
              </div>

              <div v-if="review.communication_rating" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Communication</span>
                <div class="flex items-center space-x-2">
                  <div class="flex">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      class="w-4 h-4"
                      :class="star <= review.communication_rating ? 'text-yellow-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="font-medium">{{ review.communication_rating }}</span>
                </div>
              </div>

              <div v-if="review.punctuality_rating" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Ponctualité</span>
                <div class="flex items-center space-x-2">
                  <div class="flex">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      class="w-4 h-4"
                      :class="star <= review.punctuality_rating ? 'text-yellow-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="font-medium">{{ review.punctuality_rating }}</span>
                </div>
              </div>

              <div v-if="review.professionalism_rating" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Professionnalisme</span>
                <div class="flex items-center space-x-2">
                  <div class="flex">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      class="w-4 h-4"
                      :class="star <= review.professionalism_rating ? 'text-yellow-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="font-medium">{{ review.professionalism_rating }}</span>
                </div>
              </div>

              <div v-if="review.value_rating" class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <span class="text-gray-700">Rapport qualité-prix</span>
                <div class="flex items-center space-x-2">
                  <div class="flex">
                    <svg
                      v-for="star in 5"
                      :key="star"
                      class="w-4 h-4"
                      :class="star <= review.value_rating ? 'text-yellow-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </div>
                  <span class="font-medium">{{ review.value_rating }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions utilisateur -->
          <div class="flex items-center justify-between py-4 border-t border-gray-200">
            <div class="flex items-center space-x-6">
              <!-- Bouton utile -->
              <button
                @click="markAsHelpful"
                :disabled="isReacting"
                class="flex items-center space-x-2 text-gray-600 hover:text-green-600 transition-colors disabled:opacity-50"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L9 6v4m-2 4h5.5M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                </svg>
                <span>Utile ({{ review.helpful_count || 0 }})</span>
              </button>

              <!-- Bouton pas utile -->
              <button
                @click="markAsNotHelpful"
                :disabled="isReacting"
                class="flex items-center space-x-2 text-gray-600 hover:text-red-600 transition-colors disabled:opacity-50"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L15 14v-4M17 4h2a2 2 0 012 2v6a2 2 0 01-2 2h-2.5" />
                </svg>
                <span>Pas utile ({{ review.not_helpful_count || 0 }})</span>
              </button>

              <!-- Bouton signaler -->
              <button
                @click="openReportModal"
                class="flex items-center space-x-2 text-gray-600 hover:text-red-600 transition-colors"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.19 2.5 1.732 2.5z" />
                </svg>
                <span>Signaler</span>
              </button>
            </div>

            <!-- Partage -->
            <div class="flex items-center space-x-3">
              <span class="text-sm text-gray-500">Partager :</span>
              <button
                @click="copyLink"
                class="text-gray-600 hover:text-primary transition-colors"
                title="Copier le lien"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Réponse du prestataire -->
      <div v-if="review.response" class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
        <div class="flex items-start space-x-4">
          <div class="w-10 h-10 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium">
            {{ getInitials(review.reviewed) }}
          </div>
          <div class="flex-1">
            <div class="flex items-center space-x-2 mb-3">
              <h4 class="font-semibold text-gray-900">Réponse du prestataire</h4>
              <span class="px-2 py-1 bg-primary/10 text-primary text-xs rounded-full">Réponse officielle</span>
            </div>
            <p class="text-gray-700 leading-relaxed">{{ review.response }}</p>
            <p class="text-sm text-gray-500 mt-2">{{ formatDate(review.response_at) }}</p>
          </div>
        </div>
      </div>

      <!-- Formulaire de réponse (si autorisé) -->
      <div v-if="canRespond" class="bg-white rounded-lg border border-gray-200 p-6">
        <h4 class="font-semibold text-gray-900 mb-4">Répondre à cet avis</h4>
        <form @submit.prevent="submitResponse">
          <div class="mb-4">
            <textarea
              v-model="responseForm.response"
              rows="4"
              maxlength="500"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
              placeholder="Rédigez votre réponse..."
            ></textarea>
            <div class="flex justify-between items-center mt-1">
              <div v-if="responseErrors.response" class="text-sm text-red-600">
                {{ responseErrors.response }}
              </div>
              <div class="text-sm text-gray-500">
                {{ responseForm.response.length }}/500 caractères
              </div>
            </div>
          </div>
          <div class="flex space-x-3">
            <button
              type="submit"
              :disabled="isResponding || !responseForm.response.trim()"
              class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {{ isResponding ? 'Publication...' : 'Publier la réponse' }}
            </button>
            <button
              type="button"
              @click="cancelResponse"
              class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
            >
              Annuler
            </button>
          </div>
        </form>
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
          <div class="flex justify-between items-center w-full mt-4">
            <button
              @click.stop="currentPhotoIndex = Math.max(0, currentPhotoIndex - 1)"
              :disabled="currentPhotoIndex === 0"
              class="px-3 py-1 bg-white/20 text-white rounded disabled:opacity-50"
            >
              Précédent
            </button>
            <span class="text-white">{{ currentPhotoIndex + 1 }} / {{ selectedPhotos.length }}</span>
            <button
              @click.stop="currentPhotoIndex = Math.min(selectedPhotos.length - 1, currentPhotoIndex + 1)"
              :disabled="currentPhotoIndex === selectedPhotos.length - 1"
              class="px-3 py-1 bg-white/20 text-white rounded disabled:opacity-50"
            >
              Suivant
            </button>
          </div>
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
  review: Object,
  canRespond: Boolean,
})

// État local
const isReacting = ref(false)
const showReportModal = ref(false)
const reportForm = ref({
  reason: '',
})
const isReporting = ref(false)

const showPhotoModal = ref(false)
const selectedPhotos = ref([])
const currentPhotoIndex = ref(0)

const responseForm = ref({
  response: '',
})
const responseErrors = ref({})
const isResponding = ref(false)

// Computed
const hasDetailedRatings = computed(() => {
  return props.review.quality_rating || props.review.communication_rating || 
         props.review.punctuality_rating || props.review.professionalism_rating || 
         props.review.value_rating
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
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const markAsHelpful = async () => {
  if (isReacting.value) return
  
  isReacting.value = true
  
  try {
    const response = await fetch(`/reviews/${props.review.id}/helpful`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })
    
    const result = await response.json()
    
    if (response.ok && result.success) {
      // Mettre à jour le compteur
      props.review.helpful_count = result.helpful_count
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

const markAsNotHelpful = async () => {
  if (isReacting.value) return
  
  isReacting.value = true
  
  try {
    const response = await fetch(`/reviews/${props.review.id}/not-helpful`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })
    
    const result = await response.json()
    
    if (response.ok && result.success) {
      // Mettre à jour le compteur
      props.review.not_helpful_count = result.not_helpful_count
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

const openReportModal = () => {
  showReportModal.value = true
}

const closeReportModal = () => {
  showReportModal.value = false
  reportForm.value.reason = ''
}

const submitReport = async () => {
  if (!reportForm.value.reason || isReporting.value) return
  
  isReporting.value = true
  
  try {
    const response = await fetch(`/reviews/${props.review.id}/report`, {
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

const copyLink = () => {
  const url = window.location.href
  navigator.clipboard.writeText(url).then(() => {
    alert('Lien copié dans le presse-papiers')
  }).catch(() => {
    alert('Impossible de copier le lien')
  })
}

const submitResponse = async () => {
  if (isResponding.value || !responseForm.value.response.trim()) return
  
  isResponding.value = true
  responseErrors.value = {}
  
  try {
    const response = await fetch(`/reviews/${props.review.id}/respond`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: JSON.stringify(responseForm.value),
    })
    
    const result = await response.json()
    
    if (response.ok && result.success) {
      // Recharger la page pour afficher la réponse
      router.reload()
    } else {
      if (result.errors) {
        responseErrors.value = result.errors
      } else {
        alert(result.error || 'Une erreur est survenue')
      }
    }
  } catch (error) {
    console.error('Erreur:', error)
    alert('Une erreur est survenue')
  } finally {
    isResponding.value = false
  }
}

const cancelResponse = () => {
  responseForm.value.response = ''
  responseErrors.value = {}
}
</script>