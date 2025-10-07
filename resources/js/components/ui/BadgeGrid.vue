<template>
  <div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div v-if="showStats" class="bg-white rounded-lg border border-gray-200 p-6">
      <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques des badges</h3>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-primary">{{ stats.total }}</div>
          <div class="text-sm text-gray-600">Total</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-green-600">{{ stats.recent_count }}</div>
          <div class="text-sm text-gray-600">Récents</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-yellow-600">{{ stats.total_points }}</div>
          <div class="text-sm text-gray-600">Points</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-purple-600">{{ stats.featured_count }}</div>
          <div class="text-sm text-gray-600">Mis en avant</div>
        </div>
      </div>
    </div>

    <!-- Filtres -->
    <div v-if="showFilters" class="bg-white rounded-lg border border-gray-200 p-4">
      <div class="flex flex-wrap gap-4 items-center">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
          <select
            v-model="filters.type"
            class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-primary focus:border-primary"
          >
            <option value="">Tous les types</option>
            <option value="achievement">Accomplissement</option>
            <option value="certification">Certification</option>
            <option value="milestone">Étape importante</option>
            <option value="quality">Qualité</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Rareté</label>
          <select
            v-model="filters.rarity"
            class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-primary focus:border-primary"
          >
            <option value="">Toutes</option>
            <option value="common">Commun</option>
            <option value="rare">Rare</option>
            <option value="epic">Épique</option>
            <option value="legendary">Légendaire</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Période</label>
          <select
            v-model="filters.period"
            class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:ring-primary focus:border-primary"
          >
            <option value="">Tous</option>
            <option value="recent">Récents (7j)</option>
            <option value="month">Ce mois</option>
            <option value="year">Cette année</option>
          </select>
        </div>
        
        <button
          @click="resetFilters"
          class="mt-6 px-3 py-1 text-sm text-gray-600 hover:text-gray-800 transition-colors"
        >
          Réinitialiser
        </button>
      </div>
    </div>

    <!-- Badges mis en avant -->
    <div v-if="featuredBadges.length > 0" class="space-y-4">
      <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
        <span>🌟</span>
        Badges mis en avant
      </h3>
      <div class="flex flex-wrap gap-3">
        <BadgeCard
          v-for="userBadge in featuredBadges"
          :key="userBadge.id"
          :badge="userBadge.badge"
          :earned_at="userBadge.earned_at"
          :show-points="showPoints"
          size="md"
        />
      </div>
    </div>

    <!-- Tous les badges -->
    <div class="space-y-4">
      <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">
          {{ featuredBadges.length > 0 ? 'Autres badges' : 'Badges obtenus' }}
          <span class="text-sm font-normal text-gray-500">({{ filteredBadges.length }})</span>
        </h3>
        
        <div v-if="showSort" class="flex items-center gap-2">
          <span class="text-sm text-gray-600">Trier par :</span>
          <select
            v-model="sortBy"
            class="border border-gray-300 rounded-md px-2 py-1 text-sm focus:ring-primary focus:border-primary"
          >
            <option value="recent">Plus récents</option>
            <option value="rarity">Rareté</option>
            <option value="points">Points</option>
            <option value="name">Nom</option>
          </select>
        </div>
      </div>

      <!-- Grille de badges -->
      <div v-if="filteredBadges.length > 0" :class="gridClasses">
        <div
          v-for="userBadge in sortedBadges"
          :key="userBadge.id"
          class="group cursor-pointer"
          @click="showBadgeDetails(userBadge)"
        >
          <div class="space-y-3 p-4 rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200">
            <!-- Badge principal -->
            <BadgeCard
              :badge="userBadge.badge"
              :earned_at="userBadge.earned_at"
              :show-points="showPoints"
              :size="cardSize"
            />
            
            <!-- Informations supplémentaires -->
            <div class="space-y-2">
              <p class="text-sm text-gray-600 line-clamp-2">{{ userBadge.badge.description }}</p>
              
              <div class="flex items-center justify-between text-xs text-gray-500">
                <span>{{ formatDate(userBadge.earned_at) }}</span>
                <span class="capitalize">{{ userBadge.badge.rarity_label }}</span>
              </div>
              
              <!-- Contexte d'attribution -->
              <div v-if="userBadge.reason && showContext" class="text-xs text-gray-500 italic">
                {{ userBadge.reason }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- État vide -->
      <div v-else class="text-center py-12">
        <div class="text-6xl mb-4">🏆</div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          {{ hasFilters ? 'Aucun badge trouvé' : 'Pas encore de badges' }}
        </h3>
        <p class="text-gray-600 max-w-md mx-auto">
          {{ hasFilters 
            ? 'Essayez de modifier vos filtres pour voir plus de badges.' 
            : 'Les badges seront attribués automatiquement selon vos performances et activités.' 
          }}
        </p>
        <button
          v-if="hasFilters"
          @click="resetFilters"
          class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors"
        >
          Réinitialiser les filtres
        </button>
      </div>
    </div>
  </div>

  <!-- Modal de détails du badge -->
  <div v-if="selectedBadge" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
      <div class="text-center space-y-4">
        <!-- Badge -->
        <div class="flex justify-center">
          <BadgeCard
            :badge="selectedBadge.badge"
            :earned_at="selectedBadge.earned_at"
            :show-points="true"
            size="lg"
          />
        </div>
        
        <!-- Informations -->
        <div class="space-y-3">
          <h3 class="text-xl font-bold text-gray-900">{{ selectedBadge.badge.name }}</h3>
          <p class="text-gray-600">{{ selectedBadge.badge.description }}</p>
          
          <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
              <span class="font-medium text-gray-900">Type :</span>
              <span class="text-gray-600 ml-1">{{ selectedBadge.badge.type_label }}</span>
            </div>
            <div>
              <span class="font-medium text-gray-900">Rareté :</span>
              <span class="text-gray-600 ml-1">{{ selectedBadge.badge.rarity_label }}</span>
            </div>
            <div>
              <span class="font-medium text-gray-900">Points :</span>
              <span class="text-gray-600 ml-1">{{ selectedBadge.badge.points }}</span>
            </div>
            <div>
              <span class="font-medium text-gray-900">Obtenu le :</span>
              <span class="text-gray-600 ml-1">{{ formatDate(selectedBadge.earned_at) }}</span>
            </div>
          </div>
          
          <div v-if="selectedBadge.reason" class="p-3 bg-gray-50 rounded-lg">
            <p class="text-sm text-gray-600 italic">{{ selectedBadge.reason }}</p>
          </div>
        </div>
      </div>
      
      <button
        @click="selectedBadge = null"
        class="mt-6 w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors"
      >
        Fermer
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import BadgeCard from './BadgeCard.vue'

const props = defineProps({
  badges: {
    type: Array,
    default: () => [],
  },
  stats: {
    type: Object,
    default: () => ({}),
  },
  showStats: {
    type: Boolean,
    default: true,
  },
  showFilters: {
    type: Boolean,
    default: true,
  },
  showSort: {
    type: Boolean,
    default: true,
  },
  showPoints: {
    type: Boolean,
    default: true,
  },
  showContext: {
    type: Boolean,
    default: false,
  },
  cardSize: {
    type: String,
    default: 'sm',
  },
  columns: {
    type: Number,
    default: 3,
  },
})

// État local
const filters = ref({
  type: '',
  rarity: '',
  period: '',
})

const sortBy = ref('recent')
const selectedBadge = ref(null)

// Computed
const gridClasses = computed(() => {
  const colsClass = {
    1: 'grid-cols-1',
    2: 'grid-cols-1 sm:grid-cols-2',
    3: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    4: 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
  }
  
  return `grid ${colsClass[props.columns] || colsClass[3]} gap-4`
})

const featuredBadges = computed(() => {
  return props.badges.filter(badge => badge.is_featured)
})

const hasFilters = computed(() => {
  return filters.value.type || filters.value.rarity || filters.value.period
})

const filteredBadges = computed(() => {
  let result = props.badges.filter(badge => !badge.is_featured)
  
  if (filters.value.type) {
    result = result.filter(badge => badge.badge.type === filters.value.type)
  }
  
  if (filters.value.rarity) {
    result = result.filter(badge => badge.badge.rarity === filters.value.rarity)
  }
  
  if (filters.value.period) {
    const now = new Date()
    result = result.filter(badge => {
      const earnedDate = new Date(badge.earned_at)
      
      switch (filters.value.period) {
        case 'recent':
          const weekAgo = new Date()
          weekAgo.setDate(weekAgo.getDate() - 7)
          return earnedDate > weekAgo
        case 'month':
          return earnedDate.getMonth() === now.getMonth() && 
                 earnedDate.getFullYear() === now.getFullYear()
        case 'year':
          return earnedDate.getFullYear() === now.getFullYear()
        default:
          return true
      }
    })
  }
  
  return result
})

const sortedBadges = computed(() => {
  const badges = [...filteredBadges.value]
  
  switch (sortBy.value) {
    case 'recent':
      return badges.sort((a, b) => new Date(b.earned_at) - new Date(a.earned_at))
    case 'rarity':
      const rarityOrder = { legendary: 4, epic: 3, rare: 2, common: 1 }
      return badges.sort((a, b) => rarityOrder[b.badge.rarity] - rarityOrder[a.badge.rarity])
    case 'points':
      return badges.sort((a, b) => b.badge.points - a.badge.points)
    case 'name':
      return badges.sort((a, b) => a.badge.name.localeCompare(b.badge.name))
    default:
      return badges
  }
})

// Méthodes
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const resetFilters = () => {
  filters.value = {
    type: '',
    rarity: '',
    period: '',
  }
}

const showBadgeDetails = (userBadge) => {
  selectedBadge.value = userBadge
}

// Watchers
watch(filters, () => {
  // Réinitialiser le tri quand les filtres changent
  if (hasFilters.value) {
    sortBy.value = 'recent'
  }
}, { deep: true })
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>