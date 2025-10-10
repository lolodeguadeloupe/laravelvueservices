<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

interface Props {
    user: any;
    mustVerifyEmail: boolean;
    status?: string;
}

const props = defineProps<Props>()

const form = ref({
    name: props.user.name || '',
    email: props.user.email || '',
    phone: props.user.phone || '',
    address: props.user.address || '',
})

const processing = ref(false)
const errors = ref({})
const recentlySuccessful = ref(false)
const showDeleteModal = ref(false)

const submitForm = () => {
    processing.value = true
    errors.value = {}
    
    router.patch('/settings/profile', form.value, {
        onSuccess: () => {
            recentlySuccessful.value = true
            setTimeout(() => {
                recentlySuccessful.value = false
            }, 3000)
        },
        onError: (errorResponse) => {
            errors.value = errorResponse
        },
        onFinish: () => {
            processing.value = false
        },
        preserveScroll: true
    })
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-amber-50">
      <Head title="Modifier mon profil" />
      
      <!-- Header -->
      <div class="bg-white border-b border-green-100 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center space-x-4">
            <Link href="/profile" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </Link>
            <div>
              <h1 class="text-2xl font-bold text-gray-900">Modifier mon profil</h1>
              <p class="text-sm text-gray-600">Gérez vos informations personnelles</p>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
          <!-- Message de succès -->
          <div v-if="status" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
              <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <p class="ml-3 text-sm text-green-700">{{ status }}</p>
            </div>
          </div>

          <div v-if="recentlySuccessful" class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
              <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              <p class="ml-3 text-sm text-green-700">Profil mis à jour avec succès !</p>
            </div>
          </div>

          <form @submit.prevent="submitForm" class="p-6 space-y-6">
            <!-- Informations de base -->
            <div>
              <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom complet -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom complet *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Votre nom complet"
                  />
                  <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name[0] }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Adresse email *
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="votre.email@exemple.com"
                  />
                  <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email[0] }}</p>
                  
                  <!-- Vérification email -->
                  <div v-if="mustVerifyEmail && !user.email_verified_at" class="mt-2">
                    <p class="text-sm text-amber-600">
                      Votre adresse email n'est pas vérifiée.
                      <Link
                        href="/email/verification-notification"
                        method="post"
                        class="text-green-600 hover:text-green-700 underline"
                      >
                        Cliquez ici pour renvoyer l'email de vérification.
                      </Link>
                    </p>
                  </div>
                </div>

                <!-- Téléphone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    Numéro de téléphone
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="06 12 34 56 78"
                  />
                  <p v-if="errors.phone" class="mt-1 text-sm text-red-600">{{ errors.phone[0] }}</p>
                </div>

                <!-- Type de compte -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Type de compte
                  </label>
                  <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md text-gray-700 capitalize">
                    {{ user.user_type === 'provider' ? 'Prestataire' : user.user_type === 'client' ? 'Client' : 'Administrateur' }}
                  </div>
                </div>
              </div>

              <!-- Adresse -->
              <div class="mt-6">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                  Adresse complète
                </label>
                <textarea
                  id="address"
                  v-model="form.address"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                  placeholder="Votre adresse complète (rue, ville, code postal)"
                ></textarea>
                <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address[0] }}</p>
              </div>
            </div>

            <!-- Actions -->
            <div class="pt-6 border-t border-gray-200">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                  <button
                    type="submit"
                    :disabled="processing"
                    class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <svg v-if="processing" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ processing ? 'Enregistrement...' : 'Enregistrer les modifications' }}
                  </button>
                  
                  <Link
                    href="/profile"
                    class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    Annuler
                  </Link>
                </div>

                <!-- Lien vers paramètres avancés -->
                <div class="flex items-center space-x-4 text-sm">
                  <Link href="/settings/password" class="text-green-600 hover:text-green-700">
                    Changer le mot de passe
                  </Link>
                  <Link href="/settings/appearance" class="text-green-600 hover:text-green-700">
                    Apparence
                  </Link>
                </div>
              </div>
            </div>
          </form>
        </div>

        <!-- Section Suppression du compte -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-red-200">
          <div class="p-6">
            <h2 class="text-lg font-semibold text-red-900 mb-2">Zone de danger</h2>
            <p class="text-sm text-red-600 mb-4">
              Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées.
            </p>
            <button
              type="button"
              class="inline-flex items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              @click="showDeleteModal = true"
            >
              Supprimer mon compte
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
