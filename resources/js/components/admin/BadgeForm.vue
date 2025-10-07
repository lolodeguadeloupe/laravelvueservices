<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <!-- Informations de base -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
          Nom du badge *
        </label>
        <input
          id="name"
          v-model="form.name"
          type="text"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
          placeholder="Ex: Premier pas"
        />
        <div v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name }}</div>
      </div>

      <div>
        <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">
          Icône (emoji)
        </label>
        <input
          id="icon"
          v-model="form.icon"
          type="text"
          maxlength="2"
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
          placeholder="🏆"
        />
      </div>
    </div>

    <div>
      <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
        Description *
      </label>
      <textarea
        id="description"
        v-model="form.description"
        required
        rows="3"
        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
        placeholder="Description détaillée du badge et comment l'obtenir"
      ></textarea>
      <div v-if="errors.description" class="text-red-600 text-sm mt-1">{{ errors.description }}</div>
    </div>

    <!-- Type et rareté -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
          Type *
        </label>
        <select
          id="type"
          v-model="form.type"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
        >
          <option value="">Sélectionner un type</option>
          <option value="achievement">Accomplissement</option>
          <option value="certification">Certification</option>
          <option value="milestone">Étape importante</option>
          <option value="quality">Qualité</option>
        </select>
        <div v-if="errors.type" class="text-red-600 text-sm mt-1">{{ errors.type }}</div>
      </div>

      <div>
        <label for="rarity" class="block text-sm font-medium text-gray-700 mb-1">
          Rareté *
        </label>
        <select
          id="rarity"
          v-model="form.rarity"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
        >
          <option value="">Sélectionner une rareté</option>
          <option value="common">Commun</option>
          <option value="rare">Rare</option>
          <option value="epic">Épique</option>
          <option value="legendary">Légendaire</option>
        </select>
        <div v-if="errors.rarity" class="text-red-600 text-sm mt-1">{{ errors.rarity }}</div>
      </div>

      <div>
        <label for="points" class="block text-sm font-medium text-gray-700 mb-1">
          Points de réputation *
        </label>
        <input
          id="points"
          v-model.number="form.points"
          type="number"
          min="0"
          required
          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
          placeholder="10"
        />
        <div v-if="errors.points" class="text-red-600 text-sm mt-1">{{ errors.points }}</div>
      </div>
    </div>

    <!-- Paramètres d'attribution -->
    <div class="space-y-4">
      <h4 class="text-lg font-medium text-gray-900">Paramètres d'attribution</h4>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center">
          <input
            id="is_automatic"
            v-model="form.is_automatic"
            type="checkbox"
            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
          />
          <label for="is_automatic" class="ml-2 block text-sm text-gray-900">
            Attribution automatique
          </label>
        </div>

        <div class="flex items-center">
          <input
            id="is_active"
            v-model="form.is_active"
            type="checkbox"
            class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
          />
          <label for="is_active" class="ml-2 block text-sm text-gray-900">
            Badge actif
          </label>
        </div>
      </div>

      <!-- Critères d'attribution automatique -->
      <div v-if="form.is_automatic" class="space-y-4 p-4 bg-gray-50 rounded-lg">
        <h5 class="font-medium text-gray-900">Critères d'attribution automatique</h5>
        
        <div class="space-y-3">
          <div v-for="(criterion, index) in form.criteria" :key="index" class="flex items-center gap-3">
            <select
              v-model="criterion.field"
              class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
            >
              <option value="">Sélectionner un critère</option>
              <option value="min_bookings_completed">Nombre de réservations complétées</option>
              <option value="min_rating">Note minimale</option>
              <option value="min_five_star_reviews">Nombre d'avis 5 étoiles</option>
              <option value="min_response_rate">Taux de réponse minimal (%)</option>
              <option value="max_cancellation_rate">Taux d'annulation maximal (%)</option>
              <option value="min_punctuality_rating">Note de ponctualité minimale</option>
              <option value="min_days_active">Nombre de jours d'activité</option>
            </select>
            
            <input
              v-model="criterion.value"
              type="number"
              min="0"
              step="0.1"
              class="w-24 px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
              placeholder="Valeur"
            />
            
            <button
              type="button"
              @click="removeCriterion(index)"
              class="p-2 text-red-600 hover:text-red-800 transition-colors"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <button
          type="button"
          @click="addCriterion"
          class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          Ajouter un critère
        </button>
      </div>
    </div>

    <!-- Aperçu du badge -->
    <div class="p-4 bg-gray-50 rounded-lg">
      <h4 class="text-sm font-medium text-gray-900 mb-3">Aperçu du badge</h4>
      <div class="flex justify-center">
        <BadgeCard
          v-if="form.name"
          :badge="{
            name: form.name,
            description: form.description,
            icon: form.icon || '🏆',
            type: form.type,
            rarity: form.rarity,
            points: form.points
          }"
          size="lg"
          :show-points="true"
        />
        <div v-else class="text-gray-500 text-sm">
          Remplissez les informations pour voir l'aperçu
        </div>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
      <button
        type="button"
        @click="$emit('cancelled')"
        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
      >
        Annuler
      </button>
      
      <button
        type="submit"
        :disabled="isSubmitting"
        class="px-4 py-2 bg-primary text-white rounded-md text-sm font-medium hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
      >
        <span v-if="isSubmitting">
          {{ props.badge ? 'Modification...' : 'Création...' }}
        </span>
        <span v-else>
          {{ props.badge ? 'Modifier' : 'Créer' }}
        </span>
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue'
import BadgeCard from '../ui/BadgeCard.vue'

