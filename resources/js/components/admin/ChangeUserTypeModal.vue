<template>
  <Modal v-model="isOpen" title="Modifier le type d'utilisateur" size="md">
    <form @submit.prevent="updateUserType" class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Type d'utilisateur actuel
        </label>
        <Badge :variant="getUserTypeVariant(user.user_type)">
          {{ getUserTypeLabel(user.user_type) }}
        </Badge>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Nouveau type
        </label>
        <Select v-model="form.user_type" required>
          <option value="">Sélectionner un type</option>
          <option value="client">Client</option>
          <option value="provider">Prestataire</option>
          <option value="admin">Administrateur</option>
        </Select>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
          Raison du changement
        </label>
        <textarea
          v-model="form.reason"
          placeholder="Expliquez pourquoi vous modifiez le type d'utilisateur..."
          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
          rows="3"
        ></textarea>
      </div>

      <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
        <div class="flex">
          <Icon name="exclamation-triangle" class="w-5 h-5 text-yellow-400 mr-2 flex-shrink-0 mt-0.5" />
          <div class="text-sm text-yellow-800 dark:text-yellow-200">
            <p class="font-medium mb-1">Attention</p>
            <p>Le changement de type d'utilisateur peut affecter les permissions et l'accès aux fonctionnalités.</p>
          </div>
        </div>
      </div>

      <div class="flex justify-end gap-3">
        <button
          type="button"
          @click="closeModal"
          class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
        >
          Annuler
        </button>
        <button
          type="submit"
          :disabled="!form.user_type || isLoading"
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
        >
          <Icon v-if="isLoading" name="spinner" class="w-4 h-4 mr-2 animate-spin" />
          Modifier
        </button>
      </div>
    </form>
  </Modal>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/components/ui/Modal.vue'
import Select from '@/components/ui/Select.vue'
import Badge from '@/components/ui/Badge.vue'
import Icon from '@/components/ui/Icon.vue'

interface Props {
  modelValue: boolean
  user: {
    id: number
    user_type: string
  }
}

interface Emits {
  (e: 'update:modelValue', value: boolean): void
  (e: 'updated'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const isOpen = ref(props.modelValue)
const isLoading = ref(false)

const form = reactive({
  user_type: '',
  reason: ''
})

watch(() => props.modelValue, (value) => {
  isOpen.value = value
  if (value) {
    form.user_type = ''
    form.reason = ''
  }
})

watch(isOpen, (value) => {
  emit('update:modelValue', value)
})

const getUserTypeVariant = (type: string) => {
  const variants = {
    admin: 'danger',
    provider: 'success',
    client: 'default'
  }
  return variants[type as keyof typeof variants] || 'default'
}

const getUserTypeLabel = (type: string) => {
  const labels = {
    admin: 'Admin',
    provider: 'Prestataire',
    client: 'Client'
  }
  return labels[type as keyof typeof labels] || type
}

const updateUserType = async () => {
  if (!form.user_type) return

  isLoading.value = true
  try {
    await router.patch(route('admin.users.updateUserType', props.user.id), form)
    emit('updated')
    closeModal()
  } finally {
    isLoading.value = false
  }
}

const closeModal = () => {
  isOpen.value = false
}
</script>