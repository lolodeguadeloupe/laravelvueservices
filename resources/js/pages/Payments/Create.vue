<template>
  <AppLayout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- En-tête -->
      <div class="mb-8">
        <Link :href="route('bookings.show', booking.uuid)" class="inline-flex items-center text-sm text-gray-500 hover:text-primary mb-4">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
          </svg>
          Retour à la réservation
        </Link>
        <h1 class="text-3xl font-bold text-gray-900">Paiement sécurisé</h1>
        <p class="text-gray-600 mt-2">Payez votre service en toute sécurité avec Stripe</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Récapitulatif -->
        <div class="lg:order-2">
          <div class="bg-gray-50 rounded-lg p-6 sticky top-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif</h2>
            
            <!-- Service -->
            <div class="mb-4">
              <h3 class="font-medium text-gray-900">{{ booking.service.title }}</h3>
              <p class="text-sm text-gray-600">{{ booking.service.short_description }}</p>
            </div>

            <!-- Prestataire -->
            <div class="mb-4 pb-4 border-b border-gray-200">
              <div class="flex items-center">
                <div class="w-10 h-10 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium mr-3">
                  {{ booking.provider.profile?.first_name?.charAt(0) + booking.provider.profile?.last_name?.charAt(0) }}
                </div>
                <div>
                  <p class="font-medium text-gray-900">
                    {{ booking.provider.profile?.first_name + ' ' + booking.provider.profile?.last_name }}
                  </p>
                  <p class="text-sm text-gray-500">{{ booking.provider.email }}</p>
                </div>
              </div>
            </div>

            <!-- Détails -->
            <div class="space-y-3 mb-6">
              <div class="flex justify-between">
                <span class="text-gray-600">Date souhaitée</span>
                <span class="text-gray-900">{{ new Date(booking.preferred_datetime).toLocaleDateString('fr-FR') }}</span>
              </div>
              
              <div v-if="booking.estimated_duration" class="flex justify-between">
                <span class="text-gray-600">Durée estimée</span>
                <span class="text-gray-900">{{ Math.floor(booking.estimated_duration / 60) }}h{{ booking.estimated_duration % 60 > 0 ? (booking.estimated_duration % 60).toString().padStart(2, '0') : '' }}</span>
              </div>

              <div class="flex justify-between border-t border-gray-200 pt-3">
                <span class="text-gray-600">Prix du service</span>
                <span class="text-gray-900">{{ amount }}€</span>
              </div>
              
              <div class="flex justify-between">
                <span class="text-gray-600">Frais de service</span>
                <span class="text-gray-900">Inclus</span>
              </div>
            </div>

            <!-- Total -->
            <div class="border-t border-gray-200 pt-4">
              <div class="flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-900">Total</span>
                <span class="text-2xl font-bold text-primary">{{ amount }}€</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Formulaire de paiement -->
        <div class="lg:order-1">
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Informations de paiement</h2>

            <div v-if="!clientSecret" class="text-center py-8">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
              <p class="text-gray-500 mt-2">Préparation du paiement...</p>
            </div>

            <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
              <div class="flex">
                <svg class="w-5 h-5 text-red-400 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                  <h3 class="text-sm font-medium text-red-800">Erreur de paiement</h3>
                  <p class="text-sm text-red-700 mt-1">{{ error }}</p>
                </div>
              </div>
            </div>

            <form v-else @submit.prevent="handleSubmit" ref="paymentForm">
              <div id="payment-element" class="mb-6"></div>

              <button
                type="submit"
                :disabled="!isPaymentReady || isProcessing"
                class="w-full px-4 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
              >
                <span v-if="isProcessing" class="flex items-center justify-center">
                  <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Traitement en cours...
                </span>
                <span v-else>
                  Payer {{ amount }}€
                </span>
              </button>

              <p class="text-xs text-gray-500 text-center mt-4">
                Paiement sécurisé par Stripe. Vos informations bancaires sont chiffrées et protégées.
              </p>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { loadStripe } from '@stripe/stripe-js'

const props = defineProps({
  booking: Object,
  stripeKey: String,
})

// État du composant
const clientSecret = ref(null)
const stripe = ref(null)
const elements = ref(null)
const paymentElement = ref(null)
const isPaymentReady = ref(false)
const isProcessing = ref(false)
const error = ref(null)

// Montant à payer
const amount = computed(() => {
  return props.booking.final_price || props.booking.quoted_price
})

// Initialisation de Stripe
onMounted(async () => {
  try {
    // Charger Stripe
    stripe.value = await loadStripe(props.stripeKey)
    
    // Créer le PaymentIntent
    const response = await fetch(`/bookings/${props.booking.id}/payment/intent`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.error || 'Erreur lors de la création du paiement')
    }

    clientSecret.value = data.client_secret

    // Créer les éléments Stripe
    elements.value = stripe.value.elements({
      clientSecret: clientSecret.value,
      appearance: {
        theme: 'stripe',
        variables: {
          colorPrimary: '#059669', // primary color
        },
      },
    })

    // Monter l'élément de paiement
    paymentElement.value = elements.value.create('payment')
    paymentElement.value.mount('#payment-element')

    paymentElement.value.on('ready', () => {
      isPaymentReady.value = true
    })

    paymentElement.value.on('change', (event) => {
      if (event.error) {
        error.value = event.error.message
      } else {
        error.value = null
      }
    })

  } catch (err) {
    error.value = err.message
  }
})

// Traitement du paiement
const handleSubmit = async () => {
  if (!stripe.value || !elements.value) {
    return
  }

  isProcessing.value = true
  error.value = null

  try {
    const { error: submitError } = await elements.value.submit()

    if (submitError) {
      error.value = submitError.message
      return
    }

    const { error: confirmError, paymentIntent } = await stripe.value.confirmPayment({
      elements: elements.value,
      redirect: 'if_required',
    })

    if (confirmError) {
      error.value = confirmError.message
    } else if (paymentIntent && paymentIntent.status === 'succeeded') {
      // Confirmer le paiement côté serveur
      const response = await fetch('/payments/confirm', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
          payment_intent_id: paymentIntent.id,
        }),
      })

      const result = await response.json()

      if (response.ok && result.success) {
        // Rediriger vers la page de succès
        router.visit(result.redirect_url)
      } else {
        error.value = result.error || 'Erreur lors de la confirmation du paiement'
      }
    }
  } catch (err) {
    error.value = err.message
  } finally {
    isProcessing.value = false
  }
}
</script>