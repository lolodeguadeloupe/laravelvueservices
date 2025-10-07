<template>
  <input
    :type="type"
    :value="modelValue"
    @input="updateValue"
    :placeholder="placeholder"
    :class="[
      'block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white',
      disabled && 'bg-gray-50 dark:bg-gray-800 cursor-not-allowed'
    ]"
    :disabled="disabled"
  />
</template>

<script setup lang="ts">
interface Props {
  modelValue: string | number
  type?: string
  placeholder?: string
  disabled?: boolean
}

interface Emits {
  (e: 'update:modelValue', value: string | number): void
}

withDefaults(defineProps<Props>(), {
  type: 'text',
  disabled: false
})

const emit = defineEmits<Emits>()

const updateValue = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}
</script>