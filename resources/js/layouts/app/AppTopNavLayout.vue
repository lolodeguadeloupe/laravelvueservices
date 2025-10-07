<template>
  <div class="min-h-screen bg-white">
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-100 sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex items-center">
            <Link :href="route('home')" class="flex items-center">
              <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
              </svg>
              <span class="ml-3 text-xl font-bold text-gray-900">ServicesPro</span>
            </Link>
          </div>

          <!-- Navigation Links -->
          <div class="hidden md:flex space-x-8">
            <Link :href="route('services.index')" class="text-gray-600 hover:text-primary transition-colors">
              Services
            </Link>
            <Link :href="route('how-it-works')" class="text-gray-600 hover:text-primary transition-colors">
              Comment ça marche
            </Link>
            <Link :href="route('about')" class="text-gray-600 hover:text-primary transition-colors">
              À propos
            </Link>
            <Link :href="route('contact')" class="text-gray-600 hover:text-primary transition-colors">
              Contact
            </Link>
          </div>

          <!-- Auth Links -->
          <div class="flex items-center space-x-4">
            <template v-if="$page.props.auth.user">
              <Link :href="route('dashboard')" class="text-gray-600 hover:text-primary transition-colors">
                Dashboard
              </Link>
              <Link :href="route('logout')" method="post" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                Déconnexion
              </Link>
            </template>
            <template v-else>
              <Link :href="route('login')" class="text-gray-600 hover:text-primary transition-colors">
                Se connecter
              </Link>
              <Link :href="route('register')" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">
                S'inscrire
              </Link>
            </template>
          </div>

          <!-- Mobile menu button -->
          <div class="md:hidden">
            <button @click="showMobileMenu = !showMobileMenu" class="text-gray-500 hover:text-gray-700 p-2">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-show="showMobileMenu" class="md:hidden border-t border-gray-100 py-4 space-y-2">
          <Link :href="route('services.index')" class="block px-4 py-2 text-gray-600 hover:text-primary hover:bg-gray-50 rounded">
            Services
          </Link>
          <Link :href="route('how-it-works')" class="block px-4 py-2 text-gray-600 hover:text-primary hover:bg-gray-50 rounded">
            Comment ça marche
          </Link>
          <Link :href="route('about')" class="block px-4 py-2 text-gray-600 hover:text-primary hover:bg-gray-50 rounded">
            À propos
          </Link>
          <Link :href="route('contact')" class="block px-4 py-2 text-gray-600 hover:text-primary hover:bg-gray-50 rounded">
            Contact
          </Link>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <main>
      <slot />
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div class="col-span-1 md:col-span-2">
            <div class="flex items-center mb-6">
              <svg class="w-10 h-10 text-primary" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
              </svg>
              <span class="ml-3 text-2xl font-bold">ServicesPro</span>
            </div>
            <p class="text-gray-300 max-w-md mb-6">
              La plateforme de référence pour mettre en relation clients et prestataires de services de qualité.
            </p>
            
            <!-- Social Links -->
            <div class="flex space-x-4">
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                </svg>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                </svg>
              </a>
              <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                </svg>
              </a>
            </div>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-4">Services</h3>
            <ul class="space-y-2 text-gray-300">
              <li><Link :href="route('services.index') + '?category=menage'" class="hover:text-white transition-colors">Ménage</Link></li>
              <li><Link :href="route('services.index') + '?category=jardinage'" class="hover:text-white transition-colors">Jardinage</Link></li>
              <li><Link :href="route('services.index') + '?category=bricolage'" class="hover:text-white transition-colors">Bricolage</Link></li>
              <li><Link :href="route('services.index') + '?category=cours'" class="hover:text-white transition-colors">Cours particuliers</Link></li>
            </ul>
          </div>

          <div>
            <h3 class="text-lg font-semibold mb-4">Support</h3>
            <ul class="space-y-2 text-gray-300">
              <li><Link :href="route('help')" class="hover:text-white transition-colors">Centre d'aide</Link></li>
              <li><Link :href="route('contact')" class="hover:text-white transition-colors">Contact</Link></li>
              <li><Link :href="route('terms')" class="hover:text-white transition-colors">CGU</Link></li>
              <li><Link :href="route('privacy')" class="hover:text-white transition-colors">Confidentialité</Link></li>
            </ul>
          </div>
        </div>

        <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-300">
          <p>&copy; 2024 ServicesPro. Tous droits réservés.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { route } from '@/utils/routes'

const showMobileMenu = ref(false)
</script>