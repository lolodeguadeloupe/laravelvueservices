<template>
  <MenuItem v-slot="{ active }">
    <button
      @click="$emit('click')"
      :class="[
        active ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300',
        'group flex w-full items-center rounded-md px-2 py-2 text-sm transition-colors'
      ]"
    >
      <slot />
    </button>
  </MenuItem>
</template>

<script setup lang="ts">
import { MenuItem } from '@headlessui/vue'

defineEmits<{
  click: []
}>()
</script>