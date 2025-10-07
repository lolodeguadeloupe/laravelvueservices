<template>
  <div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
      {{ label }}
    </label>
    
    <div
      @drop="handleDrop"
      @dragover="handleDragOver"
      @dragleave="handleDragLeave"
      @click="openFileDialog"
      :class="[
        'relative border-2 border-dashed rounded-lg p-6 transition-colors cursor-pointer',
        isDragging
          ? 'border-green-400 bg-green-50 dark:bg-green-900/20'
          : error
          ? 'border-red-300 bg-red-50 dark:bg-red-900/20'
          : 'border-gray-300 dark:border-gray-600 hover:border-green-400 hover:bg-green-50 dark:hover:bg-green-900/20'
      ]"
    >
      <input
        ref="fileInput"
        type="file"
        :accept="accept"
        @change="handleFileSelect"
        class="hidden"
      />
      
      <div v-if="!selectedFile" class="text-center">
        <Icon name="cloud-upload" class="mx-auto h-12 w-12 text-gray-400" />
        <div class="mt-4">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            <span class="font-medium text-green-600 dark:text-green-400">
              Cliquez pour télécharger
            </span>
            ou glissez-déposez votre fichier
          </p>
          <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">
            {{ description }}
          </p>
        </div>
      </div>
      
      <div v-else class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <Icon name="document" class="h-8 w-8 text-green-600" />
          <div>
            <p class="text-sm font-medium text-gray-900 dark:text-white">
              {{ selectedFile.name }}
            </p>
            <p class="text-xs text-gray-500">
              {{ formatFileSize(selectedFile.size) }}
            </p>
          </div>
        </div>
        <button
          @click.stop="removeFile"
          class="text-gray-400 hover:text-red-500 transition-colors"
        >
          <Icon name="trash" class="h-5 w-5" />
        </button>
      </div>
    </div>
    
    <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import Icon from './Icon.vue'

interface Props {
  modelValue: File | null
  label: string
  accept?: string
  description?: string
  error?: string
  required?: boolean
}

interface Emits {
  (e: 'update:modelValue', file: File | null): void
}

const props = withDefaults(defineProps<Props>(), {
  accept: '*/*',
  required: false
})

const emit = defineEmits<Emits>()

const fileInput = ref<HTMLInputElement>()
const selectedFile = ref<File | null>(props.modelValue)
const isDragging = ref(false)

watch(() => props.modelValue, (newValue) => {
  selectedFile.value = newValue
})

watch(selectedFile, (newFile) => {
  emit('update:modelValue', newFile)
})

const openFileDialog = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  if (file) {
    selectedFile.value = file
  }
}

const handleDrop = (event: DragEvent) => {
  event.preventDefault()
  isDragging.value = false
  
  const files = event.dataTransfer?.files
  if (files && files.length > 0) {
    selectedFile.value = files[0]
  }
}

const handleDragOver = (event: DragEvent) => {
  event.preventDefault()
  isDragging.value = true
}

const handleDragLeave = (event: DragEvent) => {
  event.preventDefault()
  isDragging.value = false
}

const removeFile = () => {
  selectedFile.value = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

const formatFileSize = (bytes: number): string => {
  if (bytes === 0) return '0 Bytes'
  
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>