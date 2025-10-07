<template>
  <div class="space-y-6">
    <!-- En-tête avec actions principales -->
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-bold text-gray-900">Gestion des badges</h2>
        <p class="text-gray-600 mt-1">
          Gérez les badges et certifications de la plateforme
        </p>
      </div>
      
      <div class="flex gap-3">
        <button
          @click="checkAllBadges"
          :disabled="isCheckingBadges"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="isCheckingBadges" class="flex items-center gap-2">
            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Vérification...
          </span>
          <span v-else>Vérifier tous les badges</span>
        </button>
        
        <button
          @click="showCreateModal = true"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
        >
          Créer un badge
        </button>
      </div>
    </div>

    <!-- Statistiques des badges -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white p-6 rounded-lg border border-gray-200">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Total badges</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_badges }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-6 rounded-lg border border-gray-200">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Badges attribués</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.awarded_badges }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-6 rounded-lg border border-gray-200">
        <div class="flex items-center">
          <div class="p-2 bg-yellow-100 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Badges actifs</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.active_badges }}</p>
          </div>
        </div>
      </div>
      
      <div class="bg-white p-6 rounded-lg border border-gray-200">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-600">Points distribués</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_points }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white p-4 rounded-lg border border-gray-200">
      <div class="flex flex-wrap gap-4 items-center">
        <div class="flex-1 min-w-64">
          <input
            v-model="filters.search"
            type="text"
            placeholder="Rechercher un badge..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary focus:border-primary"
          />
        </div>
        
        <div>
          <select
            v-model="filters.type"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary"
          >
            <option value="">Tous les types</option>
            <option value="achievement">Accomplissement</option>
            <option value="certification">Certification</option>
            <option value="milestone">Étape importante</option>
            <option value="quality">Qualité</option>
          </select>
        </div>
        
        <div>
          <select
            v-model="filters.rarity"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary"
          >
            <option value="">Toutes les raretés</option>
            <option value="common">Commun</option>
            <option value="rare">Rare</option>
            <option value="epic">Épique</option>
            <option value="legendary">Légendaire</option>
          </select>
        </div>
        
        <div>
          <select
            v-model="filters.status"
            class="border border-gray-300 rounded-md px-3 py-2 focus:ring-primary focus:border-primary"
          >
            <option value="">Tous</option>
            <option value="active">Actifs</option>
            <option value="inactive">Inactifs</option>
          </select>
        </div>
        
        <button
          @click="resetFilters"
          class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors"
        >
          Réinitialiser
        </button>
      </div>
    </div>

    <!-- Liste des badges -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Badge
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Type
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Rareté
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Points
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Attribués
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Statut
              </th>
              <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="badge in filteredBadges" :key="badge.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <span class="text-2xl mr-3">{{ badge.icon || '🏆' }}</span>
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ badge.name }}</div>
                    <div class="text-sm text-gray-500 line-clamp-1">{{ badge.description }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getTypeClasses(badge.type)">
                  {{ getTypeLabel(badge.type) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getRarityClasses(badge.rarity)">
                  {{ getRarityLabel(badge.rarity) }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ badge.points }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ badge.awarded_count || 0 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full" :class="badge.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ badge.is_active ? 'Actif' : 'Inactif' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end gap-2">
                  <button
                    @click="editBadge(badge)"
                    class="text-indigo-600 hover:text-indigo-900 transition-colors"
                  >
                    Modifier
                  </button>
                  <button
                    @click="toggleBadgeStatus(badge)"
                    class="text-yellow-600 hover:text-yellow-900 transition-colors"
                  >
                    {{ badge.is_active ? 'Désactiver' : 'Activer' }}
                  </button>
                  <button
                    @click="deleteBadge(badge)"
                    class="text-red-600 hover:text-red-900 transition-colors"
                  >
                    Supprimer
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal de création/édition -->
    <div v-if="showCreateModal || editingBadge" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">
            {{ editingBadge ? 'Modifier le badge' : 'Créer un nouveau badge' }}
          </h3>
        </div>
        
        <div class="p-6 space-y-4">
          <BadgeForm
            :badge="editingBadge"
            @saved="handleBadgeSaved"
            @cancelled="handleBadgeCancelled"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import BadgeForm from './BadgeForm.vue'

// State
const badges = ref([])
const stats = ref({
  total_badges: 0,
  awarded_badges: 0,
  active_badges: 0,
  total_points: 0
})

const filters = ref({
  search: '',
  type: '',
  rarity: '',
  status: ''
})

const showCreateModal = ref(false)
const editingBadge = ref(null)
const isCheckingBadges = ref(false)

// Computed
const filteredBadges = computed(() => {
  let result = badges.value

  if (filters.value.search) {
    const search = filters.value.search.toLowerCase()
    result = result.filter(badge => 
      badge.name.toLowerCase().includes(search) ||
      badge.description.toLowerCase().includes(search)
    )
  }

  if (filters.value.type) {
    result = result.filter(badge => badge.type === filters.value.type)
  }

  if (filters.value.rarity) {
    result = result.filter(badge => badge.rarity === filters.value.rarity)
  }

  if (filters.value.status) {
    const isActive = filters.value.status === 'active'
    result = result.filter(badge => badge.is_active === isActive)
  }

  return result
})

// Methods
const loadBadges = async () => {
  try {
    // Ici, on chargerait les badges depuis l'API
    // Pour l'instant, on utilise des données de test
    badges.value = [
      // Les badges seraient chargés depuis l'API Laravel
    ]
  } catch (error) {
    console.error('Erreur lors du chargement des badges:', error)
  }
}

const loadStats = async () => {
  try {
    // Charger les statistiques depuis l'API
    stats.value = {
      total_badges: badges.value.length,
      awarded_badges: badges.value.reduce((sum, badge) => sum + (badge.awarded_count || 0), 0),
      active_badges: badges.value.filter(badge => badge.is_active).length,
      total_points: badges.value.reduce((sum, badge) => sum + badge.points, 0)
    }
  } catch (error) {
    console.error('Erreur lors du chargement des statistiques:', error)
  }
}

const checkAllBadges = async () => {
  isCheckingBadges.value = true
  try {
    // Appel à l'API pour vérifier tous les badges
    await new Promise(resolve => setTimeout(resolve, 2000)) // Simulation
    await loadBadges()
    await loadStats()
  } catch (error) {
    console.error('Erreur lors de la vérification des badges:', error)
  } finally {
    isCheckingBadges.value = false
  }
}

const editBadge = (badge) => {
  editingBadge.value = { ...badge }
}

const toggleBadgeStatus = async (badge) => {
  try {
    // Appel API pour changer le statut
    badge.is_active = !badge.is_active
  } catch (error) {
    console.error('Erreur lors de la modification du statut:', error)
  }
}

const deleteBadge = async (badge) => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer le badge "${badge.name}" ?`)) {
    return
  }

  try {
    // Appel API pour supprimer
    badges.value = badges.value.filter(b => b.id !== badge.id)
    await loadStats()
  } catch (error) {
    console.error('Erreur lors de la suppression:', error)
  }
}

const handleBadgeSaved = (badge) => {
  if (editingBadge.value) {
    // Modification
    const index = badges.value.findIndex(b => b.id === badge.id)
    if (index !== -1) {
      badges.value[index] = badge
    }
  } else {
    // Création
    badges.value.push(badge)
  }
  
  showCreateModal.value = false
  editingBadge.value = null
  loadStats()
}

const handleBadgeCancelled = () => {
  showCreateModal.value = false
  editingBadge.value = null
}

const resetFilters = () => {
  filters.value = {
    search: '',
    type: '',
    rarity: '',
    status: ''
  }
}

// Utility functions
const getTypeLabel = (type) => {
  const labels = {
    achievement: 'Accomplissement',
    certification: 'Certification',
    milestone: 'Étape importante',
    quality: 'Qualité'
  }
  return labels[type] || type
}

const getTypeClasses = (type) => {
  const classes = {
    achievement: 'bg-blue-100 text-blue-800',
    certification: 'bg-green-100 text-green-800',
    milestone: 'bg-purple-100 text-purple-800',
    quality: 'bg-yellow-100 text-yellow-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getRarityLabel = (rarity) => {
  const labels = {
    common: 'Commun',
    rare: 'Rare',
    epic: 'Épique',
    legendary: 'Légendaire'
  }
  return labels[rarity] || rarity
}

const getRarityClasses = (rarity) => {
  const classes = {
    common: 'bg-gray-100 text-gray-800',
    rare: 'bg-blue-100 text-blue-800',
    epic: 'bg-purple-100 text-purple-800',
    legendary: 'bg-yellow-100 text-yellow-800'
  }
  return classes[rarity] || 'bg-gray-100 text-gray-800'
}

// Lifecycle
onMounted(() => {
  loadBadges()
  loadStats()
})
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>