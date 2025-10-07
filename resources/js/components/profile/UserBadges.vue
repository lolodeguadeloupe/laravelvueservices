<template>
  <div class="bg-white rounded-lg border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-lg font-semibold text-gray-900">
        Badges et certifications
      </h3>
      
      <div v-if="showStats" class="flex items-center gap-4 text-sm text-gray-600">
        <span class="flex items-center gap-1">
          <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
          </svg>
          {{ totalPoints }} points
        </span>
        
        <span>{{ badges.length }} badge{{ badges.length > 1 ? 's' : '' }}</span>
      </div>
    </div>

    <!-- Badges mis en avant -->
    <div v-if="featuredBadges.length > 0" class="mb-6">
      <div class="flex items-center gap-2 mb-3">
        <span class="text-lg">⭐</span>
        <h4 class="font-medium text-gray-900">Badges mis en avant</h4>
      </div>
      
      <div class="flex flex-wrap gap-3">
        <BadgeCard
          v-for="userBadge in featuredBadges"
          :key="userBadge.id"
          :badge="userBadge.badge"
          :earned_at="userBadge.earned_at"
          :show-points="showPoints"
          size="md"
          @click="showBadgeDetails(userBadge)"
          class="cursor-pointer hover:shadow-md transition-shadow"
        />
      </div>
    </div>

    <!-- Badges récents -->
    <div v-if="recentBadges.length > 0 && showRecent" class="mb-6">
      <div class="flex items-center gap-2 mb-3">
        <span class="text-lg">🆕</span>
        <h4 class="font-medium text-gray-900">Récemment obtenus</h4>
      </div>
      
      <div class="flex flex-wrap gap-3">
        <BadgeCard
          v-for="userBadge in recentBadges"
          :key="userBadge.id"
          :badge="userBadge.badge"
          :earned_at="userBadge.earned_at"
          :show-points="showPoints"
          size="sm"
          :show-new="true"
          @click="showBadgeDetails(userBadge)"
          class="cursor-pointer hover:shadow-md transition-shadow"
        />
      </div>
    </div>

    <!-- Tous les badges ou grille selon la vue -->
    <div v-if="viewMode === 'grid'">
      <div class="flex items-center justify-between mb-4">
        <h4 class="font-medium text-gray-900">
          {{ featuredBadges.length > 0 ? 'Autres badges' : 'Tous les badges' }}
        </h4>
        
        <button
          v-if="badges.length > maxVisible"
          @click="showAll = !showAll"
          class="text-primary hover:text-primary-dark text-sm font-medium transition-colors"
        >
          {{ showAll ? 'Voir moins' : `Voir tous (${badges.length})` }}
        </button>
      </div>

      <BadgeGrid
        v-if="badges.length > 0"
        :badges="visibleBadges"
        :show-stats="false"
        :show-filters="showAll"
        :show-sort="showAll"
        :show-points="showPoints"
        :columns="showAll ? 4 : 3"
        :card-size="showAll ? 'sm' : 'md'"
      />
    </div>

    <!-- Vue compacte (liste) -->
    <div v-else-if="viewMode === 'compact'" class="space-y-3">
      <div class="flex items-center justify-between mb-4">
        <h4 class="font-medium text-gray-900">Badges obtenus</h4>
        
        <div class="flex items-center gap-2">
          <select
            v-model="sortBy"
            class="text-sm border border-gray-300 rounded px-2 py-1 focus:ring-primary focus:border-primary"
          >
            <option value="recent">Plus récents</option>
            <option value="rarity">Rareté</option>
            <option value="points">Points</option>
            <option value="name">Nom</option>
          </select>
        </div>
      </div>

      <div class="space-y-2">
        <div
          v-for="userBadge in sortedBadges"
          :key="userBadge.id"
          @click="showBadgeDetails(userBadge)"
          class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-gray-300 hover:bg-gray-50 cursor-pointer transition-colors"
        >
          <span class="text-2xl">{{ userBadge.badge.icon || '🏆' }}</span>
          
          <div class="flex-1">
            <div class="flex items-center gap-2">
              <h5 class="font-medium text-gray-900">{{ userBadge.badge.name }}</h5>
              <span class="px-2 py-1 text-xs font-medium rounded-full" :class="getRarityClasses(userBadge.badge.rarity)">
                {{ getRarityLabel(userBadge.badge.rarity) }}
              </span>
            </div>
            <p class="text-sm text-gray-600 line-clamp-1">{{ userBadge.badge.description }}</p>
          </div>
          
          <div class="text-right">
            <div class="text-sm font-medium text-gray-900">+{{ userBadge.badge.points }}</div>
            <div class="text-xs text-gray-500">{{ formatDate(userBadge.earned_at) }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- État vide -->
    <div v-if="badges.length === 0" class="text-center py-8">
      <div class="text-4xl mb-3">🏆</div>
      <h4 class="text-lg font-medium text-gray-900 mb-2">Aucun badge pour le moment</h4>
      <p class="text-gray-600 max-w-sm mx-auto">
        Les badges seront attribués automatiquement selon vos performances et activités sur la plateforme.
      </p>
    </div>

    <!-- Modal de détails -->
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
                <span class="text-gray-600 ml-1">{{ getTypeLabel(selectedBadge.badge.type) }}</span>
              </div>
              <div>
                <span class="font-medium text-gray-900">Rareté :</span>
                <span class="text-gray-600 ml-1">{{ getRarityLabel(selectedBadge.badge.rarity) }}</span>
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
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import BadgeCard from '../ui/BadgeCard.vue'
import BadgeGrid from '../ui/BadgeGrid.vue'

const props = defineProps({
  badges: {
    type: Array,
    default: () => []
  },
  viewMode: {
    type: String,
    default: 'grid', // 'grid', 'compact'
    validator: (value) => ['grid', 'compact'].includes(value)
  },
  showStats: {
    type: Boolean,
    default: true
  },
  showPoints: {
    type: Boolean,
    default: true
  },
  showRecent: {
    type: Boolean,
    default: true
  },
  maxVisible: {
    type: Number,
    default: 6
  }
})

// State
const showAll = ref(false)
const selectedBadge = ref(null)
const sortBy = ref('recent')

// Computed
const featuredBadges = computed(() => {
  return props.badges.filter(badge => badge.is_featured)
})

const recentBadges = computed(() => {
  const weekAgo = new Date()
  weekAgo.setDate(weekAgo.getDate() - 7)
  
  return props.badges
    .filter(badge => new Date(badge.earned_at) > weekAgo && !badge.is_featured)
    .slice(0, 3)
})

const visibleBadges = computed(() => {
  const nonFeatured = props.badges.filter(badge => !badge.is_featured)
  return showAll.value ? nonFeatured : nonFeatured.slice(0, props.maxVisible)
})

const sortedBadges = computed(() => {
  const badges = [...props.badges]
  
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

const totalPoints = computed(() => {
  return props.badges.reduce((total, badge) => total + badge.badge.points, 0)
})

// Methods
const showBadgeDetails = (userBadge) => {
  selectedBadge.value = userBadge
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const getTypeLabel = (type) => {
  const labels = {
    achievement: 'Accomplissement',
    certification: 'Certification',
    milestone: 'Étape importante',
    quality: 'Qualité'
  }
  return labels[type] || type
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
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>