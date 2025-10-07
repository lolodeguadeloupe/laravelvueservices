<template>
  <div 
    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border transition-all duration-200"
    :class="badgeClasses"
    :title="badge.description"
  >
    <!-- Icône du badge -->
    <span class="text-sm" v-if="badge.icon">{{ badge.icon }}</span>
    
    <!-- Nom du badge -->
    <span class="text-xs font-medium">{{ badge.name }}</span>
    
    <!-- Indicateur nouveau (si récent) -->
    <span 
      v-if="isRecent && showNew"
      class="w-2 h-2 bg-red-500 rounded-full animate-pulse"
      title="Badge récemment obtenu"
    ></span>
    
    <!-- Points de réputation -->
    <span 
      v-if="showPoints && badge.points > 0"
      class="text-xs opacity-75"
    >
      +{{ badge.points }}
    </span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  badge: {
    type: Object,
    required: true,
  },
  earned_at: {
    type: [String, Date],
    default: null,
  },
  showNew: {
    type: Boolean,
    default: true,
  },
  showPoints: {
    type: Boolean,
    default: false,
  },
  size: {
    type: String,
    default: 'sm', // sm, md, lg
    validator: (value) => ['sm', 'md', 'lg'].includes(value),
  },
})

// Calculer si le badge est récent (7 derniers jours)
const isRecent = computed(() => {
  if (!props.earned_at) return false
  const earnedDate = new Date(props.earned_at)
  const weekAgo = new Date()
  weekAgo.setDate(weekAgo.getDate() - 7)
  return earnedDate > weekAgo
})

// Classes CSS selon la rareté et le type
const badgeClasses = computed(() => {
  const baseClasses = []
  
  // Taille
  switch (props.size) {
    case 'lg':
      baseClasses.push('px-4 py-2 text-sm')
      break
    case 'md':
      baseClasses.push('px-3 py-1.5 text-sm')
      break
    case 'sm':
    default:
      baseClasses.push('px-2 py-1 text-xs')
      break
  }
  
  // Couleurs selon la rareté
  switch (props.badge.rarity) {
    case 'legendary':
      baseClasses.push(
        'bg-gradient-to-r from-yellow-100 to-orange-100',
        'border-yellow-300',
        'text-yellow-800',
        'shadow-lg shadow-yellow-200/50'
      )
      break
    case 'epic':
      baseClasses.push(
        'bg-gradient-to-r from-purple-100 to-pink-100',
        'border-purple-300',
        'text-purple-800',
        'shadow-lg shadow-purple-200/50'
      )
      break
    case 'rare':
      baseClasses.push(
        'bg-gradient-to-r from-blue-100 to-indigo-100',
        'border-blue-300',
        'text-blue-800',
        'shadow-md shadow-blue-200/50'
      )
      break
    case 'common':
    default:
      baseClasses.push(
        'bg-gray-100',
        'border-gray-300',
        'text-gray-700',
        'shadow-sm'
      )
      break
  }
  
  return baseClasses.join(' ')
})
</script>