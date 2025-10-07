<template>
  <div class="min-h-screen bg-gradient-to-br from-green-50 to-brown-50 dark:from-gray-900 dark:to-gray-800">
    <Head title="Inscription Prestataire" />
    
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
          <Link href="/" class="flex items-center gap-2 text-green-600 font-bold text-xl">
            <Icon name="home" class="w-6 h-6" />
            ServicesPro
          </Link>
          <div class="text-sm text-gray-600 dark:text-gray-400">
            Déjà inscrit ? 
            <Link href="/login" class="text-green-600 hover:text-green-700 font-medium">
              Se connecter
            </Link>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Progress Indicator -->
      <div class="mb-8">
        <div class="flex items-center justify-center">
          <div class="flex items-center space-x-4">
            <div
              v-for="step in steps"
              :key="step.number"
              class="flex items-center"
            >
              <div
                :class="[
                  'w-10 h-10 rounded-full flex items-center justify-center text-sm font-medium transition-colors',
                  step.number <= currentStep
                    ? 'bg-green-600 text-white'
                    : 'bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                ]"
              >
                <Icon v-if="step.number < currentStep" name="check" class="w-5 h-5" />
                <span v-else>{{ step.number }}</span>
              </div>
              <div class="ml-2 text-sm">
                <div
                  :class="[
                    'font-medium',
                    step.number <= currentStep
                      ? 'text-gray-900 dark:text-white'
                      : 'text-gray-500 dark:text-gray-400'
                  ]"
                >
                  {{ step.title }}
                </div>
                <div class="text-gray-500 dark:text-gray-400 text-xs">
                  {{ step.description }}
                </div>
              </div>
              <Icon
                v-if="step.number < steps.length"
                name="chevron-right"
                class="w-5 h-5 text-gray-400 mx-4"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Form Container -->
      <Card class="p-8">
        <!-- Step 1: Informations personnelles -->
        <div v-if="currentStep === 1">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Informations personnelles
          </h2>
          
          <form @submit.prevent="submitStep1" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Prénom *
                </label>
                <Input
                  v-model="form.step1.first_name"
                  :error="errors.first_name"
                  placeholder="Votre prénom"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Nom *
                </label>
                <Input
                  v-model="form.step1.last_name"
                  :error="errors.last_name"
                  placeholder="Votre nom de famille"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Adresse email *
              </label>
              <Input
                v-model="form.step1.email"
                type="email"
                :error="errors.email"
                placeholder="votre@email.com"
                required
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Numéro de téléphone *
              </label>
              <Input
                v-model="form.step1.phone"
                type="tel"
                :error="errors.phone"
                placeholder="+33 6 12 34 56 78"
                required
              />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Mot de passe *
                </label>
                <Input
                  v-model="form.step1.password"
                  type="password"
                  :error="errors.password"
                  placeholder="••••••••"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Confirmer le mot de passe *
                </label>
                <Input
                  v-model="form.step1.password_confirmation"
                  type="password"
                  :error="errors.password_confirmation"
                  placeholder="••••••••"
                  required
                />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Date de naissance *
                </label>
                <Input
                  v-model="form.step1.date_of_birth"
                  type="date"
                  :error="errors.date_of_birth"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Genre *
                </label>
                <Select v-model="form.step1.gender" :error="errors.gender" required>
                  <option value="">Sélectionner</option>
                  <option value="male">Homme</option>
                  <option value="female">Femme</option>
                  <option value="other">Autre</option>
                </Select>
              </div>
            </div>

            <!-- Consentements -->
            <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
              <div class="flex items-start">
                <input
                  id="terms"
                  v-model="form.step1.terms_accepted"
                  type="checkbox"
                  class="mt-1 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                  required
                />
                <label for="terms" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                  J'accepte les <Link href="/terms" class="text-green-600 hover:underline">conditions générales d'utilisation</Link> *
                </label>
              </div>

              <div class="flex items-start">
                <input
                  id="privacy"
                  v-model="form.step1.privacy_accepted"
                  type="checkbox"
                  class="mt-1 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                  required
                />
                <label for="privacy" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                  J'accepte la <Link href="/privacy" class="text-green-600 hover:underline">politique de confidentialité</Link> *
                </label>
              </div>
            </div>

            <div class="flex justify-end pt-6">
              <Button type="submit" :loading="isLoading" class="px-8">
                Continuer
                <Icon name="chevron-right" class="w-4 h-4 ml-2" />
              </Button>
            </div>
          </form>
        </div>

        <!-- Step 2: Informations professionnelles -->
        <div v-if="currentStep === 2">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Informations professionnelles
          </h2>
          
          <form @submit.prevent="submitStep2" class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Nom de société (optionnel)
              </label>
              <Input
                v-model="form.step2.company_name"
                :error="errors.company_name"
                placeholder="Nom de votre entreprise"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description professionnelle *
              </label>
              <textarea
                v-model="form.step2.bio"
                :class="[
                  'w-full px-3 py-2 border rounded-md shadow-sm focus:ring-green-500 focus:border-green-500',
                  errors.bio ? 'border-red-300' : 'border-gray-300 dark:border-gray-600'
                ]"
                rows="4"
                placeholder="Décrivez votre expérience, vos compétences et ce qui vous distingue..."
                required
              ></textarea>
              <p class="text-sm text-gray-500 mt-1">Minimum 50 caractères</p>
              <p v-if="errors.bio" class="text-red-500 text-sm mt-1">{{ errors.bio }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Années d'expérience *
                </label>
                <Input
                  v-model="form.step2.experience"
                  type="number"
                  min="0"
                  max="50"
                  :error="errors.experience"
                  placeholder="0"
                  required
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Rayon d'intervention (km) *
                </label>
                <Input
                  v-model="form.step2.intervention_radius"
                  type="number"
                  min="1"
                  max="100"
                  :error="errors.intervention_radius"
                  placeholder="10"
                  required
                />
              </div>
            </div>

            <!-- Adresse -->
            <div class="space-y-4">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Adresse</h3>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Adresse *
                </label>
                <Input
                  v-model="form.step2.address"
                  :error="errors.address"
                  placeholder="123 rue de la République"
                  required
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Ville *
                  </label>
                  <Input
                    v-model="form.step2.city"
                    :error="errors.city"
                    placeholder="Paris"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Code postal *
                  </label>
                  <Input
                    v-model="form.step2.postal_code"
                    :error="errors.postal_code"
                    placeholder="75001"
                    required
                  />
                </div>
              </div>
            </div>

            <!-- Spécialités -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Spécialités * (maximum 5)
              </label>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <div
                  v-for="category in categories"
                  :key="category.id"
                  class="flex items-center"
                >
                  <input
                    :id="`category-${category.id}`"
                    v-model="form.step2.specialties"
                    :value="category.id"
                    type="checkbox"
                    :disabled="form.step2.specialties.length >= 5 && !form.step2.specialties.includes(category.id)"
                    class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500 disabled:opacity-50"
                  />
                  <label
                    :for="`category-${category.id}`"
                    class="ml-3 text-sm text-gray-700 dark:text-gray-300"
                  >
                    {{ category.name }}
                  </label>
                </div>
              </div>
              <p v-if="errors.specialties" class="text-red-500 text-sm mt-1">{{ errors.specialties }}</p>
            </div>

            <!-- Langues -->
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Langues parlées *
              </label>
              <LanguageSelector v-model="form.step2.languages" :error="errors.languages" />
            </div>

            <!-- Demandes urgentes -->
            <div class="flex items-center">
              <input
                id="urgent"
                v-model="form.step2.accepts_urgent_requests"
                type="checkbox"
                class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
              />
              <label for="urgent" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                J'accepte les demandes urgentes (interventions le jour même)
              </label>
            </div>

            <div class="flex justify-between pt-6">
              <Button variant="outline" @click="currentStep = 1">
                <Icon name="chevron-left" class="w-4 h-4 mr-2" />
                Retour
              </Button>
              <Button type="submit" :loading="isLoading" class="px-8">
                Continuer
                <Icon name="chevron-right" class="w-4 h-4 ml-2" />
              </Button>
            </div>
          </form>
        </div>

        <!-- Step 3: Documents et finalisation -->
        <div v-if="currentStep === 3">
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Documents et finalisation
          </h2>
          
          <form @submit.prevent="submitStep3" class="space-y-6">
            <!-- Upload documents -->
            <div class="space-y-6">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Documents requis</h3>
              
              <FileUpload
                v-model="form.step3.identity_document"
                label="Pièce d'identité *"
                accept=".pdf,.jpg,.jpeg,.png"
                :error="errors.identity_document"
                description="CNI, passeport ou permis de conduire (PDF, JPG, PNG - max 5MB)"
                required
              />
              
              <FileUpload
                v-model="form.step3.insurance_certificate"
                label="Certificat d'assurance professionnelle *"
                accept=".pdf,.jpg,.jpeg,.png"
                :error="errors.insurance_certificate"
                description="Attestation d'assurance responsabilité civile professionnelle"
                required
              />
              
              <FileUpload
                v-model="form.step3.professional_license"
                label="Licence/Diplôme professionnel (optionnel)"
                accept=".pdf,.jpg,.jpeg,.png"
                :error="errors.professional_license"
                description="Diplômes, certifications ou licences liés à votre activité"
              />
            </div>

            <!-- Informations bancaires -->
            <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Informations bancaires</h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Pour recevoir vos paiements après validation de votre compte.
              </p>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Titulaire du compte *
                </label>
                <Input
                  v-model="form.step3.bank_account_holder"
                  :error="errors.bank_account_holder"
                  placeholder="Nom complet du titulaire"
                  required
                />
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    IBAN *
                  </label>
                  <Input
                    v-model="form.step3.iban"
                    :error="errors.iban"
                    placeholder="FR76 1234 5678 9012 3456 789"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    BIC *
                  </label>
                  <Input
                    v-model="form.step3.bic"
                    :error="errors.bic"
                    placeholder="BNPAFRPPXXX"
                    required
                  />
                </div>
              </div>
            </div>

            <!-- Contact d'urgence -->
            <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Contact d'urgence</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Nom complet *
                  </label>
                  <Input
                    v-model="form.step3.emergency_contact_name"
                    :error="errors.emergency_contact_name"
                    placeholder="Nom de votre contact d'urgence"
                    required
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Téléphone *
                  </label>
                  <Input
                    v-model="form.step3.emergency_contact_phone"
                    type="tel"
                    :error="errors.emergency_contact_phone"
                    placeholder="+33 6 12 34 56 78"
                    required
                  />
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                  Relation *
                </label>
                <Input
                  v-model="form.step3.emergency_contact_relation"
                  :error="errors.emergency_contact_relation"
                  placeholder="Conjoint, parent, ami..."
                  required
                />
              </div>
            </div>

            <!-- Consentements finaux -->
            <div class="space-y-4 pt-6 border-t border-gray-200 dark:border-gray-700">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">Consentements</h3>
              
              <div class="flex items-start">
                <input
                  id="background-check"
                  v-model="form.step3.background_check_consent"
                  type="checkbox"
                  class="mt-1 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                  required
                />
                <label for="background-check" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                  J'autorise la vérification de mes antécédents dans le cadre de mon inscription *
                </label>
              </div>

              <div class="flex items-start">
                <input
                  id="data-processing"
                  v-model="form.step3.data_processing_consent"
                  type="checkbox"
                  class="mt-1 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                  required
                />
                <label for="data-processing" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                  J'accepte le traitement de mes données personnelles conformément au RGPD *
                </label>
              </div>

              <div class="flex items-start">
                <input
                  id="marketing"
                  v-model="form.step3.marketing_consent"
                  type="checkbox"
                  class="mt-1 h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                />
                <label for="marketing" class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                  J'accepte de recevoir des communications marketing (optionnel)
                </label>
              </div>
            </div>

            <div class="flex justify-between pt-6">
              <Button variant="outline" @click="currentStep = 2">
                <Icon name="chevron-left" class="w-4 h-4 mr-2" />
                Retour
              </Button>
              <Button type="submit" :loading="isLoading" class="px-8">
                <Icon name="check" class="w-4 h-4 mr-2" />
                Finaliser l'inscription
              </Button>
            </div>
          </form>
        </div>

        <!-- Success State -->
        <div v-if="currentStep === 4" class="text-center py-12">
          <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
            <Icon name="check" class="h-8 w-8 text-green-600" />
          </div>
          <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Inscription soumise avec succès !
          </h2>
          <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
            Votre demande d'inscription a été transmise à notre équipe. 
            Vous recevrez un email de confirmation une fois votre compte validé.
          </p>
          <Link href="/" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Retour à l'accueil
          </Link>
        </div>
      </Card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Card from '@/components/ui/Card.vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import Button from '@/components/ui/Button.vue'
import Icon from '@/components/ui/Icon.vue'
import FileUpload from '@/components/ui/FileUpload.vue'
import LanguageSelector from '@/components/forms/LanguageSelector.vue'

interface Props {
  categories: Array<{
    id: number
    name: string
    description: string
    icon: string
  }>
}

const props = defineProps<Props>()

const currentStep = ref(1)
const isLoading = ref(false)
const errors = ref({})

const steps = [
  {
    number: 1,
    title: 'Informations personnelles',
    description: 'Vos données de base'
  },
  {
    number: 2,
    title: 'Profil professionnel',
    description: 'Votre expertise'
  },
  {
    number: 3,
    title: 'Documents et validation',
    description: 'Finalisation'
  }
]

const form = reactive({
  step1: {
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    date_of_birth: '',
    gender: '',
    terms_accepted: false,
    privacy_accepted: false
  },
  step2: {
    company_name: '',
    bio: '',
    experience: 0,
    address: '',
    city: '',
    postal_code: '',
    country: 'France',
    languages: ['français'],
    specialties: [],
    intervention_radius: 10,
    accepts_urgent_requests: false
  },
  step3: {
    identity_document: null,
    professional_license: null,
    insurance_certificate: null,
    bank_account_holder: '',
    iban: '',
    bic: '',
    emergency_contact_name: '',
    emergency_contact_phone: '',
    emergency_contact_relation: '',
    background_check_consent: false,
    data_processing_consent: false,
    marketing_consent: false
  }
})

const submitStep1 = async () => {
  isLoading.value = true
  errors.value = {}

  try {
    await router.post('/provider/registration/step1', form.step1, {
      preserveState: true,
      onSuccess: () => {
        currentStep.value = 2
      },
      onError: (formErrors) => {
        errors.value = formErrors
      }
    })
  } finally {
    isLoading.value = false
  }
}

const submitStep2 = async () => {
  isLoading.value = true
  errors.value = {}

  try {
    await router.post('/provider/registration/step2', form.step2, {
      preserveState: true,
      onSuccess: () => {
        currentStep.value = 3
      },
      onError: (formErrors) => {
        errors.value = formErrors
      }
    })
  } finally {
    isLoading.value = false
  }
}

const submitStep3 = async () => {
  isLoading.value = true
  errors.value = {}

  // Créer FormData pour l'upload de fichiers
  const formData = new FormData()
  
  Object.entries(form.step3).forEach(([key, value]) => {
    if (value instanceof File) {
      formData.append(key, value)
    } else if (typeof value === 'boolean') {
      formData.append(key, value ? '1' : '0')
    } else if (value !== null && value !== undefined) {
      formData.append(key, value.toString())
    }
  })

  try {
    await router.post('/provider/registration/step3', formData, {
      preserveState: true,
      onSuccess: () => {
        currentStep.value = 4
      },
      onError: (formErrors) => {
        errors.value = formErrors
      }
    })
  } finally {
    isLoading.value = false
  }
}
</script>