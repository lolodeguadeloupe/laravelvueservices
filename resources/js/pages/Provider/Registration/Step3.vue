<template>
  <AppLayout title="Inscription Prestataire - Étape 3">
    <div class="py-12">
      <div class="mx-auto max-w-3xl">
        <!-- En-tête avec progression -->
        <div class="mb-8">
          <div class="flex items-center justify-center mb-4">
            <div class="flex items-center space-x-4">
              <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500 text-white">
                  ✓
                </div>
                <span class="ml-2 text-sm font-medium text-green-600">Informations personnelles</span>
              </div>
              <div class="h-px w-12 bg-green-500"></div>
              <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500 text-white">
                  ✓
                </div>
                <span class="ml-2 text-sm font-medium text-green-600">Informations professionnelles</span>
              </div>
              <div class="h-px w-12 bg-green-500"></div>
              <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
                  3
                </div>
                <span class="ml-2 text-sm font-medium text-primary">Documents et validation</span>
              </div>
            </div>
          </div>
          <h1 class="text-center text-3xl font-bold text-gray-900">Finalisation de votre inscription</h1>
          <p class="text-center text-gray-600 mt-2">
            Dernière étape : téléchargez vos documents et créez votre mot de passe
          </p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <Form :action="step3.process.url()" method="post" 
                enctype="multipart/form-data"
                #default="{ errors, processing, progress }">
            <div class="space-y-6">
              <!-- Mot de passe -->
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <label for="password" class="block text-sm font-medium text-gray-700">
                    Mot de passe *
                  </label>
                  <input
                    type="password"
                    name="password"
                    id="password"
                    required
                    minlength="8"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                  <p class="mt-1 text-sm text-gray-500">Minimum 8 caractères</p>
                  <div v-if="errors.password" class="mt-1 text-sm text-red-600">
                    {{ errors.password }}
                  </div>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                    Confirmer le mot de passe *
                  </label>
                  <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    required
                    minlength="8"
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                </div>
              </div>

              <!-- Documents obligatoires -->
              <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Documents requis</h3>
                
                <!-- Pièce d'identité -->
                <div class="mb-6">
                  <label for="identity_document" class="block text-sm font-medium text-gray-700">
                    Pièce d'identité * (PDF, JPG, JPEG, PNG - Max 5MB)
                  </label>
                  <input
                    type="file"
                    name="identity_document"
                    id="identity_document"
                    required
                    accept=".pdf,.jpg,.jpeg,.png"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Carte d'identité, passeport ou permis de conduire
                  </p>
                  <div v-if="errors.identity_document" class="mt-1 text-sm text-red-600">
                    {{ errors.identity_document }}
                  </div>
                </div>

                <!-- Assurance professionnelle -->
                <div class="mb-6">
                  <label for="professional_insurance" class="block text-sm font-medium text-gray-700">
                    Assurance responsabilité civile professionnelle (optionnel - PDF - Max 5MB)
                  </label>
                  <input
                    type="file"
                    name="professional_insurance"
                    id="professional_insurance"
                    accept=".pdf"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
                  />
                  <p class="mt-1 text-sm text-gray-500">
                    Recommandé pour certains types de services
                  </p>
                  <div v-if="errors.professional_insurance" class="mt-1 text-sm text-red-600">
                    {{ errors.professional_insurance }}
                  </div>
                </div>

                <!-- Fichiers de certifications -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Justificatifs de certifications (optionnel - PDF, JPG, JPEG, PNG - Max 5MB par fichier)
                  </label>
                  <div id="certification-files-container">
                    <div v-for="(file, index) in certificationFiles" :key="index" class="flex gap-2 mt-2">
                      <input
                        type="file"
                        :name="`certifications_files[${index}]`"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="flex-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20"
                      />
                      <button
                        type="button"
                        @click="removeCertificationFile(index)"
                        v-if="certificationFiles.length > 1"
                        class="px-3 py-2 text-red-600 hover:text-red-800"
                      >
                        Supprimer
                      </button>
                    </div>
                  </div>
                  <button
                    type="button"
                    @click="addCertificationFile"
                    class="mt-2 text-sm text-primary hover:text-primary/80"
                  >
                    + Ajouter un fichier
                  </button>
                  <div v-if="errors.certifications_files" class="mt-1 text-sm text-red-600">
                    {{ errors.certifications_files }}
                  </div>
                </div>
              </div>

              <!-- Conditions d'utilisation -->
              <div class="border-t border-gray-200 pt-6">
                <div class="flex items-start">
                  <div class="flex items-center h-5">
                    <input
                      id="terms_accepted"
                      name="terms_accepted"
                      type="checkbox"
                      required
                      value="1"
                      class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded"
                    />
                  </div>
                  <div class="ml-3 text-sm">
                    <label for="terms_accepted" class="font-medium text-gray-700">
                      J'accepte les conditions d'utilisation et la politique de confidentialité *
                    </label>
                    <p class="text-gray-500">
                      En cochant cette case, vous acceptez nos 
                      <a href="#" class="text-primary hover:text-primary/80">conditions d'utilisation</a>
                      et notre 
                      <a href="#" class="text-primary hover:text-primary/80">politique de confidentialité</a>.
                    </p>
                  </div>
                </div>
                <div v-if="errors.terms_accepted" class="mt-1 text-sm text-red-600">
                  {{ errors.terms_accepted }}
                </div>
              </div>

              <!-- Barre de progression pour l'upload -->
              <div v-if="progress && processing" class="mt-4">
                <div class="bg-gray-200 rounded-full h-2">
                  <div class="bg-primary h-2 rounded-full transition-all duration-300" 
                       :style="`width: ${progress.percentage}%`"></div>
                </div>
                <p class="text-sm text-gray-600 mt-1">Téléchargement en cours : {{ progress.percentage }}%</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-between">
              <Link
                :href="step2.url()"
                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              >
                Retour à l'étape 2
              </Link>
              
              <button
                type="submit"
                :disabled="processing"
                class="inline-flex items-center justify-center rounded-md bg-primary px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
              >
                <span v-if="processing">Finalisation en cours...</span>
                <span v-else>Finaliser mon inscription</span>
              </button>
            </div>
          </Form>
        </div>

        <!-- Informations importantes -->
        <div class="mt-8 bg-amber-50 border border-amber-200 rounded-lg p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
              </svg>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-amber-800">
                Important
              </h3>
              <div class="mt-1 text-sm text-amber-700">
                <p>Votre inscription sera examinée par notre équipe sous 48h. Vous recevrez une notification par email une fois votre profil validé.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Form, Link } from '@inertiajs/vue3'
import { step2, step3 } from '@/routes/provider/registration'
import { ref } from 'vue'

// Gestion dynamique des fichiers de certification
const certificationFiles = ref([{}])

const addCertificationFile = () => {
  certificationFiles.value.push({})
}

const removeCertificationFile = (index) => {
  if (certificationFiles.value.length > 1) {
    certificationFiles.value.splice(index, 1)
  }
}
</script>