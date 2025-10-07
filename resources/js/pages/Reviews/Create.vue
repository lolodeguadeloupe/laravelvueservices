<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- En-tête -->
      <div class="mb-8">
        <Link :href="route('bookings.show', booking.uuid)" class="inline-flex items-center text-sm text-gray-500 hover:text-primary mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Retour à la réservation
        </Link>
        <h1 class="text-3xl font-bold text-gray-900">Laisser un avis</h1>
        <p class="text-gray-600 mt-2">Partagez votre expérience pour aider la communauté</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Récapitulatif -->
        <div class="lg:col-span-1">
          <div class="bg-gray-50 rounded-lg p-6 sticky top-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif</h2>
            
            <!-- Service -->
            <div class="mb-4">
              <h3 class="font-medium text-gray-900">{{ booking.service.title }}</h3>
              <p class="text-sm text-gray-600">{{ booking.service.short_description }}</p>
            </div>

            <!-- Personne évaluée -->
            <div class="mb-4 pb-4 border-b border-gray-200">
              <p class="text-sm text-gray-500 mb-2">{{ reviewerType === 'client' ? 'Prestataire' : 'Client' }}</p>
              <div class="flex items-center">
                <div class="w-10 h-10 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium mr-3">
                  {{ (reviewerType === 'client' 
                    ? (booking.provider.profile?.first_name?.charAt(0) + booking.provider.profile?.last_name?.charAt(0))
                    : (booking.client.profile?.first_name?.charAt(0) + booking.client.profile?.last_name?.charAt(0))
                  ) }}
                </div>
                <div>
                  <p class="font-medium text-gray-900">
                    {{ reviewerType === 'client' 
                      ? (booking.provider.profile?.first_name + ' ' + booking.provider.profile?.last_name)
                      : (booking.client.profile?.first_name + ' ' + booking.client.profile?.last_name)
                    }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ reviewerType === 'client' ? booking.provider.email : booking.client.email }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Détails -->
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Date du service</span>
                <span class="text-gray-900">{{ new Date(booking.confirmed_datetime || booking.preferred_datetime).toLocaleDateString('fr-FR') }}</span>
              </div>
              
              <div v-if="booking.final_price" class="flex justify-between">
                <span class="text-gray-600">Prix</span>
                <span class="text-gray-900">{{ booking.final_price }}€</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Formulaire d'avis -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <form @submit.prevent="submitReview">
              <!-- Note globale -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                  Note globale *
                </label>
                <div class="flex items-center space-x-2">
                  <div class="flex space-x-1">
                    <button
                      v-for="star in 5"
                      :key="star"
                      type="button"
                      @click="form.overall_rating = star"
                      class="focus:outline-none"
                    >
                      <svg 
                        class="w-8 h-8 transition-colors"
                        :class="star <= form.overall_rating ? 'text-yellow-400' : 'text-gray-300'"
                        fill="currentColor" 
                        viewBox="0 0 20 20"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    </button>
                  </div>
                  <span v-if="form.overall_rating" class="text-sm text-gray-600 ml-3">
                    {{ getRatingText(form.overall_rating) }}
                  </span>
                </div>
                <div v-if="errors.overall_rating" class="mt-1 text-sm text-red-600">
                  {{ errors.overall_rating }}
                </div>
              </div>

              <!-- Notes détaillées -->
              <div v-if="form.overall_rating" class="mb-6">
                <h3 class="text-sm font-medium text-gray-700 mb-4">Notes détaillées (optionnelles)</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                  <div v-for="(label, key) in detailedRatings" :key="key">
                    <label class="block text-sm text-gray-600 mb-2">{{ label }}</label>
                    <div class="flex space-x-1">
                      <button
                        v-for="star in 5"
                        :key="star"
                        type="button"
                        @click="form[key + '_rating'] = star"
                        class="focus:outline-none"
                      >
                        <svg 
                          class="w-5 h-5 transition-colors"
                          :class="star <= form[key + '_rating'] ? 'text-yellow-400' : 'text-gray-300'"
                          fill="currentColor" 
                          viewBox="0 0 20 20"
                        >
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Titre -->
              <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                  Titre de votre avis (optionnel)
                </label>
                <input
                  id="title"
                  v-model="form.title"
                  type="text"
                  maxlength="255"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                  placeholder="Résumez votre expérience en quelques mots..."
                />
              </div>

              <!-- Commentaire -->
              <div class="mb-6">
                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                  Votre commentaire *
                </label>
                <textarea
                  id="comment"
                  v-model="form.comment"
                  rows="5"
                  maxlength="1000"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-primary focus:border-primary"
                  placeholder="Décrivez votre expérience en détail..."
                ></textarea>
                <div class="flex justify-between items-center mt-1">
                  <div v-if="errors.comment" class="text-sm text-red-600">
                    {{ errors.comment }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ form.comment.length }}/1000 caractères
                  </div>
                </div>
              </div>

              <!-- Photos -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Photos (optionnel, max 5)
                </label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                  <input
                    ref="fileInput"
                    type="file"
                    multiple
                    accept="image/*"
                    @change="handleFileUpload"
                    class="hidden"
                  />
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50"
                  >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Ajouter des photos
                  </button>
                  <p class="text-sm text-gray-500 mt-2">PNG, JPG jusqu'à 2MB chacune</p>
                </div>

                <!-- Aperçu des photos -->
                <div v-if="selectedFiles.length" class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4">
                  <div v-for="(file, index) in selectedFiles" :key="index" class="relative">
                    <img :src="file.preview" :alt="file.name" class="w-full h-24 object-cover rounded-lg" />
                    <button
                      type="button"
                      @click="removeFile(index)"
                      class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600"
                    >
                      ×
                    </button>
                  </div>
                </div>
              </div>

              <!-- Boutons -->
              <div class="flex space-x-4">
                <button
                  type="submit"
                  :disabled="isSubmitting || !form.overall_rating || !form.comment"
                  class="flex-1 px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
                >
                  <span v-if="isSubmitting" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Publication en cours...
                  </span>
                  <span v-else>
                    Publier l'avis
                  </span>
                </button>
                
                <Link
                  :href="route('bookings.show', booking.uuid)"
                  class="px-4 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-center"
                >
                  Annuler
                </Link>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps({
  booking: Object,
  reviewerType: String,
})

