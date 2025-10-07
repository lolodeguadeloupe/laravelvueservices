<template>
  <div :class="[
    'bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 rounded-lg',
    padding && 'p-6'
  ]">
    <slot />
  </div>
</template>

<script setup lang="ts">
interface Props {
  padding?: boolean
}

withDefaults(defineProps<Props>(), {
  padding: false
})
</script>