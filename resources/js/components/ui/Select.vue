<template>
  <select
    :value="modelValue"
    @change="updateValue"
    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
  >
    <slot />
  </select>
</template>

<script setup lang="ts">
interface Props {
  modelValue: string | number
}

interface Emits {
  (e: 'update:modelValue', value: string | number): void
  (e: 'change', value: string | number): void
}

defineProps<Props>()
const emit = defineEmits<Emits>()

const updateValue = (event: Event) => {
  const target = event.target as HTMLSelectElement
  emit('update:modelValue', target.value)
  emit('change', target.value)
}
</script>