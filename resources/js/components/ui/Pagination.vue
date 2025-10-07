<template>
  <nav v-if="links.length > 3" class="flex items-center justify-between">
    <div class="flex-1 flex justify-between sm:hidden">
      <!-- Mobile pagination -->
      <Link
        v-if="links[0].url"
        :href="links[0].url"
        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
      >
        Précédent
      </Link>
      <span v-else class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
        Précédent
      </span>
      
      <Link
        v-if="links[links.length - 1].url"
        :href="links[links.length - 1].url"
        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700"
      >
        Suivant
      </Link>
      <span v-else class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 cursor-not-allowed">
        Suivant
      </span>
    </div>
    
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
      <!-- Desktop pagination info -->
      <div>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Affichage de
          <span class="font-medium">{{ getCurrentPageStart() }}</span>
          à
          <span class="font-medium">{{ getCurrentPageEnd() }}</span>
          sur
          <span class="font-medium">{{ getTotalItems() }}</span>
          résultats
        </p>
      </div>
      
      <!-- Desktop pagination links -->
      <div>
        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <!-- Previous button -->
          <Link
            v-if="links[0].url"
            :href="links[0].url"
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <span class="sr-only">Précédent</span>
            <Icon name="chevron-left" class="h-5 w-5" />
          </Link>
          <span v-else class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-400 dark:text-gray-500 cursor-not-allowed">
            <span class="sr-only">Précédent</span>
            <Icon name="chevron-left" class="h-5 w-5" />
          </span>
          
          <!-- Page numbers -->
          <template v-for="(link, index) in links.slice(1, -1)" :key="index">
            <Link
              v-if="link.url"
              :href="link.url"
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                link.active
                  ? 'z-10 bg-green-50 dark:bg-green-900 border-green-500 text-green-600 dark:text-green-400'
                  : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700'
              ]"
              v-html="link.label"
            />
            <span
              v-else
              :class="[
                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                link.active
                  ? 'z-10 bg-green-50 dark:bg-green-900 border-green-500 text-green-600 dark:text-green-400'
                  : 'bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-400'
              ]"
              v-html="link.label"
            />
          </template>
          
          <!-- Next button -->
          <Link
            v-if="links[links.length - 1].url"
            :href="links[links.length - 1].url"
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            <span class="sr-only">Suivant</span>
            <Icon name="chevron-right" class="h-5 w-5" />
          </Link>
          <span v-else class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-400 dark:text-gray-500 cursor-not-allowed">
            <span class="sr-only">Suivant</span>
            <Icon name="chevron-right" class="h-5 w-5" />
          </span>
        </nav>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import Icon from './Icon.vue'

interface PaginationLink {
  url: string | null
  label: string
  active: boolean
}

interface Props {
  links: PaginationLink[]
  meta?: {
    current_page: number
    from: number
    to: number
    total: number
    per_page: number
  }
}

const props = defineProps<Props>()

const getCurrentPageStart = () => {
  if (props.meta) {
    return props.meta.from || 0
  }
  
  // Fallback: try to extract from links
  const activePage = props.links.find(link => link.active)
  if (activePage) {
    const pageNum = parseInt(activePage.label)
    if (!isNaN(pageNum)) {
      return ((pageNum - 1) * 20) + 1 // Assuming 20 items per page
    }
  }
  
  return 1
}

const getCurrentPageEnd = () => {
  if (props.meta) {
    return props.meta.to || 0
  }
  
  // Fallback calculation
  const start = getCurrentPageStart()
  return Math.min(start + 19, getTotalItems()) // Assuming 20 items per page
}

const getTotalItems = () => {
  if (props.meta) {
    return props.meta.total || 0
  }
  
  // Try to extract from URL parameters or make an educated guess
  return getCurrentPageEnd()
}
</script>