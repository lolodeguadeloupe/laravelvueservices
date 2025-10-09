<script setup>
import { ref, computed, reactive } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const props = defineProps({
  service: {
    type: Object,
    required: true
  },
  availableSlots: {
    type: Array,
    default: () => []
  }
})

// État du formulaire multi-étapes
const currentStep = ref(1)
const totalSteps = 4

// Données du formulaire
const bookingForm = useForm({
  service_id: props.service.id,
  scheduled_date: '',
  scheduled_time: '',
  duration: props.service.duration || 2,
  service_options: [],
  special_requests: '',
  contact_name: '',
  contact_phone: '',
  contact_email: '',
  address: {
    street: '',
    city: '',
    postal_code: '',
    additional_info: ''
  },
  total_price: props.service.price
})

// Calendrier
const selectedDate = ref('')
const selectedTime = ref('')
const availableTimes = ref([
  '08:00', '09:00', '10:00', '11:00', '12:00', 
  '14:00', '15:00', '16:00', '17:00', '18:00'
])

// Options de service
const serviceOptions = ref([
  { id: 'repassage', name: 'Repassage inclus', price: 15, selected: false },
  { id: 'produits', name: 'Produits fournis', price: 10, selected: false },
  { id: 'urgence', name: 'Service urgent', price: 20, selected: false }
])

// Calcul du prix total
const calculatedPrice = computed(() => {
  let total = props.service.price
  
  // Ajouter le prix des options sélectionnées
  serviceOptions.value.forEach(option => {
    if (option.selected) {
      total += option.price
    }
  })
  
  return total
})

// Validation des étapes
const isStep1Valid = computed(() => {
  return selectedDate.value && selectedTime.value
})

const isStep2Valid = computed(() => {
  return true // Étape optionnelle
})

const isStep3Valid = computed(() => {
  return bookingForm.contact_name && 
         bookingForm.contact_phone && 
         bookingForm.contact_email &&
         bookingForm.address.street &&
         bookingForm.address.city &&
         bookingForm.address.postal_code
})

// Navigation entre étapes
const nextStep = () => {
  if (currentStep.value < totalSteps) {
    if (currentStep.value === 1) {
      bookingForm.scheduled_date = selectedDate.value
      bookingForm.scheduled_time = selectedTime.value
    } else if (currentStep.value === 2) {
      bookingForm.service_options = serviceOptions.value
        .filter(option => option.selected)
        .map(option => ({ id: option.id, name: option.name, price: option.price }))
      bookingForm.total_price = calculatedPrice.value
    }
    
    currentStep.value++
  }
}

const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--
  }
}

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
    year: 'numeric'
  }).format(new Date(date))
}

// Soumission du formulaire
const submitBooking = () => {
  bookingForm.post(route('bookings.store'), {
    onSuccess: () => {
      // Redirection vers la page de confirmation
    },
    onError: (errors) => {
      console.error('Erreurs de validation:', errors)
    }
  })
}

// Génération du calendrier
const currentMonth = ref(new Date())
const calendarDays = computed(() => {
  const year = currentMonth.value.getFullYear()
  const month = currentMonth.value.getMonth()
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())
  
  const days = []
  const currentDate = new Date(startDate)
  
  for (let i = 0; i < 42; i++) {
    const isCurrentMonth = currentDate.getMonth() === month
    const isToday = currentDate.toDateString() === new Date().toDateString()
    const isPast = currentDate < new Date().setHours(0, 0, 0, 0)
    const isSelected = currentDate.toDateString() === new Date(selectedDate.value).toDateString()
    
    days.push({
      date: new Date(currentDate),
      day: currentDate.getDate(),
      isCurrentMonth,
      isToday,
      isPast,
      isSelected,
      isAvailable: isCurrentMonth && !isPast
    })
    
    currentDate.setDate(currentDate.getDate() + 1)
  }
  
  return days
})

const selectDate = (day) => {
  if (day.isAvailable) {
    selectedDate.value = day.date.toISOString().split('T')[0]
  }
}

