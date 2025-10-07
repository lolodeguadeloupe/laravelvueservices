<template>
  <Modal v-model="isOpen" :title="title" size="sm">
    <div class="space-y-4">
      <div v-if="danger" class="flex items-start">
        <Icon name="exclamation-triangle" class="w-6 h-6 text-red-600 mr-3 flex-shrink-0 mt-0.5" />
        <div class="text-sm text-gray-700 dark:text-gray-300">
          {{ message }}
        </div>
      </div>
      <div v-else class="text-sm text-gray-700 dark:text-gray-300">
        {{ message }}
      </div>

      <div class="flex justify-end gap-3 pt-4">
        <button
          type="button"
          @click="closeModal"
          class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
        >
          Annuler
        </button>
        <button
          type="button"
          @click="confirm"
          :disabled="loading"
          :class="[
            'px-4 py-2 rounded-lg font-medium disabled:opacity-50',
            danger
              ? 'bg-red-600 text-white hover:bg-red-700'
              : 'bg-green-600 text-white hover:bg-green-700'
          ]"
        >
          <Icon v-if="loading" name="spinner" class="w-4 h-4 mr-2 animate-spin" />
          {{ confirmText }}
        </button>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import Modal from './Modal.vue'
import Icon from './Icon.vue'

interface Props {
  modelValue: boolean
  title: string
  message: string
  confirmText?: string
  danger?: boolean
  loading?: boolean
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'confirm'): void
}

const props = withDefaults(defineProps<Props>(), {
  confirmText: 'Confirmer',
  danger: false,
  loading: false
})

const emit = defineEmits<Emits>()

const isOpen = ref(props.modelValue)

watch(() => props.modelValue, (value) => {
  isOpen.value = value
})

watch(isOpen, (value) => {
  emit('update:modelValue', value)
})

const closeModal = () => {
  isOpen.value = false
}

const confirm = () => {
  emit('confirm')
}
</script>