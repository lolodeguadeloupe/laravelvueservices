<template>
  <AdminLayout :breadcrumb="`Utilisateurs / ${user.name}`">
    <Head :title="`Utilisateur: ${user.name}`" />

    <div class="space-y-6">
      <!-- En-tête avec actions -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div class="flex items-center gap-4">
          <Link
            :href="route('admin.users.index')"
            class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200"
          >
            <Icon name="arrow-left" class="w-5 h-5" />
          </Link>
          <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ user.name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ user.email }}</p>
          </div>
        </div>

        <div class="flex gap-3">
          <button
            @click="toggleUserStatus"
            :class="[
              'px-4 py-2 rounded-lg font-medium',
              user.is_active
                ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900 dark:text-red-200'
                : 'bg-green-100 text-green-700 hover:bg-green-200 dark:bg-green-900 dark:text-green-200'
            ]"
          >
            {{ user.is_active ? 'Désactiver' : 'Activer' }}
          </button>
          <button
            v-if="!user.email_verified_at"
            @click="verifyUser"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            Vérifier manuellement
          </button>
        </div>
      </div>

      <!-- Informations principales -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profil utilisateur -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Informations générales -->
          <Card class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Informations générales
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Type d'utilisateur
                </label>
                <div class="flex items-center gap-2">
                  <Badge :variant="getUserTypeVariant(user.user_type)">
                    {{ getUserTypeLabel(user.user_type) }}
                  </Badge>
                  <button
                    @click="showChangeTypeModal = true"
                    class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
                  >
                    Modifier
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Statut
                </label>
                <Badge :variant="user.is_active ? 'success' : 'danger'">
                  {{ user.is_active ? 'Actif' : 'Inactif' }}
                </Badge>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Email vérifié
                </label>
                <Badge :variant="user.email_verified_at ? 'success' : 'warning'">
                  {{ user.email_verified_at ? 'Vérifié' : 'Non vérifié' }}
                </Badge>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Date d'inscription
                </label>
                <p class="text-sm text-gray-900 dark:text-white">
                  {{ formatDate(user.created_at) }}
                </p>
              </div>
            </div>

            <div v-if="user.profile" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
              <h4 class="font-medium text-gray-900 dark:text-white mb-4">Profil</h4>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-if="user.profile.rating">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Note moyenne
                  </label>
                  <div class="flex items-center gap-2">
                    <div class="flex">
                      <Icon
                        v-for="i in 5"
                        :key="i"
                        name="star"
                        :class="[
                          'w-4 h-4',
                          i <= Math.round(user.profile.rating)
                            ? 'text-yellow-400 fill-current'
                            : 'text-gray-300 dark:text-gray-600'
                        ]"
                      />
                    </div>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ user.profile.rating.toFixed(1) }}
                    </span>
                  </div>
                </div>

                <div v-if="user.profile.reputation_points !== undefined">
                  <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Points de réputation
                  </label>
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ user.profile.reputation_points }}
                  </p>
                </div>
              </div>

              <div v-if="user.profile.bio" class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                  Biographie
                </label>
                <p class="text-sm text-gray-900 dark:text-white">
                  {{ user.profile.bio }}
                </p>
              </div>
            </div>
          </Card>

          <!-- Services (pour les prestataires) -->
          <Card v-if="user.user_type === 'provider' && user.services?.length > 0" class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Services proposés ({{ user.services.length }})
            </h3>
            <div class="space-y-3">
              <div
                v-for="service in user.services"
                :key="service.id"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
              >
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900 dark:text-white">{{ service.title }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ service.category?.name }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ formatPrice(service.price_min) }} - {{ formatPrice(service.price_max) }}
                  </p>
                  <Badge :variant="service.is_active ? 'success' : 'default'">
                    {{ service.is_active ? 'Actif' : 'Inactif' }}
                  </Badge>
                </div>
              </div>
            </div>
          </Card>

          <!-- Historique des réservations -->
          <Card v-if="user.client_bookings?.length > 0 || user.provider_bookings?.length > 0" class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
              Réservations récentes
            </h3>
            <div class="space-y-3">
              <!-- Réservations en tant que client -->
              <div
                v-for="booking in [...(user.client_bookings || []), ...(user.provider_bookings || [])].slice(0, 5)"
                :key="booking.id"
                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
              >
                <div>
                  <h4 class="font-medium text-gray-900 dark:text-white">{{ booking.service?.title }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ formatDate(booking.scheduled_date) }}
                  </p>
                </div>
                <Badge :variant="getBookingStatusVariant(booking.status)">
                  {{ booking.status }}
                </Badge>
              </div>
            </div>
          </Card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
          <!-- Statistiques -->
          <Card class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Statistiques</h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Services créés</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ userStats.total_services }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Réservations client</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ userStats.total_bookings_as_client }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Réservations prestataire</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ userStats.total_bookings_as_provider }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Avis donnés</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ userStats.total_reviews_given }}</span>
              </div>
              <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">Avis reçus</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ userStats.total_reviews_received }}</span>
              </div>
            </div>
          </Card>

          <!-- Badges -->
          <Card class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Badges ({{ badgeStats.total_earned }})
              </h3>
              <button
                @click="showBadgeModal = true"
                class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
              >
                Gérer
              </button>
            </div>

            <div v-if="user.user_badges?.length > 0" class="space-y-3">
              <div
                v-for="userBadge in user.user_badges"
                :key="userBadge.id"
                class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
              >
                <span class="text-2xl">{{ userBadge.badge.icon }}</span>
                <div class="flex-1">
                  <h4 class="font-medium text-gray-900 dark:text-white">{{ userBadge.badge.name }}</h4>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ userBadge.badge.description }}</p>
                </div>
                <div class="text-right">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ userBadge.badge.points }} pts</div>
                  <div class="text-xs text-gray-500">{{ formatDate(userBadge.created_at) }}</div>
                </div>
              </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500 dark:text-gray-400">
              Aucun badge attribué
            </div>

            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
              <div class="flex justify-between text-sm">
                <span class="text-gray-600 dark:text-gray-400">Points totaux</span>
                <span class="font-medium text-gray-900 dark:text-white">{{ badgeStats.total_points }}</span>
              </div>
            </div>
          </Card>

          <!-- Actions rapides -->
          <Card class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Actions</h3>
            <div class="space-y-3">
              <button
                @click="checkBadges"
                :disabled="isLoading"
                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
              >
                <Icon v-if="isLoading" name="spinner" class="w-4 h-4 mr-2 animate-spin" />
                Vérifier les badges
              </button>
              
              <button
                @click="showDeleteModal = true"
                class="w-full px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 dark:bg-red-900 dark:text-red-200"
              >
                Supprimer l'utilisateur
              </button>
            </div>
          </Card>
        </div>
      </div>
    </div>

    <!-- Modals -->
    <ChangeUserTypeModal
      v-model="showChangeTypeModal"
      :user="user"
      @updated="handleUserUpdate"
    />

    <BadgeManagementModal
      v-model="showBadgeModal"
      :user="user"
      @updated="handleUserUpdate"
    />

    <ConfirmModal
      v-model="showDeleteModal"
      title="Supprimer l'utilisateur"
      message="Cette action est irréversible. Êtes-vous sûr de vouloir supprimer cet utilisateur ?"
      confirm-text="Supprimer"
      :danger="true"
      :loading="isLoading"
      @confirm="deleteUser"
    />
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import Card from '@/components/ui/Card.vue'
import Badge from '@/components/ui/Badge.vue'
import Icon from '@/components/ui/Icon.vue'
import ConfirmModal from '@/components/ui/ConfirmModal.vue'
import ChangeUserTypeModal from '@/components/admin/ChangeUserTypeModal.vue'
import BadgeManagementModal from '@/components/admin/BadgeManagementModal.vue'

