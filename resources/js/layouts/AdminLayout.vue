<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0" :class="{ '-translate-x-full': !sidebarOpen }">
      <!-- Logo -->
      <div class="flex items-center justify-center h-16 bg-green-600 dark:bg-green-700">
        <Link href="/admin" class="flex items-center gap-2 text-white font-bold text-xl">
          <Icon name="shield-check" class="w-8 h-8" />
          Admin Panel
        </Link>
      </div>

      <!-- Navigation -->
      <nav class="mt-8 px-4">
        <div class="space-y-2">
          <!-- Dashboard -->
          <NavItem
            href="/admin"
            icon="home"
            label="Dashboard"
            :active="route().current('admin.dashboard')"
          />

          <!-- Gestion des utilisateurs -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Gestion des utilisateurs
            </h3>
            <div class="mt-2 space-y-1">
              <NavItem
                href="/admin/users"
                icon="users"
                label="Utilisateurs"
                :active="route().current('admin.users.*')"
                :badge="pendingVerifications"
              />
              <NavItem
                href="/admin/badges"
                icon="award"
                label="Badges"
                :active="route().current('admin.badges.*')"
              />
            </div>
          </div>

          <!-- Modération -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Modération
            </h3>
            <div class="mt-2 space-y-1">
              <NavItem
                href="/admin/moderation"
                icon="shield"
                label="Modération"
                :active="route().current('admin.moderation.*')"
                :badge="pendingModerations"
                badge-variant="warning"
              />
              <NavItem
                href="/admin/moderation/reports"
                icon="flag"
                label="Signalements"
                :active="route().current('admin.moderation.reports')"
                :badge="pendingReports"
                badge-variant="danger"
              />
            </div>
          </div>

          <!-- Contenu -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Contenu
            </h3>
            <div class="mt-2 space-y-1">
              <NavItem
                href="/admin/services"
                icon="briefcase"
                label="Services"
                :active="route().current('admin.services')"
              />
              <NavItem
                href="/admin/bookings"
                icon="calendar"
                label="Réservations"
                :active="route().current('admin.bookings')"
              />
            </div>
          </div>

          <!-- Analytics -->
          <div class="pt-4">
            <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
              Analytics
            </h3>
            <div class="mt-2 space-y-1">
              <NavItem
                href="/admin/analytics"
                icon="chart-bar"
                label="Statistiques"
                :active="route().current('admin.analytics')"
              />
              <NavItem
                href="/admin/settings"
                icon="cog"
                label="Paramètres"
                :active="route().current('admin.settings')"
              />
            </div>
          </div>
        </div>
      </nav>

      <!-- User section -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-gray-700">
        <DropdownMenu>
          <template #trigger>
            <button class="flex items-center gap-3 w-full p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
              <img
                :src="$page.props.auth.user.avatar_url || '/images/default-avatar.png'"
                :alt="$page.props.auth.user.name"
                class="w-8 h-8 rounded-full object-cover"
              />
              <div class="flex-1 text-left">
                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">
                  {{ $page.props.auth.user.name }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">
                  Administrateur
                </div>
              </div>
              <Icon name="chevron-up" class="w-4 h-4 text-gray-400" />
            </button>
          </template>

          <template #content>
            <DropdownItem @click="visitHomepage">
              <Icon name="home" class="w-4 h-4 mr-3" />
              Voir le site
            </DropdownItem>
            <DropdownItem @click="logout">
              <Icon name="logout" class="w-4 h-4 mr-3" />
              Déconnexion
            </DropdownItem>
          </template>
        </DropdownMenu>
      </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Main content -->
    <div class="lg:pl-64">
      <!-- Top bar -->
      <div class="sticky top-0 z-30 flex h-16 items-center gap-x-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
        <!-- Mobile menu button -->
        <button
          type="button"
          class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-200 lg:hidden"
          @click="sidebarOpen = !sidebarOpen"
        >
          <Icon name="bars-3" class="h-6 w-6" />
        </button>

        <!-- Breadcrumb -->
        <div class="flex-1 flex items-center gap-x-4">
          <nav class="flex" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
              <li>
                <Link href="/admin" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                  Admin
                </Link>
              </li>
              <li v-if="breadcrumb">
                <div class="flex items-center">
                  <Icon name="chevron-right" class="h-4 w-4 text-gray-300 dark:text-gray-600 mx-2" />
                  <span class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ breadcrumb }}
                  </span>
                </div>
              </li>
            </ol>
          </nav>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
          <!-- Notifications -->
          <button
            type="button"
            class="relative rounded-full bg-white dark:bg-gray-800 p-1 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
          >
            <Icon name="bell" class="h-6 w-6" />
            <span
              v-if="totalNotifications > 0"
              class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-500 text-xs font-medium text-white flex items-center justify-center"
            >
              {{ totalNotifications > 9 ? '9+' : totalNotifications }}
            </span>
          </button>

          <!-- Dark mode toggle -->
          <button
            @click="toggleDarkMode"
            class="rounded-full bg-white dark:bg-gray-800 p-1 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
          >
            <Icon :name="isDark ? 'sun' : 'moon'" class="h-6 w-6" />
          </button>
        </div>
      </div>

      <!-- Page content -->
      <main class="py-8 px-4 sm:px-6 lg:px-8">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { usePage } from '@inertiajs/vue3'
import NavItem from '@/components/admin/NavItem.vue'
import DropdownMenu from '@/components/ui/DropdownMenu.vue'
import DropdownItem from '@/components/ui/DropdownItem.vue'
import Icon from '@/components/ui/Icon.vue'

interface Props {
  breadcrumb?: string
  pendingVerifications?: number
  pendingModerations?: number
  pendingReports?: number
}

const props = withDefaults(defineProps<Props>(), {
  pendingVerifications: 0,
  pendingModerations: 0,
  pendingReports: 0
})

const page = usePage()
const sidebarOpen = ref(false)
const isDark = ref(false)

const totalNotifications = computed(() => {
  return props.pendingVerifications + props.pendingModerations + props.pendingReports
})

const visitHomepage = () => {
  window.open('/', '_blank')
}

const logout = () => {
  router.post('/logout')
}

const toggleDarkMode = () => {
  isDark.value = !isDark.value
  // Implementation depends on your dark mode system
  document.documentElement.classList.toggle('dark', isDark.value)
  localStorage.setItem('darkMode', isDark.value.toString())
}

onMounted(() => {
  // Initialize dark mode from localStorage
  const savedDarkMode = localStorage.getItem('darkMode')
  if (savedDarkMode !== null) {
    isDark.value = savedDarkMode === 'true'
    document.documentElement.classList.toggle('dark', isDark.value)
  } else {
    // Check system preference
    isDark.value = window.matchMedia('(prefers-color-scheme: dark)').matches
    document.documentElement.classList.toggle('dark', isDark.value)
  }
})
</script>