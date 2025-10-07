<template>
  <AdminLayout breadcrumb="Utilisateurs">
    <Head title="Gestion des utilisateurs" />

    <div class="space-y-6">
      <!-- En-tête -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Gestion des utilisateurs</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            {{ stats.total }} utilisateurs au total
          </p>
        </div>
        <div class="flex gap-3">
          <Link
            :href="route('admin.users.export')"
            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
          >
            <Icon name="download" class="w-4 h-4 mr-2" />
            Exporter CSV
          </Link>
        </div>
      </div>

      <!-- Statistiques -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <StatsCard
          title="Total utilisateurs"
          :value="stats.total"
          icon="users"
          color="blue"
        />
        <StatsCard
          title="Clients"
          :value="stats.clients"
          icon="user"
          color="green"
        />
        <StatsCard
          title="Prestataires"
          :value="stats.providers"
          icon="briefcase"
          color="purple"
        />
        <StatsCard
          title="Vérifiés"
          :value="stats.verified"
          icon="shield-check"
          color="emerald"
        />
      </div>

      <!-- Filtres et recherche -->
      <Card class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <!-- Recherche -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Recherche
            </label>
            <Input
              v-model="filters.search"
              placeholder="Nom ou email..."
              @input="debouncedSearch"
            />
          </div>

          <!-- Type d'utilisateur -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Type
            </label>
            <Select v-model="filters.user_type" @change="applyFilters">
              <option value="">Tous</option>
              <option value="client">Clients</option>
              <option value="provider">Prestataires</option>
              <option value="admin">Administrateurs</option>
            </Select>
          </div>

          <!-- Statut -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Statut
            </label>
            <Select v-model="filters.is_active" @change="applyFilters">
              <option value="">Tous</option>
              <option value="1">Actifs</option>
              <option value="0">Inactifs</option>
            </Select>
          </div>

          <!-- Tri -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Trier par
            </label>
            <Select v-model="filters.sort_by" @change="applyFilters">
              <option value="created_at">Date d'inscription</option>
              <option value="name">Nom</option>
              <option value="email">Email</option>
              <option value="last_login_at">Dernière connexion</option>
            </Select>
          </div>
        </div>
      </Card>

      <!-- Tableau des utilisateurs -->
      <Card>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Utilisateur
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Type
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Statut
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Services
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Badges
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Inscription
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
              <tr
                v-for="user in users.data"
                :key="user.id"
                class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
              >
                <!-- Utilisateur -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <img
                      :src="user.avatar_url || '/images/default-avatar.png'"
                      :alt="user.name"
                      class="w-10 h-10 rounded-full object-cover"
                    />
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ user.name }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ user.email }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Type -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <Badge :variant="getUserTypeVariant(user.user_type)">
                    {{ getUserTypeLabel(user.user_type) }}
                  </Badge>
                </td>

                <!-- Statut -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-2">
                    <Badge :variant="user.is_active ? 'success' : 'danger'">
                      {{ user.is_active ? 'Actif' : 'Inactif' }}
                    </Badge>
                    <Badge v-if="user.email_verified_at" variant="default" class="text-xs">
                      Vérifié
                    </Badge>
                  </div>
                </td>

                <!-- Services -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  {{ user.services_count || 0 }}
                </td>

                <!-- Badges -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center gap-1">
                    <span class="text-sm text-gray-900 dark:text-white">
                      {{ user.user_badges_count || 0 }}
                    </span>
                    <div v-if="user.user_badges && user.user_badges.length > 0" class="flex -space-x-1">
                      <span
                        v-for="badge in user.user_badges.slice(0, 3)"
                        :key="badge.id"
                        class="text-lg"
                        :title="badge.badge.name"
                      >
                        {{ badge.badge.icon }}
                      </span>
                      <span
                        v-if="user.user_badges.length > 3"
                        class="text-xs text-gray-500 ml-1"
                      >
                        +{{ user.user_badges.length - 3 }}
                      </span>
                    </div>
                  </div>
                </td>

                <!-- Inscription -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ formatDate(user.created_at) }}
                </td>

                <!-- Actions -->
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex justify-end gap-2">
                    <Link
                      :href="route('admin.users.show', user.id)"
                      class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                    >
                      Voir
                    </Link>
                    <button
                      @click="toggleUserStatus(user)"
                      :class="[
                        'font-medium',
                        user.is_active
                          ? 'text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300'
                          : 'text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300'
                      ]"
                    >
                      {{ user.is_active ? 'Désactiver' : 'Activer' }}
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="users.links" class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
          <Pagination :links="users.links" />
        </div>
      </Card>
    </div>

    <!-- Modal de confirmation -->
    <ConfirmModal
      v-model="showConfirmModal"
      :title="confirmAction.title"
      :message="confirmAction.message"
      :confirm-text="confirmAction.confirmText"
      :loading="isLoading"
      @confirm="executeAction"
    />
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, reactive, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { debounce } from 'lodash-es'
import AdminLayout from '@/layouts/AdminLayout.vue'
import StatsCard from '@/components/admin/StatsCard.vue'
import Card from '@/components/ui/Card.vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import Badge from '@/components/ui/Badge.vue'
import Icon from '@/components/ui/Icon.vue'
import Pagination from '@/components/ui/Pagination.vue'
import ConfirmModal from '@/components/ui/ConfirmModal.vue'

interface User {
  id: number
  name: string
  email: string
  user_type: string
  is_active: boolean
  email_verified_at: string | null
  created_at: string
  avatar_url?: string
  services_count: number
  user_badges_count: number
  user_badges: Array<{
    id: number
    badge: {
      name: string
      icon: string
    }
  }>
}

interface Props {
  users: {
    data: User[]
    links: any[]
  }
  stats: {
    total: number
    clients: number
    providers: number
    admins: number
    active: number
    verified: number
  }
  filters: {
    search: string
    user_type: string
    is_active: string
    sort_by: string
    sort_order: string
  }
}

const props = defineProps<Props>()

const filters = reactive({ ...props.filters })
const showConfirmModal = ref(false)
const isLoading = ref(false)
const confirmAction = ref({
  title: '',
  message: '',
  confirmText: '',
  user: null as User | null,
  action: '' as string
})

const applyFilters = () => {
  router.get(route('admin.users.index'), filters, {
    preserveState: true,
    preserveScroll: true
  })
}

const debouncedSearch = debounce(() => {
  applyFilters()
}, 300)

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

const formatDate = (date: string) => {
  return new Date(date).toLocaleDateString('fr-FR')
}

const toggleUserStatus = (user: User) => {
  confirmAction.value = {
    title: user.is_active ? 'Désactiver utilisateur' : 'Activer utilisateur',
    message: `Êtes-vous sûr de vouloir ${user.is_active ? 'désactiver' : 'activer'} ${user.name} ?`,
    confirmText: user.is_active ? 'Désactiver' : 'Activer',
    user,
    action: 'toggleStatus'
  }
  showConfirmModal.value = true
}

const executeAction = async () => {
  if (!confirmAction.value.user) return

  isLoading.value = true
  try {
    await router.patch(
      route('admin.users.updateStatus', confirmAction.value.user.id),
      {
        is_active: !confirmAction.value.user.is_active,
        reason: `${confirmAction.value.user.is_active ? 'Désactivation' : 'Activation'} via interface admin`
      }
    )
    showConfirmModal.value = false
  } finally {
    isLoading.value = false
  }
}
</script>