const props = defineProps({
  badge: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['saved', 'cancelled'])

// State
const isSubmitting = ref(false)
const errors = ref({})

const form = reactive({
  name: '',
  description: '',
  icon: '🏆',
  type: '',
  rarity: '',
  points: 10,
  is_automatic: true,
  is_active: true,
  criteria: [
    { field: '', value: '' }
  ]
})

// Methods
const addCriterion = () => {
  form.criteria.push({ field: '', value: '' })
}

const removeCriterion = (index) => {
  if (form.criteria.length > 1) {
    form.criteria.splice(index, 1)
  }
}

const validateForm = () => {
  errors.value = {}

  if (!form.name.trim()) {
    errors.value.name = 'Le nom est requis'
  }

  if (!form.description.trim()) {
    errors.value.description = 'La description est requise'
  }

  if (!form.type) {
    errors.value.type = 'Le type est requis'
  }

  if (!form.rarity) {
    errors.value.rarity = 'La rareté est requise'
  }

  if (!form.points || form.points < 0) {
    errors.value.points = 'Les points doivent être un nombre positif'
  }

  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  isSubmitting.value = true

  try {
    // Préparer les données
    const data = {
      ...form,
      criteria: form.is_automatic 
        ? form.criteria.filter(c => c.field && c.value)
        : []
    }

    // Ici, on ferait l'appel API pour sauvegarder
    await new Promise(resolve => setTimeout(resolve, 1000)) // Simulation

    // Émettre l'événement de sauvegarde
    emit('saved', {
      id: props.badge?.id || Date.now(),
      ...data,
      created_at: props.badge?.created_at || new Date().toISOString(),
      updated_at: new Date().toISOString()
    })
  } catch (error) {
    console.error('Erreur lors de la sauvegarde:', error)
    errors.value.general = 'Une erreur est survenue lors de la sauvegarde'
  } finally {
    isSubmitting.value = false
  }
}

// Watch pour des suggestions de points basées sur la rareté
watch(() => form.rarity, (newRarity) => {
  if (!props.badge) { // Seulement pour les nouveaux badges
    const pointsSuggestions = {
      common: 10,
      rare: 50,
      epic: 100,
      legendary: 200
    }
    
    if (pointsSuggestions[newRarity]) {
      form.points = pointsSuggestions[newRarity]
    }
  }
})

// Initialisation avec les données du badge existant
onMounted(() => {
  if (props.badge) {
    Object.assign(form, {
      name: props.badge.name || '',
      description: props.badge.description || '',
      icon: props.badge.icon || '🏆',
      type: props.badge.type || '',
      rarity: props.badge.rarity || '',
      points: props.badge.points || 10,
      is_automatic: props.badge.is_automatic ?? true,
      is_active: props.badge.is_active ?? true,
      criteria: props.badge.criteria && props.badge.criteria.length > 0 
        ? props.badge.criteria 
        : [{ field: '', value: '' }]
    })
  }
})
</script>