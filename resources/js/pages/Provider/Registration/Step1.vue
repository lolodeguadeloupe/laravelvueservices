<template>
  <AppLayout title="Inscription Prestataire - Étape 1">
    <div class="py-12">
      <div class="mx-auto max-w-3xl">
        <!-- En-tête avec progression -->
        <div class="mb-8">
          <div class="flex items-center justify-center mb-4">
            <div class="flex items-center space-x-4">
              <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-white">
                  1
                </div>
                <span class="ml-2 text-sm font-medium text-primary">Informations personnelles</span>
              </div>
              <div class="h-px w-12 bg-gray-300"></div>
              <div class="flex items-center">
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-300 text-gray-600">
                  2
                </div>
                <span class="ml-2 text-sm text-gray-600">Informations professionnelles</span>
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
          <h1 class="text-center text-3xl font-bold text-gray-900">Devenir Prestataire</h1>
          <p class="text-center text-gray-600 mt-2">
            Rejoignez notre réseau de prestataires de services de qualité
          </p>
        </div>

        <!-- Formulaire -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
          <Form :action="step1.process.url()" method="post" 
                #default="{ errors, processing }">
            <div class="space-y-6">
              <!-- Prénom et Nom -->
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                  <label for="first_name" class="block text-sm font-medium text-gray-700">
                    Prénom *
                  </label>
                  <input
                    type="text"
                    name="first_name"
                    id="first_name"
                    required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                  <div v-if="errors.first_name" class="mt-1 text-sm text-red-600">
                    {{ errors.first_name }}
                  </div>
                </div>

                <div>
                  <label for="last_name" class="block text-sm font-medium text-gray-700">
                    Nom de famille *
                  </label>
                  <input
                    type="text"
                    name="last_name"
                    id="last_name"
                    required
                    class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  />
                  <div v-if="errors.last_name" class="mt-1 text-sm text-red-600">
                    {{ errors.last_name }}
                  </div>
                </div>
              </div>

              <!-- Email -->
              <div>
                <label for="email" class="block text-sm font-medium text-gray-700">
                  Adresse email *
                </label>
                <input
                  type="email"
                  name="email"
                  id="email"
                  required
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                />
                <div v-if="errors.email" class="mt-1 text-sm text-red-600">
                  {{ errors.email }}
                </div>
              </div>

              <!-- Téléphone -->
              <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">
                  Numéro de téléphone *
                </label>
                <input
                  type="tel"
                  name="phone"
                  id="phone"
                  required
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                />
                <div v-if="errors.phone" class="mt-1 text-sm text-red-600">
                  {{ errors.phone }}
                </div>
              </div>

              <!-- Date de naissance -->
              <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">
                  Date de naissance *
                </label>
                <input
                  type="date"
                  name="date_of_birth"
                  id="date_of_birth"
                  required
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                />
                <div v-if="errors.date_of_birth" class="mt-1 text-sm text-red-600">
                  {{ errors.date_of_birth }}
                </div>
              </div>

              <!-- Adresse -->
              <div>
                <label for="address" class="block text-sm font-medium text-gray-700">
                  Adresse complète *
                </label>
                <textarea
                  name="address"
                  id="address"
                  rows="3"
                  required
                  class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                  placeholder="Numéro, rue, ville, code postal, pays"
                />
                <div v-if="errors.address" class="mt-1 text-sm text-red-600">
                  {{ errors.address }}
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-end">
              <button
                type="submit"
                :disabled="processing"
                class="inline-flex items-center justify-center rounded-md bg-primary px-6 py-3 text-sm font-medium text-white shadow-sm hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 disabled:opacity-50"
              >
                <span v-if="processing">Traitement...</span>
                <span v-else>Continuer vers l'étape 2</span>
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
import { Form } from '@inertiajs/vue3'
import { step1, step2 } from '@/routes/provider/registration'
</script>