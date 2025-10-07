<template>
  <AdminLayout title="Gestion des badges">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Gestion des badges
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <BadgeManager
          :initial-badges="badges"
          :initial-stats="stats"
          @badge-updated="handleBadgeUpdate"
          @stats-updated="handleStatsUpdate"
        />
      </div>
    </div>
  </AdminLayout>
</template>

<script setup>
import { ref } from 'vue'
import AdminLayout from '@/layouts/AdminLayout.vue'
import BadgeManager from '@/components/admin/BadgeManager.vue'

const props = defineProps({
  badges: {
    type: Array,
    default: () => []
  },
  stats: {
    type: Object,
    default: () => ({})
  }
})

// State
const badges = ref(props.badges)
const stats = ref(props.stats)

// Methods
const handleBadgeUpdate = (updatedBadges) => {
  badges.value = updatedBadges
}

const handleStatsUpdate = (updatedStats) => {
  stats.value = updatedStats
}
</script>