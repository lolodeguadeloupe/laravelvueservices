<template>
  <Link
    :href="href"
    :class="[
      'group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors',
      active
        ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200'
        : 'text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700'
    ]"
  >
    <Icon
      :name="icon"
      :class="[
        'mr-3 h-5 w-5 flex-shrink-0',
        active
          ? 'text-green-600 dark:text-green-300'
          : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-200'
      ]"
    />
    {{ label }}
    
    <!-- Badge pour les notifications -->
    <Badge
      v-if="badge && badge > 0"
      :variant="badgeVariant"
      class="ml-auto"
    >
      {{ badge > 99 ? '99+' : badge }}
    </Badge>
  </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Icon from '@/components/ui/Icon.vue'
import Badge from '@/components/ui/Badge.vue'

interface Props {
  href: string
  icon: string
  label: string
  active?: boolean
  badge?: number
  badgeVariant?: 'default' | 'success' | 'warning' | 'danger'
}

withDefaults(defineProps<Props>(), {
  active: false,
  badgeVariant: 'default'
})
</script>