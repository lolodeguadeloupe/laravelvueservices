<script setup>
import { ref, computed, onMounted } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const props = defineProps({
  booking: {
    type: Object,
    required: true
  },
  clientSecret: {
    type: String,
    required: true
  },
  stripePublicKey: {
    type: String,
    required: true
  },
  paymentMethods: {
    type: Array,
    default: () => ['card', 'paypal', 'apple_pay']
  }
})

// État du paiement
const isProcessing = ref(false)
const paymentError = ref('')
const paymentSuccess = ref(false)
const selectedPaymentMethod = ref('card')

// Stripe Elements
const stripe = ref(null)
const elements = ref(null)
const cardElement = ref(null)
const cardErrors = ref('')

// Formulaire de paiement
const paymentForm = useForm({
  payment_method: 'card',
  billing_details: {
    name: '',
    email: '',
    address: {
      line1: '',
      line2: '',
      city: '',
      postal_code: '',
      country: 'FR'
    }
  },
  terms_accepted: false,
  newsletter_opt_in: false
})

// Calculs
const subtotal = computed(() => {
  let total = props.booking.service.price
  if (props.booking.service_options) {
    props.booking.service_options.forEach(option => {
      total += option.price
    })
  }
  return total
})

const serviceFee = computed(() => {
  return Math.round(subtotal.value * 0.05 * 100) / 100 // 5% de commission
})

