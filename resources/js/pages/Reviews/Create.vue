<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const props = defineProps({
  booking: {
    type: Object,
    required: true
  }
})

// État du formulaire d'avis
const reviewForm = useForm({
  booking_id: props.booking.id,
  overall_rating: 0,
  quality_rating: 0,
  punctuality_rating: 0,
  cleanliness_rating: 0,
  communication_rating: 0,
  comment: '',
  would_recommend: null,
  photos: [],
  is_anonymous: false
})

// État de l'interface
const hoveredStars = ref({
  overall: 0,
  quality: 0,
  punctuality: 0,
  cleanliness: 0,
  communication: 0
})

// Fonction pour définir la note
const setRating = (criteria, rating) => {
  reviewForm[`${criteria}_rating`] = rating
}

// Fonction pour gérer le survol des étoiles
const handleStarHover = (criteria, rating) => {
  hoveredStars.value[criteria] = rating
}

// Fonction pour réinitialiser le survol
const resetStarHover = (criteria) => {
  hoveredStars.value[criteria] = 0
}

// Fonction pour obtenir la note affichée (survol ou sélectionnée)
const getDisplayRating = (criteria) => {
  return hoveredStars.value[criteria] || reviewForm[`${criteria}_rating`]
}

// Fonction pour gérer l'upload de photos
const handlePhotoUpload = (event) => {
  const files = Array.from(event.target.files)
  
  // Limiter à 5 photos maximum
  if (reviewForm.photos.length + files.length > 5) {
    alert('Vous ne pouvez ajouter que 5 photos maximum.')
    return
  }

  files.forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader()
      reader.onload = (e) => {
        reviewForm.photos.push({
          file: file,
          url: e.target.result,
          name: file.name
        })
      }
      reader.readAsDataURL(file)
    }
  })
}

// Fonction pour supprimer une photo
const removePhoto = (index) => {
  reviewForm.photos.splice(index, 1)
}

// Validation du formulaire
const isFormValid = computed(() => {
  return reviewForm.overall_rating > 0 && 
         reviewForm.comment.trim().length >= 10 &&
         reviewForm.would_recommend !== null
})

// Fonction pour formater le prix
const formatPrice = (price) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