// État du formulaire
const form = reactive({
  overall_rating: 0,
  quality_rating: 0,
  communication_rating: 0,
  punctuality_rating: 0,
  professionalism_rating: 0,
  value_rating: 0,
  title: '',
  comment: '',
})

const selectedFiles = ref([])
const isSubmitting = ref(false)
const errors = ref({})

// Labels pour les notes détaillées
const detailedRatings = {
  quality: 'Qualité du service',
  communication: 'Communication',
  punctuality: 'Ponctualité',
  professionalism: 'Professionnalisme',
  value: 'Rapport qualité-prix',
}

// Méthodes
const getRatingText = (rating) => {
  const texts = {
    1: 'Très décevant',
    2: 'Décevant',
    3: 'Correct',
    4: 'Très bien',
    5: 'Excellent'
  }
  return texts[rating] || ''
}

const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  
  files.forEach((file) => {
    if (selectedFiles.value.length >= 5) {
      return
    }
    
    if (file.size > 2 * 1024 * 1024) {
      alert('La taille du fichier ne peut pas dépasser 2MB')
      return
    }
    
    const reader = new FileReader()
    reader.onload = (e) => {
      selectedFiles.value.push({
        file,
        name: file.name,
        preview: e.target.result,
      })
    }
    reader.readAsDataURL(file)
  })
  
  // Reset input
  event.target.value = ''
}

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
}

const submitReview = async () => {
  if (isSubmitting.value) return
  
  isSubmitting.value = true
  errors.value = {}
  
  try {
    const formData = new FormData()
    
    // Ajouter les données du formulaire
    Object.keys(form).forEach(key => {
      if (form[key] !== '' && form[key] !== 0) {
        formData.append(key, form[key])
      }
    })
    
    // Ajouter les photos
    selectedFiles.value.forEach((fileObj, index) => {
      formData.append(`photos[${index}]`, fileObj.file)
    })
    
    const response = await fetch(`/bookings/${props.booking.id}/review`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: formData,
    })
    
    const result = await response.json()
    
    if (response.ok && result.success) {
      router.visit(result.redirect_url, {
        onSuccess: () => {
          // Message de succès géré côté serveur
        }
      })
    } else {
      if (result.errors) {
        errors.value = result.errors
      } else {
        alert(result.error || 'Une erreur est survenue')
      }
    }
  } catch (error) {
    console.error('Erreur:', error)
    alert('Une erreur est survenue lors de la publication de votre avis')
  } finally {
    isSubmitting.value = false
  }
}
</script>