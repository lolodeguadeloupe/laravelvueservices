<template>
  <AdminLayout>
    <Head title="Dashboard Admin" />
    
    <div class="space-y-6">
      <!-- En-tête avec titre et actions -->
      <div class="flex justify-between items-center">
        <div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Admin</h1>
          <p class="text-gray-600 dark:text-gray-400 mt-1">
            Vue d'ensemble de la plateforme de services
          </p>
        </div>
        <div class="flex gap-3">
          <button
            @click="refreshData"
            :disabled="isLoading"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
          >
            <Icon name="refresh" class="w-4 h-4 mr-2" :class="{ 'animate-spin': isLoading }" />
            Actualiser
          </button>
          <Link
            href="/admin/analytics"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            <Icon name="chart-bar" class="w-4 h-4 mr-2" />
            Analytics
          </Link>
        </div>
      </div>

      <!-- Cartes de statistiques principales -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatsCard
          title="Utilisateurs Total"
          :value="stats.users.total"
          :change="stats.users.growth"
          icon="users"
          color="blue"
        />
        <StatsCard
          title="Prestataires Actifs"
          :value="stats.users.providers"
          :change="stats.users.provider_growth"
          icon="briefcase"
          color="green"
        />
        <StatsCard
          title="Réservations ce mois"
          :value="stats.bookings.this_month"
          :change="stats.bookings.growth"
          icon="calendar"
          color="purple"
        />
        <StatsCard
          title="Chiffre d'affaires"
          :value="formatCurrency(stats.revenue.total)"
          :change="stats.revenue.growth"
          icon="currency-euro"
          color="emerald"
        />
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Graphique des inscriptions -->
        <Card class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              Inscriptions (30 derniers jours)
            </h3>
            <div class="flex gap-2">
              <button
                v-for="period in ['7d', '30d', '90d']"
                :key="period"
                @click="chartPeriod = period"
                :class="[
                  'px-3 py-1 text-sm rounded-md',
                  chartPeriod === period
                    ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100'
                    : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700'
                ]"
              >
                {{ period }}
              </button>
            </div>
          </div>
          <UserRegistrationChart :data="charts.registrations" :period="chartPeriod" />
        </Card>

        <!-- Top catégories -->
        <Card class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Catégories populaires
          </h3>
          <div class="space-y-3">
            <div
              v-for="category in stats.categories.top"
              :key="category.id"
              class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
            >
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-100 dark:bg-green-800 flex items-center justify-center">
                  <Icon :name="category.icon" class="w-4 h-4 text-green-600 dark:text-green-300" />
                </div>
                <span class="font-medium text-gray-900 dark:text-white">{{ category.name }}</span>
              </div>
              <div class="text-right">
                <div class="font-semibold text-gray-900 dark:text-white">{{ category.services_count }}</div>
                <div class="text-sm text-gray-500">services</div>
              </div>
            </div>
          </div>
        </Card>
      </div>

      <!-- Activité récente et modération -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Activité récente -->
        <Card class="p-6 lg:col-span-2">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              Activité récente
            </h3>
            <Link
              href="/admin/activity"
              class="text-green-600 hover:text-green-700 text-sm font-medium"
            >
              Voir tout
            </Link>
          </div>
          <div class="space-y-3">
            <div
              v-for="activity in recentActivity"
              :key="activity.id"
              class="flex items-start gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg"
            >
              <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-800 flex items-center justify-center flex-shrink-0">
                <Icon :name="getActivityIcon(activity.type)" class="w-4 h-4 text-green-600 dark:text-green-300" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ activity.description }}
                </p>
                <p class="text-sm text-gray-500">
                  {{ formatRelativeTime(activity.created_at) }}
                </p>
              </div>
            </div>
          </div>
        </Card>

        <!-- Modération en attente -->
        <Card class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              Modération
            </h3>
            <Link
              href="/admin/moderation"
              class="text-green-600 hover:text-green-700 text-sm font-medium"
            >
              Gérer
            </Link>
          </div>
          <div class="space-y-3">
            <div class="flex justify-between items-center p-3 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
              <div>
                <div class="font-semibold text-yellow-800 dark:text-yellow-200">
                  {{ stats.moderation.pending_reviews }}
                </div>
                <div class="text-sm text-yellow-600 dark:text-yellow-300">
                  Avis en attente
                </div>
              </div>
              <Icon name="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-300" />
            </div>
            <div class="flex justify-between items-center p-3 bg-red-50 dark:bg-red-900 rounded-lg">
              <div>
                <div class="font-semibold text-red-800 dark:text-red-200">
                  {{ stats.moderation.flagged_reviews }}
                </div>
                <div class="text-sm text-red-600 dark:text-red-300">
                  Avis signalés
                </div>
              </div>
              <Icon name="flag" class="w-5 h-5 text-red-600 dark:text-red-300" />
            </div>
            <div class="flex justify-between items-center p-3 bg-orange-50 dark:bg-orange-900 rounded-lg">
              <div>
                <div class="font-semibold text-orange-800 dark:text-orange-200">
                  {{ stats.moderation.reports_count }}
                </div>
                <div class="text-sm text-orange-600 dark:text-orange-300">
                  Signalements
                </div>
              </div>
              <Icon name="exclamation-triangle" class="w-5 h-5 text-orange-600 dark:text-orange-300" />
            </div>
          </div>
        </Card>
      </div>

      <!-- Badges récents et utilisateurs VIP -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Badges récemment attribués -->
        <Card class="p-6">
          <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
              Badges récents
            </h3>
            <Link
              href="/admin/badges"
              class="text-green-600 hover:text-green-700 text-sm font-medium"
            >
              Gérer badges
            </Link>
          </div>
          <div class="space-y-3">
            <div
              v-for="badge in stats.badges.recent_awards"
              :key="badge.id"
              class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg"
            >
              <div class="text-2xl">{{ badge.badge.icon }}</div>
              <div class="flex-1">
                <div class="font-medium text-gray-900 dark:text-white">
                  {{ badge.badge.name }}
                </div>
                <div class="text-sm text-gray-500">
                  attribué à {{ badge.user.name }}
                </div>
              </div>
              <div class="text-sm text-gray-400">
                {{ formatRelativeTime(badge.created_at) }}
              </div>
            </div>
          </div>
        </Card>

        <!-- Top utilisateurs -->
        <Card class="p-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Utilisateurs VIP
          </h3>
          <div class="space-y-3">
            <div
              v-for="user in stats.users.top_rated"
              :key="user.id"
              class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg"
            >
              <img
                :src="user.avatar_url || '/images/default-avatar.png'"
                :alt="user.name"
                class="w-10 h-10 rounded-full object-cover"
              />
              <div class="flex-1">
                <div class="font-medium text-gray-900 dark:text-white">
                  {{ user.name }}
                </div>
                <div class="flex items-center gap-2">
                  <div class="flex items-center">
                    <Icon name="star" class="w-4 h-4 text-yellow-400 fill-current" />
                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-1">
                      {{ user.profile?.rating?.toFixed(1) || 'N/A' }}
                    </span>
                  </div>
                  <span class="text-gray-400">•</span>
                  <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ user.services_count }} services
                  </span>
                </div>
              </div>
              <Badge :variant="user.user_type === 'provider' ? 'success' : 'default'">
                {{ user.user_type }}
              </Badge>
            </div>
          </div>
        </Card>
      </div>
    </div>
  </AdminLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/layouts/AdminLayout.vue'