// Fonction pour formater la date
const formatDate = (date) => {
  return new Intl.DateTimeFormat('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(new Date(date))
}

// Textes descriptifs pour les notes
const getRatingText = (rating) => {
  const texts = {
    1: 'Très décevant',
    2: 'Décevant',
    3: 'Correct',
    4: 'Bien',
    5: 'Excellent'
  }
  return texts[rating] || ''
}

// Soumission du formulaire
const submitReview = () => {
  reviewForm.post(route('reviews.store', props.booking.id), {
    onSuccess: () => {
      // Redirection vers la page de confirmation ou la réservation
    }
  })
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-amber-50 py-8">
      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('dashboard')" class="text-green-600 hover:text-green-700">
                  Dashboard
                </Link>
              </li>
              <li>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </li>
              <li>
                <Link :href="route('bookings.show', booking.id)" class="text-green-600 hover:text-green-700">
                  Réservation #{{ booking.id }}
                </Link>
              </li>
              <li>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </li>
              <li class="text-gray-500">Évaluation</li>
            </ol>
          </nav>
          
          <h1 class="text-3xl font-bold text-gray-900">Évaluez votre service</h1>
          <p class="mt-2 text-lg text-gray-600">Partagez votre expérience pour aider d'autres clients</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Formulaire principal -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Note globale -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Note globale</h3>
              
              <div class="text-center">
                <div class="flex justify-center space-x-2 mb-4">
                  <button
                    v-for="star in 5"
                    :key="star"
                    @click="setRating('overall', star)"
                    @mouseenter="handleStarHover('overall', star)"
                    @mouseleave="resetStarHover('overall')"
                    class="text-4xl transition-colors duration-150 focus:outline-none"
                  >
                    <svg
                      :class="star <= getDisplayRating('overall') ? 'text-yellow-400' : 'text-gray-300'"
                      fill="currentColor"
                      viewBox="0 0 20 20"
                      class="w-10 h-10"
                    >
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                  </button>
                </div>
                
                <p v-if="reviewForm.overall_rating > 0" class="text-lg font-medium text-gray-700">
                  {{ getRatingText(reviewForm.overall_rating) }}
                </p>
                <p v-else class="text-gray-500">Cliquez sur les étoiles pour noter</p>
              </div>
            </div>

            <!-- Critères détaillés -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-6">Critères détaillés</h3>
              
              <div class="space-y-6">
                <!-- Qualité du service -->
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="font-medium text-gray-900">Qualité du service</h4>
                    <p class="text-sm text-gray-600">Le travail a-t-il été bien fait ?</p>
                  </div>
                  <div class="flex space-x-1 ml-4">
                    <button
                      v-for="star in 5"
                      :key="star"
                      @click="setRating('quality', star)"
                      @mouseenter="handleStarHover('quality', star)"
                      @mouseleave="resetStarHover('quality')"
                      class="transition-colors duration-150 focus:outline-none"
                    >
                      <svg
                        :class="star <= getDisplayRating('quality') ? 'text-yellow-400' : 'text-gray-300'"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        class="w-6 h-6"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Ponctualité -->
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="font-medium text-gray-900">Ponctualité</h4>
                    <p class="text-sm text-gray-600">Le prestataire est-il arrivé à l'heure ?</p>
                  </div>
                  <div class="flex space-x-1 ml-4">
                    <button
                      v-for="star in 5"
                      :key="star"
                      @click="setRating('punctuality', star)"
                      @mouseenter="handleStarHover('punctuality', star)"
                      @mouseleave="resetStarHover('punctuality')"
                      class="transition-colors duration-150 focus:outline-none"
                    >
                      <svg
                        :class="star <= getDisplayRating('punctuality') ? 'text-yellow-400' : 'text-gray-300'"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        class="w-6 h-6"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Propreté -->
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="font-medium text-gray-900">Propreté</h4>
                    <p class="text-sm text-gray-600">L'espace a-t-il été laissé propre ?</p>
                  </div>
                  <div class="flex space-x-1 ml-4">
                    <button
                      v-for="star in 5"
                      :key="star"
                      @click="setRating('cleanliness', star)"
                      @mouseenter="handleStarHover('cleanliness', star)"
                      @mouseleave="resetStarHover('cleanliness')"
                      class="transition-colors duration-150 focus:outline-none"
                    >
                      <svg
                        :class="star <= getDisplayRating('cleanliness') ? 'text-yellow-400' : 'text-gray-300'"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        class="w-6 h-6"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Communication -->
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <h4 class="font-medium text-gray-900">Communication</h4>
                    <p class="text-sm text-gray-600">Le prestataire était-il à l'écoute ?</p>
                  </div>
                  <div class="flex space-x-1 ml-4">
                    <button
                      v-for="star in 5"
                      :key="star"
                      @click="setRating('communication', star)"
                      @mouseenter="handleStarHover('communication', star)"
                      @mouseleave="resetStarHover('communication')"
                      class="transition-colors duration-150 focus:outline-none"
                    >
                      <svg
                        :class="star <= getDisplayRating('communication') ? 'text-yellow-400' : 'text-gray-300'"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                        class="w-6 h-6"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Commentaire -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Votre commentaire</h3>
              
              <div>
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                  Décrivez votre expérience (minimum 10 caractères)
                </label>
                <textarea
                  id="comment"
                  v-model="reviewForm.comment"
                  rows="5"
                  placeholder="Partagez les détails de votre expérience : qu'est-ce qui s'est bien passé ? Y a-t-il des points à améliorer ?"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                ></textarea>
                <p class="mt-1 text-sm text-gray-500">
                  {{ reviewForm.comment.length }} caractères
                  <span v-if="reviewForm.comment.length < 10" class="text-red-600">
                    (minimum 10 requis)
                  </span>
                </p>
              </div>
            </div>

            <!-- Recommandation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Recommandation</h3>
              
              <div class="space-y-3">
                <p class="text-gray-700">Recommanderiez-vous ce prestataire à vos proches ?</p>
                
                <div class="flex space-x-4">
                  <label :class="[
                    'flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors',
                    reviewForm.would_recommend === true ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'
                  ]">
                    <input
                      type="radio"
                      :value="true"
                      v-model="reviewForm.would_recommend"
                      class="sr-only"
                    >
                    <div class="flex items-center space-x-3">
                      <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                      </svg>
                      <span class="font-medium text-gray-900">Oui, je recommande</span>
                    </div>
                  </label>
                  
                  <label :class="[
                    'flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors',
                    reviewForm.would_recommend === false ? 'border-red-500 bg-red-50' : 'border-gray-200 hover:border-gray-300'
                  ]">
                    <input
                      type="radio"
                      :value="false"
                      v-model="reviewForm.would_recommend"
                      class="sr-only"
                    >
                    <div class="flex items-center space-x-3">
                      <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14H5.236a2 2 0 01-1.789-2.894l3.5-7A2 2 0 018.736 3h4.018c.163 0 .326.02.485.06L17 4m-7 10v2a2 2 0 002 2h.095c.5 0 .905-.405.905-.905 0-.714.211-1.412.608-2.006L17 13V4m-7 10h2m5-10H5a2 2 0 00-2 2v6a2 2 0 002 2h2.5" />
                      </svg>
                      <span class="font-medium text-gray-900">Non, je ne recommande pas</span>
                    </div>
                  </label>
                </div>
              </div>
            </div>

            <!-- Photos -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Photos du travail réalisé (optionnel)</h3>
              
              <div class="space-y-4">
                <p class="text-gray-600">Ajoutez des photos pour illustrer le travail réalisé (maximum 5 photos)</p>
                
                <!-- Zone d'upload -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 transition-colors">
                  <label class="cursor-pointer">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <div class="mt-2">
                      <span class="text-sm font-medium text-gray-900">Cliquez pour ajouter des photos</span>
                      <p class="text-xs text-gray-500">PNG, JPG jusqu'à 10MB chacune</p>
                    </div>
                    <input
                      type="file"
                      multiple
                      accept="image/*"
                      @change="handlePhotoUpload"
                      class="hidden"
                    >
                  </label>
                </div>
                
                <!-- Aperçu des photos -->
                <div v-if="reviewForm.photos.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                  <div
                    v-for="(photo, index) in reviewForm.photos"
                    :key="index"
                    class="relative group"
                  >
                    <img
                      :src="photo.url"
                      :alt="photo.name"
                      class="w-full h-24 object-cover rounded-lg"
                    >
                    <button
                      @click="removePhoto(index)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Options de confidentialité -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Confidentialité</h3>
              
              <div class="flex items-start">
                <input
                  id="is_anonymous"
                  v-model="reviewForm.is_anonymous"
                  type="checkbox"
                  class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded mt-1"
                >
                <label for="is_anonymous" class="ml-3 text-sm text-gray-700">
                  Publier cet avis de manière anonyme
                  <p class="text-gray-500 mt-1">Votre nom ne sera pas affiché publiquement</p>
                </label>
              </div>
            </div>

            <!-- Bouton de soumission -->
            <div class="text-center">
              <button
                @click="submitReview"
                :disabled="!isFormValid || reviewForm.processing"
                class="bg-green-600 text-white py-3 px-8 rounded-lg font-semibold text-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <svg v-if="reviewForm.processing" class="inline w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ reviewForm.processing ? 'Publication en cours...' : 'Publier mon avis' }}
              </button>
            </div>
          </div>

          <!-- Sidebar - Récapitulatif du service -->
          <div class="lg:col-span-1">
            <div class="sticky top-8">
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Service évalué</h3>
                
                <!-- Image du service -->
                <div class="mb-4">
                  <img
                    :src="booking.service.images?.[0]?.url || '/placeholder-service.jpg'"
                    :alt="booking.service.title"
                    class="w-full h-32 object-cover rounded-lg"
                  >
                </div>
                
                <!-- Détails -->
                <div class="space-y-3">
                  <div>
                    <h4 class="font-medium text-gray-900">{{ booking.service.title }}</h4>
                    <p class="text-sm text-gray-600">{{ booking.service.provider.profile?.company_name || booking.service.provider.name }}</p>
                  </div>
                  
                  <div class="text-sm text-gray-600">
                    <div class="flex items-center mb-1">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 002 2z" />
                      </svg>
                      {{ formatDate(booking.scheduled_date) }}
                    </div>
                    
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                      </svg>
                      {{ formatPrice(booking.price) }}
                    </div>
                  </div>
                  
                  <div class="pt-3 border-t border-gray-200">
                    <div class="flex items-center space-x-3">
                      <img
                        :src="booking.service.provider.profile?.avatar || '/default-avatar.jpg'"
                        :alt="booking.service.provider.name"
                        class="h-10 w-10 rounded-full object-cover"
                      >
                      <div>
                        <p class="font-medium text-gray-900">{{ booking.service.provider.name }}</p>
                        <div class="flex items-center">
                          <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                          <span class="text-sm text-gray-600">{{ booking.service.average_rating || '5.0' }}/5</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Conseils pour un bon avis -->
              <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-medium text-blue-900 mb-2">Conseils pour un avis utile</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                  <li>• Soyez honnête et constructif</li>
                  <li>• Décrivez ce qui s'est bien passé</li>
                  <li>• Mentionnez les points à améliorer</li>
                  <li>• Restez respectueux envers le prestataire</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>