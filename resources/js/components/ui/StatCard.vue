<template>
  <div class="bg-white dark:bg-gray-800 p-6 rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
          {{ title }}
        </p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">
          {{ formatValue(value) }}
        </p>
        <p v-if="change" :class="[
          'text-sm font-medium',
          change > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'
        ]">
          {{ change > 0 ? '+' : '' }}{{ change }}%
          <span class="text-gray-500 dark:text-gray-400">vs dernier mois</span>
        </p>
      </div>
      <div :class="[
        'p-3 rounded-full',
        colorClasses[color]
      ]">
        <Icon :name="icon" class="w-6 h-6" />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Icon from './Icon.vue'

interface Props {
  title: string
  value: number | string
  icon: string
  color?: 'blue' | 'green' | 'yellow' | 'red' | 'purple' | 'gray'
  change?: number
}

const props = withDefaults(defineProps<Props>(), {
  color: 'blue'
})

const colorClasses = {
  blue: 'bg-blue-100 dark:bg-blue-800 text-blue-600 dark:text-blue-200',
  green: 'bg-green-100 dark:bg-green-800 text-green-600 dark:text-green-200',
  yellow: 'bg-yellow-100 dark:bg-yellow-800 text-yellow-600 dark:text-yellow-200',
  red: 'bg-red-100 dark:bg-red-800 text-red-600 dark:text-red-200',
  purple: 'bg-purple-100 dark:bg-purple-800 text-purple-600 dark:text-purple-200',
  gray: 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-200'
}

const formatValue = (value: number | string) => {
  if (typeof value === 'number') {
    if (value >= 1000000) {
      return (value / 1000000).toFixed(1) + 'M'
    } else if (value >= 1000) {
      return (value / 1000).toFixed(1) + 'K'
    }
    return value.toLocaleString()
  }
  return value
}
</script>