interface Props {
  user: any // Type complet à définir selon vos besoins
  userStats: any
  badgeStats: any
}

const props = defineProps<Props>()

const showChangeTypeModal = ref(false)
const showBadgeModal = ref(false)
const showDeleteModal = ref(false)
const isLoading = ref(false)

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

const getBookingStatusVariant = (status: string) => {
  const variants = {
    pending: 'warning',
    confirmed: 'default',
    completed: 'success',
    cancelled: 'danger'
  }
  return variants[status as keyof typeof variants] || 'default'
}

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(price)
}

const toggleUserStatus = async () => {
  isLoading.value = true
  try {
    await router.patch(route('admin.users.updateStatus', props.user.id), {
      is_active: !props.user.is_active,
      reason: `${props.user.is_active ? 'Désactivation' : 'Activation'} via interface admin`
    })
  } finally {
    isLoading.value = false
  }
}

const verifyUser = async () => {
  isLoading.value = true
  try {
    await router.patch(route('admin.users.verify', props.user.id), {
      reason: 'Vérification manuelle par un administrateur'
    })
  } finally {
    isLoading.value = false
  }
}

const checkBadges = async () => {
  isLoading.value = true
  try {
    await router.post(route('admin.badges.checkUser'), {
      user_id: props.user.id
    })
  } finally {
    isLoading.value = false
  }
}

const deleteUser = async () => {
  isLoading.value = true
  try {
    await router.delete(route('admin.users.destroy', props.user.id), {
      data: {
        reason: 'Suppression via interface admin'
      }
    })
    router.visit(route('admin.users.index'))
  } finally {
    isLoading.value = false
    showDeleteModal.value = false
  }
}

const handleUserUpdate = () => {
  // Refresh the page data
  router.reload()
}
</script>