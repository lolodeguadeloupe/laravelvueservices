<template>
  <span :class="[
    'inline-flex items-center font-medium rounded-full',
    sizeClasses[size],
    statusClasses[status] || statusClasses.default
  ]">
    <Icon
      v-if="showIcon"
      :name="getStatusIcon(status)"
      :class="[
        'mr-1.5',
        size === 'sm' ? 'w-3 h-3' : size === 'lg' ? 'w-5 h-5' : 'w-4 h-4'
      ]"
    />
    {{ getStatusLabel(status) }}
  </span>
</template>

<script setup lang="ts">
import Icon from './Icon.vue'

interface Props {
  status: string
  size?: 'sm' | 'md' | 'lg'
  showIcon?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  size: 'md',
  showIcon: true
})

const sizeClasses = {
  sm: 'px-2 py-1 text-xs',
  md: 'px-2.5 py-0.5 text-sm',
  lg: 'px-3 py-1 text-base'
}

const statusClasses = {
  // KYC Status
  pending: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
  under_review: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
  approved: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
  rejected: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
  
  // General Status
  active: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
  inactive: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
  suspended: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
  
  // Booking Status
  confirmed: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
  cancelled: 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200',
  completed: 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200',
  
  // Service Status
  published: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
  draft: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
  archived: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
  
  // User verification
  verified: 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200',
  unverified: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200',
  
  // Moderation status
  flagged: 'bg-orange-100 text-orange-800 dark:bg-orange-800 dark:text-orange-200',
  moderated: 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200',
  
  // Default
  default: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200'
}

const getStatusLabel = (status: string) => {
  const labels: Record<string, string> = {
    // KYC
    pending: 'En attente',
    under_review: 'En révision',
    approved: 'Approuvé',
    rejected: 'Rejeté',
    
    // General
    active: 'Actif',
    inactive: 'Inactif',
    suspended: 'Suspendu',
    
    // Booking
    confirmed: 'Confirmé',
    cancelled: 'Annulé',
    completed: 'Terminé',
    
    // Service
    published: 'Publié',
    draft: 'Brouillon',
    archived: 'Archivé',
    
    // Verification
    verified: 'Vérifié',
    unverified: 'Non vérifié',
    
    // Moderation
    flagged: 'Signalé',
    moderated: 'Modéré'
  }
  
  return labels[status] || status
}

const getStatusIcon = (status: string) => {
  const icons: Record<string, string> = {
    // KYC
    pending: 'clock',
    under_review: 'eye',
    approved: 'check-circle',
    rejected: 'x-circle',
    
    // General
    active: 'check-circle',
    inactive: 'circle',
    suspended: 'ban',
    
    // Booking
    confirmed: 'check',
    cancelled: 'x',
    completed: 'check-circle-2',
    
    // Service
    published: 'eye',
    draft: 'edit',
    archived: 'archive',
    
    // Verification
    verified: 'shield-check',
    unverified: 'shield-alert',
    
    // Moderation
    flagged: 'flag',
    moderated: 'shield'
  }
  
  return icons[status] || 'circle'
}
</script>