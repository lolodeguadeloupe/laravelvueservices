<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- En-tête -->
      <div class="mb-8">
        <Link :href="route('services.show', service.id)" class="inline-flex items-center text-sm text-gray-500 hover:text-primary mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Retour au service
        </Link>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Faire une demande de service</h1>
        <p class="text-gray-600">Complétez les informations ci-dessous pour faire votre demande</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Récapitulatif du service -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif</h2>
            
            <!-- Service -->
            <div class="mb-6">
              <div class="aspect-w-16 aspect-h-9 mb-4">
                <img 
                  :src="service.images?.[0]?.url || '/default-service.jpg'" 
                  :alt="service.title"
                  class="w-full h-32 object-cover rounded-lg"
                />
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">{{ service.title }}</h3>
              <p class="text-sm text-gray-600 mb-3 line-clamp-3">{{ service.description }}</p>
              <div class="flex items-center justify-between">
                <span class="text-sm text-gray-500">À partir de</span>
                <span class="text-lg font-bold text-primary">{{ service.price }}€</span>
              </div>
            </div>

            <!-- Prestataire -->
            <div class="border-t border-gray-100 pt-6">
              <div class="flex items-center">
                <div class="w-12 h-12 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium mr-4">
                  {{ service.provider.profile?.first_name?.charAt(0) }}{{ service.provider.profile?.last_name?.charAt(0) }}
                </div>
                <div>
                  <p class="font-medium text-gray-900">
                    {{ service.provider.profile?.first_name }} {{ service.provider.profile?.last_name }}
                  </p>
                  <p class="text-sm text-gray-500">
                    {{ service.provider.profile?.city }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Formulaire de demande -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <Form
              :action="route('bookings.store')"
              method="post"
              #default="{ errors, processing, wasSuccessful }"
            >
              <input type="hidden" name="service_id" :value="service.id" />

              <div class="space-y-6">
                <!-- Date et heure préférées -->
                <div>
                  <label for="preferred_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                    Date et heure souhaitées *
                  </label>
                  <input
                    type="datetime-local"
                    name="preferred_datetime"
                    id="preferred_datetime"
                    :min="new Date().toISOString().slice(0, 16)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                    required
                  />
                  <div v-if="errors.preferred_datetime" class="mt-1 text-sm text-red-600">
                    {{ errors.preferred_datetime }}
                  </div>
                </div>

                <!-- Adresse d'intervention -->
                <div>
                  <h3 class="text-lg font-medium text-gray-900 mb-4">Adresse d'intervention</h3>
                  <div class="grid grid-cols-1 gap-4">
                    <div>
                      <label for="street" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse *
                      </label>
                      <input
                        type="text"
                        name="client_address[street]"
                        id="street"
                        placeholder="123 Rue de la Paix"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                        required
                      />
                      <div v-if="errors['client_address.street']" class="mt-1 text-sm text-red-600">
                        {{ errors['client_address.street'] }}
                      </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                      <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                          Code postal *
                        </label>
                        <input
                          type="text"
                          name="client_address[postal_code]"
                          id="postal_code"
                          placeholder="75001"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                          required
                        />
                        <div v-if="errors['client_address.postal_code']" class="mt-1 text-sm text-red-600">
                          {{ errors['client_address.postal_code'] }}
                        </div>
                      </div>

                      <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                          Ville *
                        </label>
                        <input
                          type="text"
                          name="client_address[city]"
                          id="city"
                          placeholder="Paris"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                          required
                        />
                        <div v-if="errors['client_address.city']" class="mt-1 text-sm text-red-600">
                          {{ errors['client_address.city'] }}
                        </div>
                      </div>
                    </div>

                    <div>
                      <label for="additional_info" class="block text-sm font-medium text-gray-700 mb-2">
                        Informations complémentaires
                      </label>
                      <input
                        type="text"
                        name="client_address[additional_info]"
                        id="additional_info"
                        placeholder="Étage, code d'accès, etc."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary"
                      />
                      <div v-if="errors['client_address.additional_info']" class="mt-1 text-sm text-red-600">
                        {{ errors['client_address.additional_info'] }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Notes du client -->
                <div>
                  <label for="client_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Détails de votre demande
                  </label>
                  <textarea
                    name="client_notes"
                    id="client_notes"
                    rows="4"
                    placeholder="Décrivez votre demande, vos attentes, ou toute information utile pour le prestataire..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary focus:border-primary resize-none"
                  ></textarea>
                  <div v-if="errors.client_notes" class="mt-1 text-sm text-red-600">
                    {{ errors.client_notes }}
                  </div>
                </div>

                <!-- Information importante -->
                <div class="bg-primary/5 border border-primary/20 rounded-lg p-4">
                  <div class="flex">
                    <svg class="w-5 h-5 text-primary mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                      <h4 class="text-sm font-medium text-primary mb-1">À savoir</h4>
                      <ul class="text-sm text-gray-700 space-y-1">
                        <li>• Votre demande sera envoyée au prestataire pour validation</li>
                        <li>• Le prestataire vous contactera pour confirmer les détails et le tarif</li>
                        <li>• Vous pouvez annuler gratuitement jusqu'à l'acceptation de la demande</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Boutons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                  <Link 
                    :href="route('services.show', service.id)"
                    class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    Annuler
                  </Link>
                  
                  <button
                    type="submit"
                    :disabled="processing"
                    class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center"
                  >
                    <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    {{ processing ? 'Envoi en cours...' : 'Envoyer la demande' }}
                  </button>
                </div>
              </div>
            </Form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { Form } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'

defineProps({
  service: Object
})
</script>