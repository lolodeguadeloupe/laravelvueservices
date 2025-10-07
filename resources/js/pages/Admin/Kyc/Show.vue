<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
      <div>
        <Button
          @click="$inertia.visit('/admin/kyc')"
          variant="outline"
          size="sm"
          class="mb-4"
        >
          <Icon name="arrow-left" class="w-4 h-4 mr-2" />
          Retour à la liste
        </Button>
        
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
          Validation KYC - {{ provider.name }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          Examiner les documents et informations du prestataire
        </p>
      </div>
      
      <div class="flex items-center space-x-3">
        <StatusBadge :status="kycStatus" size="lg" />
        
        <div class="flex space-x-2">
          <Button
            v-if="kycStatus === 'under_review'"
            @click="approveKyc"
            variant="success"
          >
            <Icon name="check" class="w-4 h-4 mr-2" />
            Approuver
          </Button>
          
          <Button
            v-if="kycStatus === 'under_review'"
            @click="showRejectModal = true"
            variant="danger"
          >
            <Icon name="x" class="w-4 h-4 mr-2" />
            Rejeter
          </Button>
          
          <Button
            v-if="kycStatus === 'pending'"
            @click="startReview"
            variant="primary"
          >
            <Icon name="eye" class="w-4 h-4 mr-2" />
            Commencer l'examen
          </Button>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Informations personnelles -->
      <div class="lg:col-span-1">
        <Card>
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Informations personnelles
            </h3>
            
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Nom complet
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ provider.name }}
                </p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Email
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ provider.email }}
                </p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Téléphone
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ provider.phone || 'Non renseigné' }}
                </p>
              </div>
              
              <div v-if="provider.profile?.date_of_birth">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Date de naissance
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ formatDate(provider.profile.date_of_birth) }}
                </p>
              </div>
              
              <div v-if="provider.profile?.gender">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Genre
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ provider.profile.gender }}
                </p>
              </div>
            </div>
          </div>
        </Card>

        <!-- Informations professionnelles -->
        <Card class="mt-6">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Informations professionnelles
            </h3>
            
            <div class="space-y-4">
              <div v-if="provider.profile?.business_name">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Nom de l'entreprise
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ provider.profile.business_name }}
                </p>
              </div>
              
              <div v-if="provider.profile?.business_type">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Type d'entreprise
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ getBusinessTypeLabel(provider.profile.business_type) }}
                </p>
              </div>
              
              <div v-if="provider.profile?.siret_number">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Numéro SIRET
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                  {{ provider.profile.siret_number }}
                </p>
              </div>
              
              <div v-if="provider.profile?.years_experience">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Années d'expérience
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ provider.profile.years_experience }} ans
                </p>
              </div>
              
              <div v-if="provider.profile?.specialties?.length">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Spécialités
                </label>
                <div class="mt-1 flex flex-wrap gap-2">
                  <span
                    v-for="specialty in provider.profile.specialties"
                    :key="specialty"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200"
                  >
                    {{ specialty }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </Card>

        <!-- Informations bancaires -->
        <Card class="mt-6">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Informations bancaires
            </h3>
            
            <div class="space-y-4">
              <div v-if="provider.profile?.iban">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  IBAN
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                  {{ maskIban(provider.profile.iban) }}
                </p>
              </div>
              
              <div v-if="provider.profile?.bic">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  BIC
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                  {{ provider.profile.bic }}
                </p>
              </div>
              
              <div v-if="provider.profile?.bank_account_holder">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                  Titulaire du compte
                </label>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ provider.profile.bank_account_holder }}
                </p>
              </div>
            </div>
          </div>
        </Card>
      </div>

      <!-- Documents KYC -->
      <div class="lg:col-span-2">
        <Card>
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Documents KYC
            </h3>
            
            <div v-if="documents.length === 0" class="text-center py-8">
              <Icon name="file-x" class="w-12 h-12 text-gray-400 mx-auto mb-3" />
              <p class="text-gray-500 dark:text-gray-400">Aucun document fourni</p>
            </div>
            
            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div
                v-for="document in documents"
                :key="document.field"
                class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
              >
                <div class="flex items-center justify-between mb-3">
                  <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ document.label }}
                  </h4>
                  <div class="flex items-center space-x-2">
                    <span
                      :class="[
                        'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
                        document.exists 
                          ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200'
                          : 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200'
                      ]"
                    >
                      <Icon
                        :name="document.exists ? 'check' : 'x'"
                        class="w-3 h-3 mr-1"
                      />
                      {{ document.exists ? 'Disponible' : 'Manquant' }}
                    </span>
                  </div>
                </div>
                
                <div v-if="document.exists" class="space-y-3">
                  <div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
                    <Icon name="file" class="w-4 h-4" />
                    <span>{{ getFileName(document.path) }}</span>
                  </div>
                  
                  <div class="flex space-x-2">
                    <Button
                      @click="viewDocument(document)"
                      variant="outline"
                      size="sm"
                      class="flex-1"
                    >
                      <Icon name="eye" class="w-4 h-4 mr-2" />
                      Voir
                    </Button>
                    
                    <Button
                      @click="downloadDocument(document)"
                      variant="outline"
                      size="sm"
                      class="flex-1"
                    >
                      <Icon name="download" class="w-4 h-4 mr-2" />
                      Télécharger
                    </Button>
                  </div>
                </div>
                
                <div v-else class="text-center py-4">
                  <Icon name="file-x" class="w-8 h-8 text-gray-400 mx-auto mb-2" />
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    Document non fourni
                  </p>
                </div>
              </div>
            </div>
          </div>
        </Card>

        <!-- Historique -->
        <Card class="mt-6">
          <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Historique KYC
            </h3>
            
            <div class="space-y-4">
              <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-100 dark:bg-blue-800 rounded-full flex items-center justify-center">
                    <Icon name="plus" class="w-4 h-4 text-blue-600 dark:text-blue-200" />
                  </div>
                </div>
                <div>
                  <p class="text-sm text-gray-900 dark:text-white">
                    Inscription créée
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDate(provider.created_at) }}
                  </p>
                </div>
              </div>
              
              <div v-if="provider.profile?.kyc_submitted_at" class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-800 rounded-full flex items-center justify-center">
                    <Icon name="upload" class="w-4 h-4 text-yellow-600 dark:text-yellow-200" />
                  </div>
                </div>
                <div>
                  <p class="text-sm text-gray-900 dark:text-white">
                    Documents KYC soumis
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDate(provider.profile.kyc_submitted_at) }}
                  </p>
                </div>
              </div>
              
              <div v-if="provider.profile?.kyc_reviewed_at" class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                  <div :class="[
                    'w-8 h-8 rounded-full flex items-center justify-center',
                    kycStatus === 'approved' 
                      ? 'bg-green-100 dark:bg-green-800'
                      : 'bg-red-100 dark:bg-red-800'
                  ]">
                    <Icon
                      :name="kycStatus === 'approved' ? 'check' : 'x'"
                      :class="[
                        'w-4 h-4',
                        kycStatus === 'approved'
                          ? 'text-green-600 dark:text-green-200'
                          : 'text-red-600 dark:text-red-200'
                      ]"
                    />
                  </div>
                </div>
                <div>
                  <p class="text-sm text-gray-900 dark:text-white">
                    KYC {{ kycStatus === 'approved' ? 'approuvé' : 'rejeté' }}
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ formatDate(provider.profile.kyc_reviewed_at) }}
                  </p>
                  <p v-if="provider.profile?.kyc_rejection_reason" class="text-xs text-red-600 dark:text-red-400 mt-1">
                    {{ provider.profile.kyc_rejection_reason }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </Card>
      </div>
    </div>

    <!-- Modal de rejet -->
    <Modal
      v-model:show="showRejectModal"
      title="Rejeter la demande KYC"
      max-width="lg"
    >
      <form @submit.prevent="rejectKyc">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Raison du rejet *
            </label>
            <textarea
              v-model="rejectForm.reason"
              rows="4"
              required
              placeholder="Expliquez pourquoi cette demande est rejetée..."
              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-red-500 focus:ring-red-500"
            ></textarea>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Documents manquants ou incorrects
            </label>
            <div class="space-y-2">
              <label
                v-for="doc in availableDocuments"
                :key="doc.value"
                class="flex items-center"
              >
                <input
                  v-model="rejectForm.missing_documents"
                  :value="doc.value"
                  type="checkbox"
                  class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                />
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                  {{ doc.label }}
                </span>
              </label>
            </div>
          </div>
        </div>
        
        <div class="flex justify-end space-x-3 mt-6">
          <Button
            @click="showRejectModal = false"
            variant="outline"
            type="button"
          >
            Annuler
          </Button>
          <Button
            variant="danger"
            type="submit"
            :disabled="rejectForm.processing"
          >
            <Icon v-if="rejectForm.processing" name="loader" class="w-4 h-4 mr-2 animate-spin" />
            Rejeter la demande
          </Button>
        </div>
      </form>
    </Modal>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import Button from '@/components/ui/Button.vue'
import Card from '@/components/ui/Card.vue'
import Icon from '@/components/ui/Icon.vue'
import Modal from '@/components/ui/Modal.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'

interface Props {
  provider: {
    id: number
    name: string
    email: string
    phone: string
    created_at: string
    profile?: {
      business_name?: string
      business_type?: string
      siret_number?: string
      date_of_birth?: string
      gender?: string
      years_experience?: number
      specialties?: string[]
      iban?: string
      bic?: string
      bank_account_holder?: string
      kyc_submitted_at?: string
      kyc_reviewed_at?: string
      kyc_rejection_reason?: string
    }
  }
  documents: Array<{
    field: string
    label: string
    path: string
    url: string
    exists: boolean
  }>
  kycStatus: string
}

const props = defineProps<Props>()

const showRejectModal = ref(false)

const rejectForm = reactive({
  reason: '',
  missing_documents: [] as string[],
  processing: false
})

const availableDocuments = [
  { value: 'Pièce d\'identité', label: 'Pièce d\'identité' },
  { value: 'Extrait Kbis/Auto-entrepreneur', label: 'Extrait Kbis/Auto-entrepreneur' },
  { value: 'Assurance professionnelle', label: 'Assurance professionnelle' },
  { value: 'Certifications professionnelles', label: 'Certifications professionnelles' },
  { value: 'RIB', label: 'RIB' }
]

const startReview = () => {
  router.patch(`/admin/kyc/${props.provider.id}/review`, {}, {
    onSuccess: () => {
      router.reload()
    }
  })
}

const approveKyc = () => {
  router.patch(`/admin/kyc/${props.provider.id}/approve`, {}, {
    onSuccess: () => {
      router.reload()
    }
  })
}

const rejectKyc = () => {
  rejectForm.processing = true
  
  router.patch(`/admin/kyc/${props.provider.id}/reject`, {
    reason: rejectForm.reason,
    missing_documents: rejectForm.missing_documents
  }, {
    onSuccess: () => {
      showRejectModal.value = false
      router.reload()
    },
    onFinish: () => {
      rejectForm.processing = false
    }
  })
}

const viewDocument = (document: any) => {
  window.open(document.url, '_blank')
}

const downloadDocument = (document: any) => {
  window.open(`/admin/kyc/${props.provider.id}/documents/${document.field}`, '_blank')
}

const getBusinessTypeLabel = (type: string) => {
  const types: Record<string, string> = {
    individual: 'Particulier',
    company: 'Entreprise',
    auto_entrepreneur: 'Auto-entrepreneur'
  }
  return types[type] || type
}

const maskIban = (iban: string) => {
  if (iban.length <= 8) return iban
  return iban.slice(0, 4) + '*'.repeat(iban.length - 8) + iban.slice(-4)
}

const getFileName = (path: string) => {
  return path.split('/').pop() || 'document'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>