<template>
  <Modal v-model="isOpen" title="Gestion des badges" size="lg">
    <div class="space-y-6">
      <!-- Badges actuels -->
      <div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Badges actuels ({{ user.user_badges?.length || 0 }})
        </h3>
        
        <div v-if="user.user_badges?.length > 0" class="space-y-2 max-h-48 overflow-y-auto">
          <div
            v-for="userBadge in user.user_badges"
            :key="userBadge.id"
            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
          >
            <div class="flex items-center gap-3">
              <span class="text-xl">{{ userBadge.badge.icon }}</span>
              <div>
                <h4 class="font-medium text-gray-900 dark:text-white">{{ userBadge.badge.name }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ userBadge.badge.points }} points</p>
              </div>
            </div>
            <button
              @click="revokeBadge(userBadge.badge.id)"
              :disabled="isLoading"
              class="text-red-600 hover:text-red-800 dark:text-red-400 disabled:opacity-50"
            >
              <Icon name="trash" class="w-4 h-4" />
            </button>
          </div>
        </div>

        <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
          Aucun badge attribué
        </div>
      </div>

      <!-- Attribuer un nouveau badge -->
      <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
          Attribuer un nouveau badge
        </h3>

        <form @submit.prevent="awardBadge" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Badge à attribuer
            </label>
            <Select v-model="form.badge_id" required>
              <option value="">Sélectionner un badge</option>
              <option
                v-for="badge in availableBadges"
                :key="badge.id"
                :value="badge.id"
              >
                {{ badge.icon }} {{ badge.name }} ({{ badge.points }} points)
              </option>
            </Select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Raison (optionnel)
            </label>
            <textarea
              v-model="form.reason"
              placeholder="Raison de l'attribution..."
              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white"
              rows="2"
            ></textarea>
          </div>

          <button
            type="submit"
            :disabled="!form.badge_id || isLoading"
            class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
          >
            <Icon v-if="isLoading" name="spinner" class="w-4 h-4 mr-2 animate-spin" />
            Attribuer le badge
          </button>
        </form>
      </div>

      <!-- Actions -->
      <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
        <div class="flex justify-between">
          <button
            @click="checkAllBadges"
            :disabled="isLoading"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
          >
            <Icon v-if="isLoading" name="spinner" class="w-4 h-4 mr-2 animate-spin" />
            Vérifier tous les badges
          </button>

          <button
            @click="closeModal"
            class="px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
          >
            Fermer
          </button>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script setup lang="ts">
import { ref, reactive, watch, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/components/ui/Modal.vue'
import Select from '@/components/ui/Select.vue'
import Icon from '@/components/ui/Icon.vue'

interface Badge {
  id: number
  name: string
  icon: string
  points: number
}

interface Props {
  modelValue: boolean
  user: {
    id: number
    user_badges?: Array<{
      id: number
      badge: Badge
    }>
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
const availableBadges = ref<Badge[]>([])

const form = reactive({
  badge_id: '',
  reason: ''
})

watch(() => props.modelValue, (value) => {
  isOpen.value = value
  if (value) {
    form.badge_id = ''
    form.reason = ''
    loadAvailableBadges()
  }
})

watch(isOpen, (value) => {
  emit('update:modelValue', value)
})

const loadAvailableBadges = async () => {
  try {
    const response = await fetch(route('admin.badges.list'))
    const badges = await response.json()
    
    // Filtrer les badges déjà attribués
    const userBadgeIds = props.user.user_badges?.map(ub => ub.badge.id) || []
    availableBadges.value = badges.filter((badge: Badge) => !userBadgeIds.includes(badge.id))
  } catch (error) {
    console.error('Erreur lors du chargement des badges:', error)
  }
}

const awardBadge = async () => {
  if (!form.badge_id) return

  isLoading.value = true
  try {
    await router.post(route('admin.users.awardBadge', props.user.id), form)
    emit('updated')
    form.badge_id = ''
    form.reason = ''
    loadAvailableBadges()
  } finally {
    isLoading.value = false
  }
}

const revokeBadge = async (badgeId: number) => {
  isLoading.value = true
  try {
    await router.delete(route('admin.users.revokeBadge', props.user.id), {
      data: { badge_id: badgeId }
    })
    emit('updated')
    loadAvailableBadges()
  } finally {
    isLoading.value = false
  }
}

const checkAllBadges = async () => {
  isLoading.value = true
  try {
    await router.post(route('admin.badges.checkUser'), {
      user_id: props.user.id
    })
    emit('updated')
  } finally {
    isLoading.value = false
  }
}

const closeModal = () => {
  isOpen.value = false
}
</script>