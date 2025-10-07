<template>
  <div class="space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
          Validation KYC
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
          Gérer les demandes de validation des prestataires
        </p>
      </div>
      
      <div class="flex space-x-3">
        <Button
          @click="refreshStatistics"
          variant="outline"
          size="sm"
        >
          <Icon name="refresh-cw" class="w-4 h-4 mr-2" />
          Actualiser
        </Button>
        
        <Button
          @click="exportData"
          variant="outline"
          size="sm"
        >
          <Icon name="download" class="w-4 h-4 mr-2" />
          Exporter
        </Button>
      </div>
    </div>

    <!-- Statistiques KYC -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <StatCard
        title="En attente"
        :value="statistics.pending"
        icon="clock"
        color="yellow"
      />
      <StatCard
        title="En révision"
        :value="statistics.under_review"
        icon="eye"
        color="blue"
      />
      <StatCard
        title="Approuvés"
        :value="statistics.approved"
        icon="check-circle"
        color="green"
      />
      <StatCard
        title="Rejetés"
        :value="statistics.rejected"
        icon="x-circle"
        color="red"
      />
    </div>

    <!-- Filtres -->
    <Card>
      <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Statut
            </label>
            <select
              v-model="filters.status"
              @change="applyFilters"
              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-green-500 focus:ring-green-500"
            >
              <option value="">Tous les statuts</option>
              <option value="pending">En attente</option>
              <option value="under_review">En révision</option>
              <option value="approved">Approuvés</option>
              <option value="rejected">Rejetés</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Type d'entreprise
            </label>
            <select
              v-model="filters.business_type"
              @change="applyFilters"
              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-green-500 focus:ring-green-500"
            >
              <option value="">Tous les types</option>
              <option value="individual">Particulier</option>
              <option value="company">Entreprise</option>
              <option value="auto_entrepreneur">Auto-entrepreneur</option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Recherche
            </label>
            <input
              v-model="filters.search"
              @input="applyFilters"
              type="text"
              placeholder="Nom, email, entreprise..."
              class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-green-500 focus:ring-green-500"
            />
          </div>
          
          <div class="flex items-end">
            <Button
              @click="resetFilters"
              variant="outline"
              class="w-full"
            >
              Réinitialiser
            </Button>
          </div>
        </div>
      </div>
    </Card>

    <!-- Liste des demandes -->
    <Card>
      <div class="overflow-x-auto">
        <table class="w-full table-auto">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Prestataire
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Entreprise
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Statut
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Soumis le
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
            <tr
              v-for="request in pendingRequests.data"
              :key="request.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-800"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-10 w-10">
                    <div class="h-10 w-10 rounded-full bg-green-100 dark:bg-green-800 flex items-center justify-center">
                      <span class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ request.name.charAt(0).toUpperCase() }}
                      </span>
                    </div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ request.name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ request.email }}
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-white">
                  {{ request.profile?.business_name || 'Non renseigné' }}
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400">
                  {{ getBusinessTypeLabel(request.profile?.business_type) }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <StatusBadge :status="request.profile?.kyc_status || 'pending'" />
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ formatDate(request.profile?.kyc_submitted_at || request.created_at) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <Button
                    @click="viewDetails(request)"
                    variant="outline"
                    size="sm"
                  >
                    <Icon name="eye" class="w-4 h-4" />
                  </Button>
                  
                  <Button
                    v-if="request.profile?.kyc_status === 'pending'"
                    @click="startReview(request)"
                    variant="primary"
                    size="sm"
                  >
                    Examiner
                  </Button>
                </div>
              </td>
            </tr>
            
            <tr v-if="pendingRequests.data.length === 0">
              <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                Aucune demande KYC trouvée
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div v-if="pendingRequests.links" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        <Pagination :links="pendingRequests.links" />
      </div>
    </Card>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Button from '@/components/ui/Button.vue'
import Card from '@/components/ui/Card.vue'
import Icon from '@/components/ui/Icon.vue'
import StatCard from '@/components/ui/StatCard.vue'
import StatusBadge from '@/components/ui/StatusBadge.vue'
import Pagination from '@/components/ui/Pagination.vue'

interface Props {
  pendingRequests: {
    data: Array<{
      id: number
      name: string
      email: string
      phone: string
      created_at: string
      profile?: {
        business_name?: string
        business_type?: string
        kyc_status: string
        kyc_submitted_at?: string
      }
    }>
    links: any[]
  }
}

const props = defineProps<Props>()

const statistics = ref({
  pending: 0,
  under_review: 0,
  approved: 0,
  rejected: 0
})

const filters = ref({
  status: '',
  business_type: '',
  search: ''
})

onMounted(() => {
  loadStatistics()
})

const loadStatistics = async () => {
  try {
    const response = await fetch('/admin/kyc/statistics')
    statistics.value = await response.json()
  } catch (error) {
    console.error('Erreur lors du chargement des statistiques:', error)
  }
}

const refreshStatistics = () => {
  loadStatistics()
  router.reload()
}

const applyFilters = () => {
  router.get('/admin/kyc', filters.value, {
    preserveState: true,
    preserveScroll: true
  })
}

const resetFilters = () => {
  filters.value = {
    status: '',
    business_type: '',
    search: ''
  }
  applyFilters()
}

const viewDetails = (request: any) => {
  router.visit(`/admin/kyc/${request.id}`)
}

const startReview = async (request: any) => {
  try {
    await router.patch(`/admin/kyc/${request.id}/review`, {}, {
      onSuccess: () => {
        router.visit(`/admin/kyc/${request.id}`)
      }
    })
  } catch (error) {
    console.error('Erreur lors du démarrage de la révision:', error)
  }
}

const exportData = async () => {
  try {
    const response = await fetch('/admin/kyc/export')
    const data = await response.json()
    
    // Créer et télécharger le fichier CSV
    const csv = convertToCSV(data.data)
    const blob = new Blob([csv], { type: 'text/csv' })
    const url = window.URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `kyc-export-${new Date().toISOString().split('T')[0]}.csv`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    window.URL.revokeObjectURL(url)
  } catch (error) {
    console.error('Erreur lors de l\'export:', error)
  }
}

const convertToCSV = (data: any[]) => {
  const headers = Object.keys(data[0] || {})
  const csvContent = [
    headers.join(','),
    ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
  ].join('\n')
  
  return csvContent
}

const getBusinessTypeLabel = (type?: string) => {
  const types: Record<string, string> = {
    individual: 'Particulier',
    company: 'Entreprise',
    auto_entrepreneur: 'Auto-entrepreneur'
  }
  return types[type || ''] || 'Non renseigné'
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