const totalAmount = computed(() => {
  return subtotal.value + serviceFee.value
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
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

// Initialisation de Stripe
onMounted(async () => {
  if (typeof window !== 'undefined' && window.Stripe) {
    stripe.value = window.Stripe(props.stripePublicKey)
    
    elements.value = stripe.value.elements({
      clientSecret: props.clientSecret,
      appearance: {
        theme: 'stripe',
        variables: {
          colorPrimary: '#10b981', // green-500
          colorBackground: '#ffffff',
          colorText: '#1f2937',
          colorDanger: '#ef4444',
          fontFamily: 'system-ui, sans-serif',
          borderRadius: '8px'
        }
      }
    })

    cardElement.value = elements.value.create('payment', {
      layout: 'tabs'
    })
    
    cardElement.value.mount('#card-element')
    
    cardElement.value.on('change', (event) => {
      cardErrors.value = event.error ? event.error.message : ''
    })
  }
})

// Traitement du paiement
const processPayment = async () => {
  if (!stripe.value || !elements.value) {
    paymentError.value = 'Stripe n\'est pas initialisé correctement.'
    return
  }

  isProcessing.value = true
  paymentError.value = ''

  try {
    // Confirmer le paiement avec Stripe
    const { error, paymentIntent } = await stripe.value.confirmPayment({
      elements: elements.value,
      confirmParams: {
        return_url: `${window.location.origin}/payments/success`,
        payment_method_data: {
          billing_details: {
            name: paymentForm.billing_details.name,
            email: paymentForm.billing_details.email,
            address: {
              line1: paymentForm.billing_details.address.line1,
              line2: paymentForm.billing_details.address.line2,
              city: paymentForm.billing_details.address.city,
              postal_code: paymentForm.billing_details.address.postal_code,
              country: paymentForm.billing_details.address.country
            }
          }
        }
      },
      redirect: 'if_required'
    })

    if (error) {
      paymentError.value = error.message
    } else if (paymentIntent.status === 'succeeded') {
      // Paiement réussi
      paymentSuccess.value = true
      
      // Envoyer confirmation au backend
      await fetch(route('payments.confirm'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({
          payment_intent_id: paymentIntent.id,
          booking_id: props.booking.id
        })
      })

      // Redirection vers la page de succès
      setTimeout(() => {
        window.location.href = route('bookings.show', props.booking.id)
      }, 2000)
    }
  } catch (err) {
    paymentError.value = 'Une erreur inattendue s\'est produite.'
    console.error('Erreur de paiement:', err)
  } finally {
    isProcessing.value = false
  }
}

// Sélection de la méthode de paiement
const selectPaymentMethod = (method) => {
  selectedPaymentMethod.value = method
  paymentForm.payment_method = method
}

// Validation du formulaire
const isFormValid = computed(() => {
  return paymentForm.billing_details.name &&
         paymentForm.billing_details.email &&
         paymentForm.billing_details.address.line1 &&
         paymentForm.billing_details.address.city &&
         paymentForm.billing_details.address.postal_code &&
         paymentForm.terms_accepted &&
         !cardErrors.value
})
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-amber-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('bookings.index')" class="text-green-600 hover:text-green-700">
                  Réservations
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
              <li class="text-gray-500">Paiement</li>
            </ol>
          </nav>
          
          <h1 class="text-3xl font-bold text-gray-900">Paiement sécurisé</h1>
          <p class="mt-2 text-lg text-gray-600">Finalisez votre réservation</p>
        </div>

        <!-- Message de succès -->
        <div v-if="paymentSuccess" class="mb-8 bg-green-50 border border-green-200 rounded-lg p-6">
          <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
              <h3 class="text-lg font-medium text-green-900">Paiement réussi !</h3>
              <p class="text-green-700">Votre réservation a été confirmée. Redirection en cours...</p>
            </div>
          </div>
        </div>

        <!-- Message d'erreur -->
        <div v-if="paymentError" class="mb-8 bg-red-50 border border-red-200 rounded-lg p-4">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-red-700">{{ paymentError }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
          <!-- Formulaire de paiement -->
          <div class="space-y-6">
            <!-- Méthodes de paiement -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Méthode de paiement</h3>
              
              <div class="space-y-3">
                <!-- Carte bancaire -->
                <label :class="[
                  'flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors',
                  selectedPaymentMethod === 'card' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'
                ]">
                  <input
                    type="radio"
                    value="card"
                    v-model="selectedPaymentMethod"
                    @change="selectPaymentMethod('card')"
                    class="sr-only"
                  >
                  <div class="flex items-center space-x-3">
                    <div :class="[
                      'w-4 h-4 rounded-full border-2',
                      selectedPaymentMethod === 'card' ? 'border-green-500 bg-green-500' : 'border-gray-300'
                    ]">
                      <div v-if="selectedPaymentMethod === 'card'" class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                    </div>
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span class="font-medium text-gray-900">Carte bancaire</span>
                  </div>
                  <div class="ml-auto flex space-x-2">
                    <img src="/images/visa.png" alt="Visa" class="h-6">
                    <img src="/images/mastercard.png" alt="Mastercard" class="h-6">
                    <img src="/images/amex.png" alt="American Express" class="h-6">
                  </div>
                </label>

                <!-- PayPal -->
                <label v-if="paymentMethods.includes('paypal')" :class="[
                  'flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors',
                  selectedPaymentMethod === 'paypal' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'
                ]">
                  <input
                    type="radio"
                    value="paypal"
                    v-model="selectedPaymentMethod"
                    @change="selectPaymentMethod('paypal')"
                    class="sr-only"
                  >
                  <div class="flex items-center space-x-3">
                    <div :class="[
                      'w-4 h-4 rounded-full border-2',
                      selectedPaymentMethod === 'paypal' ? 'border-green-500 bg-green-500' : 'border-gray-300'
                    ]">
                      <div v-if="selectedPaymentMethod === 'paypal'" class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                    </div>
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M7.076 21.337H2.47a.641.641 0 0 1-.633-.74L4.944.901C5.026.382 5.474 0 5.998 0h7.46c2.57 0 4.578.543 5.69 1.81 1.01 1.15 1.304 2.42 1.012 4.287-.023.143-.047.288-.077.437-.983 5.05-4.349 6.797-8.647 6.797h-2.19c-.524 0-.968.382-1.05.9l-1.12 7.106zm14.146-14.42a3.35 3.35 0 0 0-.607-.421c-.962-.6-2.393-.84-4.267-.84h-2.19c-.524 0-.968.382-1.05.9L11.79 10.8h2.19c4.298 0 7.664-1.747 8.647-6.797.03-.149.054-.294.077-.437.292-1.867-.002-3.137-1.012-4.287z"/>
                    </svg>
                    <span class="font-medium text-gray-900">PayPal</span>
                  </div>
                </label>

                <!-- Apple Pay -->
                <label v-if="paymentMethods.includes('apple_pay')" :class="[
                  'flex items-center p-4 border-2 rounded-lg cursor-pointer transition-colors',
                  selectedPaymentMethod === 'apple_pay' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'
                ]">
                  <input
                    type="radio"
                    value="apple_pay"
                    v-model="selectedPaymentMethod"
                    @change="selectPaymentMethod('apple_pay')"
                    class="sr-only"
                  >
                  <div class="flex items-center space-x-3">
                    <div :class="[
                      'w-4 h-4 rounded-full border-2',
                      selectedPaymentMethod === 'apple_pay' ? 'border-green-500 bg-green-500' : 'border-gray-300'
                    ]">
                      <div v-if="selectedPaymentMethod === 'apple_pay'" class="w-2 h-2 bg-white rounded-full mx-auto mt-0.5"></div>
                    </div>
                    <svg class="w-6 h-6 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                      <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
                    </svg>
                    <span class="font-medium text-gray-900">Apple Pay</span>
                  </div>
                </label>
              </div>
            </div>

            <!-- Formulaire carte bancaire -->
            <div v-if="selectedPaymentMethod === 'card'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de carte</h3>
              
              <!-- Stripe Elements -->
              <div id="card-element" class="mb-4"></div>
              
              <div v-if="cardErrors" class="text-red-600 text-sm mb-4">
                {{ cardErrors }}
              </div>
            </div>

            <!-- Informations de facturation -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations de facturation</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom complet *
                  </label>
                  <input
                    id="billing_name"
                    v-model="paymentForm.billing_details.name"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  >
                </div>
                
                <div>
                  <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email *
                  </label>
                  <input
                    id="billing_email"
                    v-model="paymentForm.billing_details.email"
                    type="email"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  >
                </div>
                
                <div class="md:col-span-2">
                  <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">
                    Adresse *
                  </label>
                  <input
                    id="billing_address"
                    v-model="paymentForm.billing_details.address.line1"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  >
                </div>
                
                <div>
                  <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                    Code postal *
                  </label>
                  <input
                    id="billing_postal_code"
                    v-model="paymentForm.billing_details.address.postal_code"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  >
                </div>
                
                <div>
                  <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-2">
                    Ville *
                  </label>
                  <input
                    id="billing_city"
                    v-model="paymentForm.billing_details.address.city"
                    type="text"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  >
                </div>
              </div>
            </div>

            <!-- Conditions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <div class="space-y-4">
                <div class="flex items-start">
                  <input
                    id="terms_accepted"
                    v-model="paymentForm.terms_accepted"
                    type="checkbox"
                    required
                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded mt-1"
                  >
                  <label for="terms_accepted" class="ml-3 text-sm text-gray-700">
                    J'accepte les 
                    <Link href="/terms" class="text-green-600 hover:text-green-700 underline">conditions d'utilisation</Link>
                    et la 
                    <Link href="/privacy" class="text-green-600 hover:text-green-700 underline">politique de confidentialité</Link>
                    *
                  </label>
                </div>
                
                <div class="flex items-start">
                  <input
                    id="newsletter_opt_in"
                    v-model="paymentForm.newsletter_opt_in"
                    type="checkbox"
                    class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded mt-1"
                  >
                  <label for="newsletter_opt_in" class="ml-3 text-sm text-gray-700">
                    Je souhaite recevoir des offres et nouveautés par email
                  </label>
                </div>
              </div>
            </div>

            <!-- Bouton de paiement -->
            <div class="text-center">
              <button
                @click="processPayment"
                :disabled="!isFormValid || isProcessing"
                class="w-full bg-green-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
              >
                <svg v-if="isProcessing" class="inline w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ isProcessing ? 'Traitement en cours...' : `Payer ${formatPrice(totalAmount)}` }}
              </button>
              
              <!-- Indicateurs de sécurité -->
              <div class="mt-4 flex items-center justify-center space-x-4 text-sm text-gray-500">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                  Paiement sécurisé SSL
                </div>
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                  Protégé par Stripe
                </div>
              </div>
            </div>
          </div>

          <!-- Récapitulatif -->
          <div class="lg:col-span-1">
            <div class="sticky top-8 space-y-6">
              <!-- Récapitulatif de commande -->
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif</h3>
                
                <!-- Service -->
                <div class="space-y-4">
                  <div class="flex items-start space-x-4">
                    <img
                      :src="booking.service.images?.[0]?.url || '/placeholder-service.jpg'"
                      :alt="booking.service.title"
                      class="w-16 h-16 object-cover rounded-lg"
                    >
                    <div class="flex-1">
                      <h4 class="font-medium text-gray-900">{{ booking.service.title }}</h4>
                      <p class="text-sm text-gray-600">{{ booking.service.provider.profile?.company_name }}</p>
                      <p class="text-sm text-gray-500">{{ formatDate(booking.scheduled_date) }}</p>
                    </div>
                    <span class="font-semibold text-gray-900">{{ formatPrice(booking.service.price) }}</span>
                  </div>
                  
                  <!-- Options -->
                  <div v-if="booking.service_options && booking.service_options.length > 0" class="space-y-2">
                    <div
                      v-for="option in booking.service_options"
                      :key="option.id"
                      class="flex justify-between text-sm"
                    >
                      <span class="text-gray-600">{{ option.name }}</span>
                      <span class="text-gray-900">{{ formatPrice(option.price) }}</span>
                    </div>
                  </div>
                  
                  <!-- Totaux -->
                  <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Sous-total</span>
                      <span class="text-gray-900">{{ formatPrice(subtotal) }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                      <span class="text-gray-600">Frais de service</span>
                      <span class="text-gray-900">{{ formatPrice(serviceFee) }}</span>
                    </div>
                    
                    <div class="flex justify-between text-lg font-semibold border-t pt-2">
                      <span class="text-gray-900">Total</span>
                      <span class="text-green-600">{{ formatPrice(totalAmount) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Informations du prestataire -->
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Votre prestataire</h3>
                
                <div class="flex items-center space-x-3">
                  <img
                    :src="booking.service.provider.profile?.avatar || '/default-avatar.jpg'"
                    :alt="booking.service.provider.name"
                    class="h-12 w-12 rounded-full object-cover"
                  >
                  <div>
                    <h4 class="font-medium text-gray-900">{{ booking.service.provider.name }}</h4>
                    <p class="text-sm text-gray-600">{{ booking.service.provider.profile?.company_name }}</p>
                    <div class="flex items-center mt-1">
                      <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                      <span class="text-sm text-gray-600">{{ booking.service.average_rating || '5.0' }}/5</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Garanties -->
              <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-green-900 mb-4">Nos garanties</h3>
                
                <div class="space-y-3">
                  <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <div>
                      <h4 class="font-medium text-green-900">Service garanti</h4>
                      <p class="text-sm text-green-700">Qualité de service garantie ou remboursé</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                      <h4 class="font-medium text-green-900">Ponctualité</h4>
                      <p class="text-sm text-green-700">Arrivée à l'heure garantie</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <div>
                      <h4 class="font-medium text-green-900">Support 24/7</h4>
                      <p class="text-sm text-green-700">Assistance disponible à tout moment</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Script Stripe -->
    <script src="https://js.stripe.com/v3/"></script>
  </AppLayout>
</template>