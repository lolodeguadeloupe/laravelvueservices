<template>
  <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center justify-between">
      <div class="flex-1">
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ title }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ displayValue }}</p>
        
        <!-- Indicateur de changement -->
        <div v-if="change !== undefined" class="flex items-center mt-2">
          <Icon
            :name="change >= 0 ? 'trending-up' : 'trending-down'"
            :class="[
              'w-4 h-4 mr-1',
              change >= 0 ? 'text-green-600' : 'text-red-600'
            ]"
          />
          <span
            :class="[
              'text-sm font-medium',
              change >= 0 ? 'text-green-600' : 'text-red-600'
            ]"
          >
            {{ Math.abs(change) }}%
          </span>
          <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">
            vs mois dernier
          </span>
        </div>
      </div>
      
      <!-- Icône -->
      <div :class="[
        'w-12 h-12 rounded-lg flex items-center justify-center',
        colorClasses.bg
      ]">
        <Icon :name="icon" :class="['w-6 h-6', colorClasses.icon]" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Icon from '@/components/ui/Icon.vue'

interface Props {
  title: string
  value: string | number
  change?: number
  icon: string
  color?: 'blue' | 'green' | 'purple' | 'emerald' | 'yellow' | 'red'
}

const props = withDefaults(defineProps<Props>(), {
  color: 'blue'
})

const displayValue = computed(() => {
  if (typeof props.value === 'number') {
    return props.value.toLocaleString('fr-FR')
  }
  return props.value
})

const colorClasses = computed(() => {
  const colors = {
    blue: {
      bg: 'bg-blue-100 dark:bg-blue-900',
      icon: 'text-blue-600 dark:text-blue-300'
    },
    green: {
      bg: 'bg-green-100 dark:bg-green-900',
      icon: 'text-green-600 dark:text-green-300'
    },
    purple: {
      bg: 'bg-purple-100 dark:bg-purple-900',
      icon: 'text-purple-600 dark:text-purple-300'
    },
    emerald: {
      bg: 'bg-emerald-100 dark:bg-emerald-900',
      icon: 'text-emerald-600 dark:text-emerald-300'
    },
    yellow: {
      bg: 'bg-yellow-100 dark:bg-yellow-900',
      icon: 'text-yellow-600 dark:text-yellow-300'
    },
    red: {
      bg: 'bg-red-100 dark:bg-red-900',
      icon: 'text-red-600 dark:text-red-300'
    }
  }
  
  return colors[props.color]
})
</script>