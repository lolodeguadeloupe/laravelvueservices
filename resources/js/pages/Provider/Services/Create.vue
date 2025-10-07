<template>
  <ProviderLayout>
    <div class="max-w-4xl mx-auto p-6">
      <!-- En-tête -->
      <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
          <Link
            :href="route('provider.services.index')"
            class="text-gray-500 hover:text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
          </Link>
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Créer un nouveau service</h1>
            <p class="text-gray-600 mt-1">Ajoutez un nouveau service à votre catalogue</p>
          </div>
        </div>
      </div>

      <!-- Formulaire -->
      <Form
        :action="route('provider.services.store')"
        method="post"
        enctype="multipart/form-data"
        #default="{ errors, processing, wasSuccessful }"
      >
        <div class="bg-white rounded-xl border border-gray-200 p-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informations principales -->
            <div class="space-y-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations principales</h2>
              
              <!-- Titre -->
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                  Titre du service *
                </label>
                <input
                  id="title"
                  name="title"
                  type="text"
                  required
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Ex: Ménage à domicile"
                >
                <div v-if="errors.title" class="mt-1 text-sm text-red-600">
                  {{ errors.title }}
                </div>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                  Description *
                </label>
                <textarea
                  id="description"
                  name="description"
                  rows="5"
                  required
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Décrivez votre service en détail..."
                ></textarea>
                <div v-if="errors.description" class="mt-1 text-sm text-red-600">
                  {{ errors.description }}
                </div>
              </div>

              <!-- Catégorie -->
              <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                  Catégorie *
                </label>
                <select
                  id="category_id"
                  name="category_id"
                  required
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                >
                  <option value="">Sélectionnez une catégorie</option>
                  <option
                    v-for="category in categories"
                    :key="category.id"
                    :value="category.id"
                  >
                    {{ category.name }}
                  </option>
                </select>
                <div v-if="errors.category_id" class="mt-1 text-sm text-red-600">
                  {{ errors.category_id }}
                </div>
              </div>

              <!-- Prix et type de prix -->
              <div class="grid grid-cols-2 gap-4">
                <div>
                  <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                    Prix *
                  </label>
                  <div class="relative">
                    <input
                      id="price"
                      name="price"
                      type="number"
                      step="0.01"
                      min="0"
                      required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-8 focus:ring-2 focus:ring-primary focus:border-transparent"
                      placeholder="0.00"
                    >
                    <span class="absolute right-3 top-2.5 text-gray-500 text-sm">€</span>
                  </div>
                  <div v-if="errors.price" class="mt-1 text-sm text-red-600">
                    {{ errors.price }}
                  </div>
                </div>
                
                <div>
                  <label for="price_type" class="block text-sm font-medium text-gray-700 mb-2">
                    Type de prix *
                  </label>
                  <select
                    id="price_type"
                    name="price_type"
                    required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                  >
                    <option value="fixed">Prix fixe</option>
                    <option value="hourly">Prix horaire</option>
                    <option value="custom">Sur devis</option>
                  </select>
                  <div v-if="errors.price_type" class="mt-1 text-sm text-red-600">
                    {{ errors.price_type }}
                  </div>
                </div>
              </div>

              <!-- Durée -->
              <div>
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                  Durée estimée (en minutes)
                </label>
                <input
                  id="duration"
                  name="duration"
                  type="number"
                  min="1"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Ex: 120"
                >
                <div v-if="errors.duration" class="mt-1 text-sm text-red-600">
                  {{ errors.duration }}
                </div>
              </div>
            </div>

            <!-- Informations logistiques -->
            <div class="space-y-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Informations logistiques</h2>
              
              <!-- Type de lieu -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Lieu d'intervention *
                </label>
                <div class="space-y-2">
                  <label class="flex items-center">
                    <input
                      name="location_type"
                      type="radio"
                      value="client_location"
                      class="text-primary focus:ring-primary"
                    >
                    <span class="ml-2 text-sm text-gray-700">Chez le client uniquement</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      name="location_type"
                      type="radio"
                      value="provider_location"
                      class="text-primary focus:ring-primary"
                    >
                    <span class="ml-2 text-sm text-gray-700">Dans mes locaux uniquement</span>
                  </label>
                  <label class="flex items-center">
                    <input
                      name="location_type"
                      type="radio"
                      value="both"
                      class="text-primary focus:ring-primary"
                    >
                    <span class="ml-2 text-sm text-gray-700">Les deux</span>
                  </label>
                </div>
                <div v-if="errors.location_type" class="mt-1 text-sm text-red-600">
                  {{ errors.location_type }}
                </div>
              </div>

              <!-- Zone de service -->
              <div>
                <label for="service_area" class="block text-sm font-medium text-gray-700 mb-2">
                  Zone de service
                </label>
                <textarea
                  id="service_area"
                  name="service_area"
                  rows="3"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Ex: Paris et petite couronne (75, 92, 93, 94)"
                ></textarea>
                <div v-if="errors.service_area" class="mt-1 text-sm text-red-600">
                  {{ errors.service_area }}
                </div>
              </div>

              <!-- Prérequis -->
              <div>
                <label for="requirements" class="block text-sm font-medium text-gray-700 mb-2">
                  Prérequis et conditions
                </label>
                <textarea
                  id="requirements"
                  name="requirements"
                  rows="4"
                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-primary focus:border-transparent"
                  placeholder="Ex: Fournitures à prévoir, conditions d'annulation..."
                ></textarea>
                <div v-if="errors.requirements" class="mt-1 text-sm text-red-600">
                  {{ errors.requirements }}
                </div>
              </div>

              <!-- Images -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Photos du service
                </label>
                <div
                  @drop="handleDrop"
                  @dragover.prevent
                  @dragenter.prevent
                  class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors"
                >
                  <input
                    ref="fileInput"
                    name="images[]"
                    type="file"
                    multiple
                    accept="image/*"
                    class="hidden"
                    @change="handleFileSelect"
                  >
                  <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <p class="text-gray-600 mb-2">Glissez-déposez vos images ici ou</p>
                  <button
                    type="button"
                    @click="$refs.fileInput.click()"
                    class="text-primary hover:text-primary/80 font-medium"
                  >
                    cliquez pour parcourir
                  </button>
                  <p class="text-xs text-gray-500 mt-2">PNG, JPG jusqu'à 2MB chacune</p>
                </div>
                <div v-if="errors.images" class="mt-1 text-sm text-red-600">
                  {{ errors.images }}
                </div>
                <div v-if="selectedFiles.length > 0" class="mt-4">
                  <h4 class="text-sm font-medium text-gray-700 mb-2">Images sélectionnées:</h4>
                  <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    <div
                      v-for="(file, index) in selectedFiles"
                      :key="index"
                      class="relative"
                    >
                      <img
                        :src="file.preview"
                        :alt="file.name"
                        class="w-full h-20 object-cover rounded border"
                      >
                      <button
                        type="button"
                        @click="removeFile(index)"
                        class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600"
                      >
                        ×
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Statut -->
              <div>
                <label class="flex items-center">
                  <input
                    name="is_active"
                    type="checkbox"
                    value="1"
                    checked
                    class="rounded border-gray-300 text-primary focus:ring-primary"
                  >
                  <span class="ml-2 text-sm text-gray-700">Service actif (visible par les clients)</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
            <Link
              :href="route('provider.services.index')"
              class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors"
            >
              Annuler
            </Link>
            <button
              type="submit"
              :disabled="processing"
              class="px-6 py-3 bg-primary text-white rounded-lg font-medium hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              <span v-if="processing">Création...</span>
              <span v-else>Créer le service</span>
            </button>
          </div>

          <!-- Message de succès -->
          <div v-if="wasSuccessful" class="mt-4 p-4 bg-green-100 border border-green-200 rounded-lg">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <span class="text-green-700 text-sm font-medium">Service créé avec succès!</span>
            </div>
          </div>
        </div>
      </Form>
    </div>
  </ProviderLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Form } from '@inertiajs/vue3'
import ProviderLayout from '@/layouts/ProviderLayout.vue'

// Props
defineProps({
  categories: {
    type: Array,
    required: true
  }
})

// State
const selectedFiles = ref([])
const fileInput = ref(null)

// Méthodes pour la gestion des fichiers
const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  processFiles(files)
}

const handleDrop = (event) => {
  event.preventDefault()
  const files = Array.from(event.dataTransfer.files)
  processFiles(files)
}

const processFiles = (files) => {
  files.forEach(file => {
    if (file.type.startsWith('image/') && file.size <= 2048000) { // 2MB max
      const reader = new FileReader()
      reader.onload = (e) => {
        selectedFiles.value.push({
          file,
          name: file.name,
          preview: e.target.result
        })
      }
      reader.readAsDataURL(file)
    }
  })
}

const removeFile = (index) => {
  selectedFiles.value.splice(index, 1)
  // Reset file input to allow re-selecting the same files
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}
</script>