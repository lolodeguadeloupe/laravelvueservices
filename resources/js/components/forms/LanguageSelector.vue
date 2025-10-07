<template>
  <div class="space-y-3">
    <div class="flex flex-wrap gap-2">
      <span
        v-for="language in selectedLanguages"
        :key="language"
        class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200"
      >
        {{ language }}
        <button
          @click="removeLanguage(language)"
          class="ml-2 hover:text-green-600"
        >
          <Icon name="x" class="w-3 h-3" />
        </button>
      </span>
    </div>
    
    <div class="relative">
      <input
        v-model="searchQuery"
        @input="filterLanguages"
        @focus="showDropdown = true"
        @keydown.escape="showDropdown = false"
        type="text"
        placeholder="Rechercher une langue..."
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
      />
      
      <div
        v-if="showDropdown && filteredLanguages.length > 0"
        class="absolute z-10 w-full mt-1 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-60 overflow-auto"
      >
        <button
          v-for="language in filteredLanguages"
          :key="language"
          @click="addLanguage(language)"
          class="w-full px-3 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-900 dark:text-white"
        >
          {{ language }}
        </button>
      </div>
    </div>
    
    <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import Icon from '@/components/ui/Icon.vue'

interface Props {
  modelValue: string[]
  error?: string
}

interface Emits {
  (e: 'update:modelValue', languages: string[]): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const selectedLanguages = ref<string[]>([...props.modelValue])
const searchQuery = ref('')
const showDropdown = ref(false)

const allLanguages = [
  'Français', 'Anglais', 'Espagnol', 'Italien', 'Allemand', 'Portugais',
  'Néerlandais', 'Russe', 'Chinois', 'Japonais', 'Coréen', 'Arabe',
  'Hindi', 'Bengali', 'Turc', 'Polonais', 'Roumain', 'Grec',
  'Suédois', 'Norvégien', 'Danois', 'Finnois', 'Tchèque', 'Hongrois'
]

const filteredLanguages = computed(() => {
  if (!searchQuery.value) return allLanguages
  
  return allLanguages.filter(language => 
    language.toLowerCase().includes(searchQuery.value.toLowerCase()) &&
    !selectedLanguages.value.includes(language)
  )
})

watch(selectedLanguages, (newLanguages) => {
  emit('update:modelValue', newLanguages)
}, { deep: true })

watch(() => props.modelValue, (newValue) => {
  selectedLanguages.value = [...newValue]
})

const addLanguage = (language: string) => {
  if (!selectedLanguages.value.includes(language)) {
    selectedLanguages.value.push(language)
  }
  searchQuery.value = ''
  showDropdown.value = false
}

const removeLanguage = (language: string) => {
  const index = selectedLanguages.value.indexOf(language)
  if (index > -1) {
    selectedLanguages.value.splice(index, 1)
  }
}

const filterLanguages = () => {
  showDropdown.value = true
}

const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement
  if (!target.closest('.language-selector')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>