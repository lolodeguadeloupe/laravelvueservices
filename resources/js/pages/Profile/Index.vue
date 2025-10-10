<script setup>
import { ref, computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const props = defineProps({
  user: Object,
  badges: {
    type: Array,
    default: () => []
  },
  badgeStats: {
    type: Object,
    default: () => ({})
  }
})

const activeTab = ref('profile')

// Fonction pour formater la date
const formatDate = (date) => {
  return new Intl.DateTimeFormat('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(new Date(date))
}

// Filtrer les badges par type
const featuredBadges = computed(() => 
  props.badges.filter(userBadge => userBadge.is_featured)
)

const publicBadges = computed(() => 
  props.badges.filter(userBadge => userBadge.is_public)
)

const privateBadges = computed(() => 
  props.badges.filter(userBadge => !userBadge.is_public)
)

// Fonction pour basculer la visibilité d'un badge
const toggleBadgeVisibility = (badgeId, isPublic) => {
  router.post(route('profile.badges.toggle-visibility'), {
    badge_id: badgeId,
    is_public: isPublic
  }, {
    preserveScroll: true
  })
}

// Fonction pour basculer le statut featured d'un badge
const toggleBadgeFeatured = (badgeId, isFeatured) => {
  router.post(route('profile.badges.toggle-featured'), {
    badge_id: badgeId,
    is_featured: isFeatured
  }, {
    preserveScroll: true
  })
}
</script>

<template>
  <AppLayout>
    <div class="min-h-screen bg-gradient-to-br from-green-50 to-amber-50">
      <!-- Header du profil -->
      <div class="bg-white border-b border-green-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-6">
              <!-- Avatar -->
              <div class="flex-shrink-0">
                <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center">
                  <span class="text-2xl font-bold text-white">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </span>
                </div>
              </div>
              
              <!-- Informations utilisateur -->
              <div>
                <h1 class="text-3xl font-bold text-gray-900">
                  {{ user.name }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                  {{ user.email }}
                </p>
                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                  <span v-if="user.phone">📞 {{ user.phone }}</span>
                  <span v-if="user.address">📍 {{ user.address }}</span>
                </div>
              </div>
            </div>
            
            <!-- Actions -->
            <div class="mt-4 sm:mt-0">
              <Link
                href="/settings/profile"
                class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md text-green-600 hover:bg-green-50 transition-colors"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifier le profil
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Navigation par onglets -->
        <div class="mb-8">
          <nav class="flex space-x-8" aria-label="Tabs">
            <button
              @click="activeTab = 'profile'"
              :class="[
                activeTab === 'profile'
                  ? 'border-green-500 text-green-600 bg-green-50'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors'
              ]"
            >
              <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Profil
            </button>
            
            <button
              @click="activeTab = 'badges'"
              :class="[
                activeTab === 'badges'
                  ? 'border-green-500 text-green-600 bg-green-50'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                'whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors'
              ]"
            >
              <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
              Mes Badges
              <span v-if="badges.length > 0" class="ml-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">
                {{ badges.length }}
              </span>
            </button>
          </nav>
        </div>

        <!-- Contenu des onglets -->
        <div class="tab-content">
          <!-- Onglet Profil -->
          <div v-if="activeTab === 'profile'" class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6">Informations personnelles</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900">{{ user.name }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900">{{ user.email }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Téléphone</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900">{{ user.phone || 'Non renseigné' }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">Type de compte</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900 capitalize">{{ user.user_type }}</div>
                </div>
                
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Adresse</label>
                  <div class="p-3 bg-gray-50 rounded-md text-gray-900">{{ user.address || 'Non renseignée' }}</div>
                </div>
              </div>
            </div>

            <!-- Statistiques du profil -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques du profil</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gradient-to-r from-green-500 to-green-600 rounded-lg text-white">
                  <div class="text-2xl font-bold">{{ badges.length }}</div>
                  <div class="text-sm opacity-90">Badges obtenus</div>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white">
                  <div class="text-2xl font-bold">{{ publicBadges.length }}</div>
                  <div class="text-sm opacity-90">Badges publics</div>
                </div>
                
                <div class="text-center p-4 bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg text-white">
                  <div class="text-2xl font-bold">{{ featuredBadges.length }}</div>
                  <div class="text-sm opacity-90">Badges mis en avant</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Onglet Badges -->
          <div v-if="activeTab === 'badges'" class="space-y-6">
            <!-- Badges mis en avant -->
            <div v-if="featuredBadges.length > 0">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Badges mis en avant</h2>
              <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="userBadge in featuredBadges"
                  :key="userBadge.id"
                  class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 relative"
                >
                  <div class="absolute top-2 right-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      ⭐ Mis en avant
                    </span>
                  </div>
                  
                  <div class="text-center">
                    <div class="text-4xl mb-3">{{ userBadge.badge.icon }}</div>
                    <h3 class="font-semibold text-gray-900 mb-2">{{ userBadge.badge.name }}</h3>
                    <p class="text-sm text-gray-600 mb-4">{{ userBadge.badge.description }}</p>
                    
                    <div class="text-xs text-gray-500">
                      Obtenu le {{ formatDate(userBadge.earned_at) }}
                    </div>
                    
                    <div class="mt-4 flex items-center justify-center space-x-2">
                      <button
                        @click="toggleBadgeVisibility(userBadge.badge.id, !userBadge.is_public)"
                        :class="[
                          'px-3 py-1 rounded-full text-xs font-medium transition-colors',
                          userBadge.is_public
                            ? 'bg-green-100 text-green-800 hover:bg-green-200'
                            : 'bg-gray-100 text-gray-800 hover:bg-gray-200'
                        ]"
                      >
                        {{ userBadge.is_public ? '🌍 Public' : '🔒 Privé' }}
                      </button>
                      
                      <button
                        @click="toggleBadgeFeatured(userBadge.badge.id, false)"
                        class="px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800 hover:bg-amber-200 transition-colors"
                      >
                        Retirer de la mise en avant
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Badges publics -->
            <div v-if="publicBadges.length > 0">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Badges publics</h2>
              <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div
                  v-for="userBadge in publicBadges.filter(b => !b.is_featured)"
                  :key="userBadge.id"
                  class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center"
                >
                  <div class="text-3xl mb-2">{{ userBadge.badge.icon }}</div>
                  <h3 class="font-medium text-gray-900 text-sm mb-1">{{ userBadge.badge.name }}</h3>
                  <p class="text-xs text-gray-600 mb-3">{{ userBadge.badge.description }}</p>
                  
                  <div class="flex items-center justify-center space-x-1">
                    <button
                      @click="toggleBadgeVisibility(userBadge.badge.id, false)"
                      class="px-2 py-1 rounded text-xs bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors"
                      title="Rendre privé"
                    >
                      🔒
                    </button>
                    
                    <button
                      v-if="featuredBadges.length < 3"
                      @click="toggleBadgeFeatured(userBadge.badge.id, true)"
                      class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition-colors"
                      title="Mettre en avant"
                    >
                      ⭐
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Badges privés -->
            <div v-if="privateBadges.length > 0">
              <h2 class="text-xl font-semibold text-gray-900 mb-4">Badges privés</h2>
              <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div
                  v-for="userBadge in privateBadges"
                  :key="userBadge.id"
                  class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-center opacity-75"
                >
                  <div class="text-3xl mb-2">{{ userBadge.badge.icon }}</div>
                  <h3 class="font-medium text-gray-900 text-sm mb-1">{{ userBadge.badge.name }}</h3>
                  <p class="text-xs text-gray-600 mb-3">{{ userBadge.badge.description }}</p>
                  
                  <button
                    @click="toggleBadgeVisibility(userBadge.badge.id, true)"
                    class="px-2 py-1 rounded text-xs bg-green-100 text-green-600 hover:bg-green-200 transition-colors"
                    title="Rendre public"
                  >
                    🌍 Rendre public
                  </button>
                </div>
              </div>
            </div>

            <!-- Message si aucun badge -->
            <div v-if="badges.length === 0" class="text-center py-12">
              <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun badge obtenu</h3>
              <p class="mt-1 text-sm text-gray-500">Continuez à utiliser la plateforme pour débloquer des badges.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>