const prevMonth = () => {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() - 1)
}

const nextMonth = () => {
  currentMonth.value = new Date(currentMonth.value.getFullYear(), currentMonth.value.getMonth() + 1)
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-amber-50 py-8">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-4">
              <li>
                <Link :href="route('services.index')" class="text-green-600 hover:text-green-700">
                  Services
                </Link>
              </li>
              <li>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </li>
              <li>
                <Link :href="route('services.show', service.id)" class="text-green-600 hover:text-green-700">
                  {{ service.title }}
                </Link>
              </li>
              <li>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </li>
              <li class="text-gray-500">Réservation</li>
            </ol>
          </nav>
          
          <h1 class="mt-4 text-3xl font-bold text-gray-900">Réserver ce service</h1>
          <p class="mt-2 text-lg text-gray-600">{{ service.title }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Formulaire principal -->
          <div class="lg:col-span-2">
            <!-- Indicateur d'étapes -->
            <div class="mb-8">
              <nav aria-label="Progress">
                <ol class="flex items-center">
                  <li v-for="step in totalSteps" :key="step" class="relative">
                    <div v-if="step !== totalSteps" class="absolute top-4 left-4 -ml-px mt-0.5 h-full w-0.5" 
                         :class="step < currentStep ? 'bg-green-600' : 'bg-gray-300'"></div>
                    
                    <div class="group relative flex items-start">
                      <span class="h-9 flex items-center">
                        <span :class="[
                          'relative z-10 w-8 h-8 flex items-center justify-center rounded-full text-sm font-semibold',
                          step < currentStep ? 'bg-green-600 text-white' : 
                          step === currentStep ? 'bg-green-100 border-2 border-green-600 text-green-600' : 
                          'bg-gray-100 border-2 border-gray-300 text-gray-500'
                        ]">
                          <svg v-if="step < currentStep" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                          </svg>
                          <span v-else>{{ step }}</span>
                        </span>
                      </span>
                      <span class="ml-4 min-w-0 flex flex-col">
                        <span :class="[
                          'text-sm font-medium',
                          step <= currentStep ? 'text-green-600' : 'text-gray-500'
                        ]">
                          {{ step === 1 ? 'Date & Heure' : 
                             step === 2 ? 'Options' : 
                             step === 3 ? 'Informations' : 'Confirmation' }}
                        </span>
                      </span>
                    </div>
                  </li>
                </ol>
              </nav>
            </div>

            <!-- Contenu des étapes -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <!-- Étape 1: Date et heure -->
              <div v-if="currentStep === 1" class="space-y-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Choisissez votre date et heure</h3>
                  
                  <!-- Calendrier -->
                  <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-4">
                      <h4 class="text-lg font-medium text-gray-900">
                        {{ currentMonth.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' }) }}
                      </h4>
                      <div class="flex space-x-2">
                        <button @click="prevMonth" class="p-2 hover:bg-gray-200 rounded-md">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                          </svg>
                        </button>
                        <button @click="nextMonth" class="p-2 hover:bg-gray-200 rounded-md">
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                          </svg>
                        </button>
                      </div>
                    </div>
                    
                    <div class="grid grid-cols-7 gap-1 mb-2">
                      <div v-for="day in ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam']" 
                           :key="day" class="p-2 text-center text-sm font-medium text-gray-700">
                        {{ day }}
                      </div>
                    </div>
                    
                    <div class="grid grid-cols-7 gap-1">
                      <button
                        v-for="day in calendarDays"
                        :key="day.date.toISOString()"
                        @click="selectDate(day)"
                        :disabled="!day.isAvailable"
                        :class="[
                          'p-2 text-sm rounded-md transition-colors',
                          day.isCurrentMonth ? 'text-gray-900' : 'text-gray-300',
                          day.isAvailable ? 'hover:bg-green-100' : 'cursor-not-allowed',
                          day.isSelected ? 'bg-green-600 text-white' : '',
                          day.isToday && !day.isSelected ? 'bg-blue-100 text-blue-600' : ''
                        ]"
                      >
                        {{ day.day }}
                      </button>
                    </div>
                  </div>
                  
                  <!-- Sélection de l'heure -->
                  <div v-if="selectedDate" class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Horaires disponibles</h4>
                    <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                      <button
                        v-for="time in availableTimes"
                        :key="time"
                        @click="selectedTime = time"
                        :class="[
                          'px-4 py-2 text-sm font-medium rounded-md border transition-colors',
                          selectedTime === time 
                            ? 'bg-green-600 text-white border-green-600'
                            : 'bg-white text-gray-700 border-gray-300 hover:bg-green-50 hover:border-green-300'
                        ]"
                      >
                        {{ time }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Étape 2: Options -->
              <div v-if="currentStep === 2" class="space-y-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Options supplémentaires</h3>
                  
                  <div class="space-y-4">
                    <div
                      v-for="option in serviceOptions"
                      :key="option.id"
                      class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                    >
                      <div class="flex items-center">
                        <input
                          :id="option.id"
                          v-model="option.selected"
                          type="checkbox"
                          class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                        >
                        <label :for="option.id" class="ml-3 text-sm font-medium text-gray-900">
                          {{ option.name }}
                        </label>
                      </div>
                      <span class="text-sm font-semibold text-green-600">
                        +{{ formatPrice(option.price) }}
                      </span>
                    </div>
                  </div>
                  
                  <div class="mt-6">
                    <label for="special_requests" class="block text-sm font-medium text-gray-700 mb-2">
                      Demandes spéciales (optionnel)
                    </label>
                    <textarea
                      id="special_requests"
                      v-model="bookingForm.special_requests"
                      rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      placeholder="Précisez vos besoins particuliers..."
                    ></textarea>
                  </div>
                </div>
              </div>

              <!-- Étape 3: Informations -->
              <div v-if="currentStep === 3" class="space-y-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Vos informations</h3>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                      <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom complet *
                      </label>
                      <input
                        id="contact_name"
                        v-model="bookingForm.contact_name"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      >
                    </div>
                    
                    <div>
                      <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Téléphone *
                      </label>
                      <input
                        id="contact_phone"
                        v-model="bookingForm.contact_phone"
                        type="tel"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      >
                    </div>
                    
                    <div class="md:col-span-2">
                      <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email *
                      </label>
                      <input
                        id="contact_email"
                        v-model="bookingForm.contact_email"
                        type="email"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      >
                    </div>
                  </div>
                  
                  <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Adresse d'intervention</h4>
                    
                    <div class="grid grid-cols-1 gap-4">
                      <div>
                        <label for="address_street" class="block text-sm font-medium text-gray-700 mb-2">
                          Adresse *
                        </label>
                        <input
                          id="address_street"
                          v-model="bookingForm.address.street"
                          type="text"
                          required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                        >
                      </div>
                      
                      <div class="grid grid-cols-2 gap-4">
                        <div>
                          <label for="address_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Code postal *
                          </label>
                          <input
                            id="address_postal_code"
                            v-model="bookingForm.address.postal_code"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                        </div>
                        
                        <div>
                          <label for="address_city" class="block text-sm font-medium text-gray-700 mb-2">
                            Ville *
                          </label>
                          <input
                            id="address_city"
                            v-model="bookingForm.address.city"
                            type="text"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                          >
                        </div>
                      </div>
                      
                      <div>
                        <label for="address_additional" class="block text-sm font-medium text-gray-700 mb-2">
                          Informations complémentaires
                        </label>
                        <input
                          id="address_additional"
                          v-model="bookingForm.address.additional_info"
                          type="text"
                          placeholder="Étage, code d'accès, etc."
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Étape 4: Confirmation -->
              <div v-if="currentStep === 4" class="space-y-6">
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-4">Confirmation de réservation</h3>
                  
                  <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-900">Service:</span>
                      <span>{{ service.title }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-900">Date:</span>
                      <span>{{ formatDate(selectedDate) }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-900">Heure:</span>
                      <span>{{ selectedTime }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-900">Prestataire:</span>
                      <span>{{ service.provider.profile?.company_name || service.provider.name }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-900">Contact:</span>
                      <span>{{ bookingForm.contact_name }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                      <span class="font-medium text-gray-900">Adresse:</span>
                      <span>{{ bookingForm.address.street }}, {{ bookingForm.address.city }}</span>
                    </div>
                    
                    <div v-if="bookingForm.service_options.length > 0">
                      <span class="font-medium text-gray-900">Options:</span>
                      <ul class="mt-1 space-y-1">
                        <li v-for="option in bookingForm.service_options" :key="option.id" class="flex justify-between">
                          <span>{{ option.name }}</span>
                          <span>{{ formatPrice(option.price) }}</span>
                        </li>
                      </ul>
                    </div>
                    
                    <div class="border-t pt-4">
                      <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span class="text-green-600">{{ formatPrice(calculatedPrice) }}</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700">
                      <strong>Important:</strong> Cette réservation sera envoyée au prestataire pour validation. 
                      Vous recevrez une confirmation par email une fois acceptée.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Navigation -->
              <div class="mt-8 flex justify-between">
                <button
                  v-if="currentStep > 1"
                  @click="prevStep"
                  class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                  </svg>
                  Précédent
                </button>
                
                <div class="flex-1"></div>
                
                <button
                  v-if="currentStep < totalSteps"
                  @click="nextStep"
                  :disabled="(currentStep === 1 && !isStep1Valid) || 
                           (currentStep === 3 && !isStep3Valid)"
                  class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  Suivant
                  <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                  </svg>
                </button>
                
                <button
                  v-if="currentStep === totalSteps"
                  @click="submitBooking"
                  :disabled="bookingForm.processing"
                  class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                >
                  <svg v-if="bookingForm.processing" class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ bookingForm.processing ? 'Envoi...' : 'Confirmer la réservation' }}
                </button>
              </div>
            </div>
          </div>

          <!-- Sidebar récapitulatif -->
          <div class="lg:col-span-1">
            <div class="sticky top-8">
              <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Récapitulatif</h3>
                
                <!-- Image du service -->
                <div class="mb-4">
                  <img
                    :src="service.images?.[0]?.url || '/placeholder-service.jpg'"
                    :alt="service.title"
                    class="w-full h-32 object-cover rounded-lg"
                  >
                </div>
                
                <!-- Détails du service -->
                <div class="space-y-3">
                  <div>
                    <h4 class="font-medium text-gray-900">{{ service.title }}</h4>
                    <p class="text-sm text-gray-600">{{ service.provider.profile?.company_name || service.provider.name }}</p>
                  </div>
                  
                  <div v-if="selectedDate && selectedTime" class="text-sm">
                    <p class="font-medium text-gray-900">{{ formatDate(selectedDate) }}</p>
                    <p class="text-gray-600">à {{ selectedTime }}</p>
                  </div>
                  
                  <div class="border-t pt-3">
                    <div class="flex justify-between text-sm">
                      <span>Service de base</span>
                      <span>{{ formatPrice(service.price) }}</span>
                    </div>
                    
                    <div v-for="option in serviceOptions.filter(o => o.selected)" :key="option.id" class="flex justify-between text-sm">
                      <span>{{ option.name }}</span>
                      <span>+{{ formatPrice(option.price) }}</span>
                    </div>
                    
                    <div class="border-t mt-3 pt-3">
                      <div class="flex justify-between font-semibold">
                        <span>Total</span>
                        <span class="text-green-600">{{ formatPrice(calculatedPrice) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Contact du prestataire -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                  <h5 class="font-medium text-gray-900 mb-2">Contact prestataire</h5>
                  <div class="text-sm text-gray-600 space-y-1">
                    <p>{{ service.provider.name }}</p>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                      </svg>
                      <span>{{ service.average_rating || '5.0' }}/5 ({{ service.reviews_count || 0 }} avis)</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>