import StatsCard from '@/components/admin/StatsCard.vue'
import UserRegistrationChart from '@/components/admin/UserRegistrationChart.vue'
import Card from '@/components/ui/Card.vue'
import Badge from '@/components/ui/Badge.vue'
import Icon from '@/components/ui/Icon.vue'

interface Props {
  stats: {
    users: {
      total: number
      clients: number
      providers: number
      admins: number
      growth: number
      provider_growth: number
      top_rated: Array<{
        id: number
        name: string
        email: string
        user_type: string
        avatar_url?: string
        services_count: number
        profile?: {
          rating: number
        }
      }>
    }
    bookings: {
      total: number
      this_month: number
      pending: number
      completed: number
      growth: number
    }
    revenue: {
      total: number
      this_month: number
      growth: number
    }
    categories: {
      total: number
      top: Array<{
        id: number
        name: string
        icon: string
        services_count: number
      }>
    }
    badges: {
      total: number
      awarded_count: number
      recent_awards: Array<{
        id: number
        created_at: string
        badge: {
          name: string
          icon: string
        }
        user: {
          name: string
        }
      }>
    }
    moderation: {
      pending_reviews: number
      flagged_reviews: number
      reports_count: number
    }
  }
  charts: {
    registrations: Array<{
      date: string
      clients: number
      providers: number
    }>
  }
  recentActivity: Array<{
    id: number
    type: string
    description: string
    created_at: string
  }>
}

const props = defineProps<Props>()

const isLoading = ref(false)
const chartPeriod = ref('30d')

const refreshData = async () => {
  isLoading.value = true
  try {
    await window.location.reload()
  } finally {
    isLoading.value = false
  }
}

const formatCurrency = (amount: number): string => {
  return new Intl.NumberFormat('fr-FR', {
    style: 'currency',
    currency: 'EUR'
  }).format(amount)
}

const formatRelativeTime = (date: string): string => {
  const rtf = new Intl.RelativeTimeFormat('fr', { numeric: 'auto' })
  const diff = new Date(date).getTime() - new Date().getTime()
  const days = Math.round(diff / (1000 * 60 * 60 * 24))
  
  if (Math.abs(days) < 1) {
    const hours = Math.round(diff / (1000 * 60 * 60))
    if (Math.abs(hours) < 1) {
      const minutes = Math.round(diff / (1000 * 60))
      return rtf.format(minutes, 'minute')
    }
    return rtf.format(hours, 'hour')
  }
  
  return rtf.format(days, 'day')
}

const getActivityIcon = (type: string): string => {
  const icons: Record<string, string> = {
    user_registered: 'user-plus',
    booking_created: 'calendar-plus',
    service_created: 'briefcase',
    review_posted: 'star',
    badge_awarded: 'award',
    report_created: 'flag'
  }
  return icons[type] || 'activity'
}
</script>