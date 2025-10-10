<script setup>
import { computed } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  user: Object,
  badges: {
    type: Array,
    default: () => []
  },
  badgeStats: {
    type: Object,
    default: () => ({})
  },
  isOwner: {
    type: Boolean,
    default: false
  }
})

// Fonction pour formater la date
const formatDate = (date) => {
  return new Intl.DateTimeFormat('fr-FR', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  }).format(new Date(date))
}

// Filtrer les badges mis en avant
const featuredBadges = computed(() => 
  props.badges.filter(userBadge => userBadge.is_featured)
)

// Autres badges publics
const otherBadges = computed(() => 
  props.badges.filter(userBadge => !userBadge.is_featured)
)
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
                <p class="mt-1 text-sm text-gray-600 capitalize">
                  {{ user.user_type === 'provider' ? 'Prestataire' : 'Client' }}
                </p>
                <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                  <span v-if="user.profile?.company_name">
                    🏢 {{ user.profile.company_name }}
                  </span>
                  <span v-if="user.profile?.rating">
                    ⭐ {{ user.profile.rating }}/5
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Actions (si propriétaire du profil) -->
            <div v-if="isOwner" class="mt-4 sm:mt-0">
              <Link
                href="/profile"
                class="inline-flex items-center px-4 py-2 border border-green-600 text-sm font-medium rounded-md text-green-600 hover:bg-green-50 transition-colors"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Gérer mon profil
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Colonne principale -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Informations du profil -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6">À propos</h2>
              
              <div class="space-y-4">
                <div v-if="user.profile?.bio">
                  <h3 class="text-sm font-medium text-gray-700 mb-2">Présentation</h3>
                  <p class="text-gray-900">{{ user.profile.bio }}</p>
                </div>
                
                <div v-if="user.profile?.experience">
                  <h3 class="text-sm font-medium text-gray-700 mb-2">Expérience</h3>
                  <p class="text-gray-900">{{ user.profile.experience }}</p>
                </div>
                
                <div v-if="user.profile?.certifications">
                  <h3 class="text-sm font-medium text-gray-700 mb-2">Certifications</h3>
                  <p class="text-gray-900">{{ user.profile.certifications }}</p>
                </div>
                
                <!-- Message par défaut si pas d'informations -->
                <div v-if="!user.profile?.bio && !user.profile?.experience && !user.profile?.certifications" class="text-center py-8 text-gray-500">
                  <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  <p class="mt-2">Aucune information de profil disponible</p>
                </div>
              </div>
            </div>

            <!-- Badges mis en avant -->
            <div v-if="featuredBadges.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6">Badges mis en avant</h2>
              
              <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div
                  v-for="userBadge in featuredBadges"
                  :key="userBadge.id"
                  class="bg-gradient-to-br from-yellow-50 to-amber-50 rounded-lg border-2 border-yellow-200 p-4 text-center relative"
                >
                  <div class="absolute top-2 right-2">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      ⭐
                    </span>
                  </div>
                  
                  <div class="text-4xl mb-3">{{ userBadge.badge.icon }}</div>
                  <h3 class="font-semibold text-gray-900 mb-2">{{ userBadge.badge.name }}</h3>
                  <p class="text-sm text-gray-600 mb-3">{{ userBadge.badge.description }}</p>
                  
                  <div class="text-xs text-gray-500">
                    Obtenu le {{ formatDate(userBadge.earned_at) }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Autres badges -->
            <div v-if="otherBadges.length > 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h2 class="text-xl font-semibold text-gray-900 mb-6">Autres badges</h2>
              
              <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <div
                  v-for="userBadge in otherBadges"
                  :key="userBadge.id"
                  class="bg-gray-50 rounded-lg p-3 text-center hover:bg-gray-100 transition-colors"
                >
                  <div class="text-2xl mb-1">{{ userBadge.badge.icon }}</div>
                  <h3 class="font-medium text-gray-900 text-sm mb-1">{{ userBadge.badge.name }}</h3>
                  <p class="text-xs text-gray-600">{{ userBadge.badge.description }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="lg:col-span-1 space-y-6">
            <!-- Statistiques rapides -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistiques</h3>
              
              <div class="space-y-4">
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Badges obtenus</span>
                  <span class="text-lg font-semibold text-green-600">{{ badges.length }}</span>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Badges mis en avant</span>
                  <span class="text-lg font-semibold text-yellow-600">{{ featuredBadges.length }}</span>
                </div>
                
                <div v-if="user.profile?.rating" class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Note moyenne</span>
                  <span class="text-lg font-semibold text-blue-600">{{ user.profile.rating }}/5</span>
                </div>
                
                <div v-if="user.user_type === 'provider'" class="flex items-center justify-between">
                  <span class="text-sm text-gray-600">Type de compte</span>
                  <span class="text-sm font-medium text-green-600">Prestataire</span>
                </div>
              </div>
            </div>

            <!-- Informations de contact (si prestataire) -->
            <div v-if="user.user_type === 'provider' && user.profile?.company_name" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations</h3>
              
              <div class="space-y-3">
                <div v-if="user.profile.company_name">
                  <span class="text-sm text-gray-600 block">Entreprise</span>
                  <span class="text-sm font-medium text-gray-900">{{ user.profile.company_name }}</span>
                </div>
                
                <div v-if="user.profile.phone">
                  <span class="text-sm text-gray-600 block">Téléphone</span>
                  <span class="text-sm font-medium text-gray-900">{{ user.profile.phone }}</span>
                </div>
                
                <div v-if="user.profile.website">
                  <span class="text-sm text-gray-600 block">Site web</span>
                  <a :href="user.profile.website" target="_blank" class="text-sm font-medium text-green-600 hover:text-green-700">
                    {{ user.profile.website }}
                  </a>
                </div>
              </div>
              
              <!-- Bouton de contact -->
              <div class="mt-6 pt-4 border-t border-gray-200">
                <button class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                  Contacter {{ user.name }}
                </button>
              </div>
            </div>

            <!-- Message si aucun badge -->
            <div v-if="badges.length === 0" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 text-center">
              <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun badge public</h3>
              <p class="mt-1 text-xs text-gray-500">Cet utilisateur n'a pas encore de badges publics</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>