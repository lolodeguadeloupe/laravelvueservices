<template>
  <AppLayout title="Inscription Prestataire - Étape 2">
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
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
                  2
                </div>
                <span class="ml-2 text-sm font-medium text-primary">Informations professionnelles</span>
              </div>
              <div class="h-px w-12 bg-gray-300"></div>
              <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-300 text-gray-600">
                  3
                </div>
                <span class="ml-2 text-sm text-gray-600">Documents et validation</span>
              </div>
            </div>
          </div>
          <h1 class="text-center text-3xl font-bold text-gray-900">Votre profil professionnel</h1>
          <p class="text-center text-gray-600 mt-2">
            Parlez-nous de votre expérience et de vos compétences
          </p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <Form :action="step2.process.url()" method="post" 
                #default="{ errors, processing }">
            <div class="space-y-6">
              <!-- Nom de l'entreprise (optionnel) -->
              <div>
                <label for="company_name" class="block text-sm font-medium text-gray-700">
                  Nom de l'entreprise (optionnel)
                </label>
                <input
                  type="text"
                  name="company_name"
                  id="company_name"
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  placeholder="Si vous exercez sous le nom d'une entreprise"
                />
                <div v-if="errors.company_name" class="mt-1 text-sm text-red-600">
                  {{ errors.company_name }}
                </div>
              </div>

              <!-- SIRET (optionnel) -->
              <div>
                <label for="siret" class="block text-sm font-medium text-gray-700">
                  Numéro SIRET (optionnel)
                </label>
                <input
                  type="text"
                  name="siret"
                  id="siret"
                  maxlength="14"
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  placeholder="12345678901234"
                />
                <div v-if="errors.siret" class="mt-1 text-sm text-red-600">
                  {{ errors.siret }}
                </div>
              </div>

              <!-- Bio/Description -->
              <div>
                <label for="bio" class="block text-sm font-medium text-gray-700">
                  Description de votre profil *
                </label>
                <textarea
                  name="bio"
                  id="bio"
                  rows="5"
                  required
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  placeholder="Présentez-vous en quelques lignes : qui êtes-vous, quels services proposez-vous, qu'est-ce qui vous différencie..."
                />
                <p class="mt-1 text-sm text-gray-500">Maximum 1000 caractères</p>
                <div v-if="errors.bio" class="mt-1 text-sm text-red-600">
                  {{ errors.bio }}
                </div>
              </div>

              <!-- Expérience -->
              <div>
                <label for="experience" class="block text-sm font-medium text-gray-700">
                  Votre expérience *
                </label>
                <textarea
                  name="experience"
                  id="experience"
                  rows="6"
                  required
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  placeholder="Décrivez votre parcours professionnel, vos expériences significatives, vos réalisations marquantes..."
                />
                <p class="mt-1 text-sm text-gray-500">Maximum 2000 caractères</p>
                <div v-if="errors.experience" class="mt-1 text-sm text-red-600">
                  {{ errors.experience }}
                </div>
              </div>

              <!-- Certifications -->
              <div>
                <label for="certifications" class="block text-sm font-medium text-gray-700">
                  Certifications et diplômes (optionnel)
                </label>
                <div id="certifications-container">
                  <div v-for="(certification, index) in certifications" :key="index" class="flex gap-2 mt-2">
                    <input
                      type="text"
                      :name="`certifications[${index}]`"
                      v-model="certifications[index]"
                      class="flex-1 rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                      placeholder="Nom de la certification ou du diplôme"
                    />
                    <button
                      type="button"
                      @click="removeCertification(index)"
                      v-if="certifications.length > 1"
                      class="px-3 py-2 text-red-600 hover:text-red-800"
                    >
                      Supprimer
                    </button>
                  </div>
                </div>
                <button
                  type="button"
                  @click="addCertification"
                  class="mt-2 text-sm text-primary hover:text-primary/80"
                >
                  + Ajouter une certification
                </button>
                <div v-if="errors.certifications" class="mt-1 text-sm text-red-600">
                  {{ errors.certifications }}
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-between">
              <Link
                :href="step1.url()"
                class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
              >
                Retour à l'étape 1
              </Link>
              
              <button
                type="submit"
                :disabled="processing"
                class="inline-flex items-center justify-center rounded-md bg-primary px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
              >
                <span v-if="processing">Traitement...</span>
                <span v-else>Continuer vers l'étape 3</span>
              </button>
            </div>
          </Form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { Form, Link } from '@inertiajs/vue3'
import { step1, step2, step3 } from '@/routes/provider/registration'
import { ref } from 'vue'

// Gestion dynamique des certifications
const certifications = ref([''])

const addCertification = () => {
  certifications.value.push('')
}

const removeCertification = (index) => {
  if (certifications.value.length > 1) {
    certifications.value.splice(index, 1)
  }
}
</script>