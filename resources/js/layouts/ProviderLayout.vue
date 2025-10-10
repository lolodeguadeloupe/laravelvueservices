<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo et navigation principale -->
          <div class="flex items-center">
            <Link :href="route('provider.dashboard')" class="flex items-center">
              <div class="flex-shrink-0 flex items-center">
                <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                </svg>
                <span class="ml-2 text-xl font-bold text-gray-900">ServicesPro</span>
              </div>
            </Link>
            
            <!-- Navigation principale -->
            <div class="hidden md:ml-8 md:flex md:space-x-8">
              <Link
                :href="route('provider.dashboard')"
                :class="route().current('provider.dashboard') 
                  ? 'border-primary text-primary' 
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="border-b-2 px-1 pt-1 pb-4 text-sm font-medium transition-colors"
              >
                Tableau de bord
              </Link>
              <Link
                :href="route('provider.services.index')"
                :class="route().current('provider.services.*') 
                  ? 'border-primary text-primary' 
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="border-b-2 px-1 pt-1 pb-4 text-sm font-medium transition-colors"
              >
                Mes Services
              </Link>
              <Link
                :href="route('provider.bookings')"
                :class="route().current('provider.bookings') 
                  ? 'border-primary text-primary' 
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="border-b-2 px-1 pt-1 pb-4 text-sm font-medium transition-colors"
              >
                Réservations
              </Link>
              <Link
                :href="route('provider.earnings')"
                :class="route().current('provider.earnings') 
                  ? 'border-primary text-primary' 
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                class="border-b-2 px-1 pt-1 pb-4 text-sm font-medium transition-colors"
              >
                Gains
              </Link>
            </div>
          </div>

          <!-- Actions utilisateur -->
          <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button class="relative p-2 text-gray-400 hover:text-gray-500">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5 5-5H15m-6 10H4l5-5-5-5h5" />
              </svg>
              <span class="absolute top-0 right-0 h-2 w-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- Menu utilisateur -->
            <div class="relative">
              <button
                @click="showUserMenu = !showUserMenu"
                class="flex items-center space-x-3 p-2 text-sm rounded-lg hover:bg-gray-100"
              >
                <div class="w-8 h-8 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium">
                  {{ $page.props.auth.user.name?.charAt(0).toUpperCase() }}
                </div>
                <span class="hidden md:block text-gray-700">{{ $page.props.auth.user.name }}</span>
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <!-- Dropdown menu -->
              <div
                v-show="showUserMenu"
                @click.away="showUserMenu = false"
                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
              >
                <Link
                  :href="route('provider.profile')"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Mon profil
                </Link>
                <Link
                  :href="route('settings.profile')"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Paramètres
                </Link>
                <div class="border-t border-gray-100 my-1"></div>
                <Link
                  href="/logout"
                  method="post"
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                  Déconnexion
                </Link>
              </div>
            </div>

            <!-- Menu mobile -->
            <button
              @click="showMobileMenu = !showMobileMenu"
              class="md:hidden p-2 text-gray-400 hover:text-gray-500"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
      </div>

      <!-- Menu mobile -->
      <div v-show="showMobileMenu" class="md:hidden border-t border-gray-200 bg-white">
        <div class="px-2 pt-2 pb-3 space-y-1">
          <Link
            :href="route('provider.dashboard')"
            :class="route().current('provider.dashboard')
              ? 'bg-primary/10 border-primary text-primary'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
            class="block border-l-4 pl-3 pr-4 py-2 text-base font-medium"
          >
            Tableau de bord
          </Link>
          <Link
            :href="route('provider.services.index')"
            :class="route().current('provider.services.*')
              ? 'bg-primary/10 border-primary text-primary'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
            class="block border-l-4 pl-3 pr-4 py-2 text-base font-medium"
          >
            Mes Services
          </Link>
          <Link
            :href="route('provider.bookings')"
            :class="route().current('provider.bookings')
              ? 'bg-primary/10 border-primary text-primary'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
            class="block border-l-4 pl-3 pr-4 py-2 text-base font-medium"
          >
            Réservations
          </Link>
          <Link
            :href="route('provider.earnings')"
            :class="route().current('provider.earnings')
              ? 'bg-primary/10 border-primary text-primary'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:bg-gray-50'"
            class="block border-l-4 pl-3 pr-4 py-2 text-base font-medium"
          >
            Gains
          </Link>
        </div>
      </div>
    </nav>

    <!-- Contenu principal -->
    <main>
      <slot />
    </main>

    <!-- Toast notifications -->
    <div
      v-if="$page.props.flash?.success"
      class="fixed bottom-4 right-4 bg-green-100 border border-green-200 text-green-700 px-6 py-3 rounded-lg shadow-lg z-50"
    >
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        {{ $page.props.flash.success }}
      </div>
    </div>

    <div
      v-if="$page.props.flash?.error"
      class="fixed bottom-4 right-4 bg-red-100 border border-red-200 text-red-700 px-6 py-3 rounded-lg shadow-lg z-50"
    >
      <div class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        {{ $page.props.flash.error }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

// State
const showUserMenu = ref(false)
const showMobileMenu = ref(false)
</script>