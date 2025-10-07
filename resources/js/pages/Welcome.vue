<template>
  <AppLayout>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary/5 via-white to-secondary/5">
      <div class="absolute inset-0">
        <div class="absolute inset-0 bg-gradient-to-br from-primary/10 to-secondary/10"></div>
        <div class="absolute top-0 left-0 w-full h-full">
          <div class="absolute top-20 left-10 w-32 h-32 bg-primary/20 rounded-full blur-3xl"></div>
          <div class="absolute bottom-20 right-10 w-40 h-40 bg-secondary/20 rounded-full blur-3xl"></div>
          <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-primary/10 rounded-full blur-3xl"></div>
        </div>
      </div>
      
      <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <!-- Contenu principal -->
          <div>
            <div class="inline-flex items-center bg-white/80 backdrop-blur-sm border border-primary/20 rounded-full px-4 py-2 mb-8">
              <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
              <span class="text-sm font-medium text-gray-700">Plus de 1000 prestataires qualifiés</span>
            </div>
            
            <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
              Trouvez le
              <span class="text-primary">prestataire</span>
              parfait pour vos
              <span class="text-secondary">besoins</span>
            </h1>
            
            <p class="text-xl text-gray-600 mb-8 max-w-2xl">
              Découvrez des professionnels vérifiés près de chez vous. 
              Ménage, jardinage, bricolage, cours... Tout ce dont vous avez besoin en quelques clics.
            </p>

            <!-- Barre de recherche principale -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-2 mb-8">
              <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                <!-- Service recherché -->
                <div class="md:col-span-5">
                  <div class="relative">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                      v-model="searchForm.service"
                      type="text"
                      placeholder="Quel service recherchez-vous ?"
                      class="w-full pl-12 pr-4 py-4 border-0 rounded-xl focus:ring-2 focus:ring-primary focus:outline-none text-gray-900 placeholder-gray-500"
                    >
                  </div>
                </div>
                
                <!-- Localisation -->
                <div class="md:col-span-4">
                  <div class="relative">
                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <input
                      v-model="searchForm.location"
                      type="text"
                      placeholder="Où ?"
                      class="w-full pl-12 pr-4 py-4 border-0 rounded-xl focus:ring-2 focus:ring-primary focus:outline-none text-gray-900 placeholder-gray-500"
                    >
                  </div>
                </div>

                <!-- Bouton recherche -->
                <div class="md:col-span-3">
                  <button
                    @click="performSearch"
                    class="w-full bg-primary text-white py-4 px-6 rounded-xl font-semibold hover:bg-primary-dark transition-colors shadow-lg hover:shadow-xl"
                  >
                    Rechercher
                  </button>
                </div>
              </div>
            </div>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
              <Link
                :href="route('services.index')"
                class="bg-primary text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-primary-dark transition-colors shadow-lg hover:shadow-xl inline-flex items-center justify-center"
              >
                Découvrir les services
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
              </Link>
              
              <Link
                :href="route('provider.registration.step1')"
                class="bg-white text-gray-700 border-2 border-gray-200 px-8 py-4 rounded-xl font-semibold text-lg hover:border-primary hover:text-primary transition-colors inline-flex items-center justify-center"
              >
                Devenir prestataire
              </Link>
            </div>
          </div>

          <!-- Image/Illustration hero -->
          <div class="relative">
            <div class="bg-gradient-to-br from-primary/20 to-secondary/20 rounded-3xl p-8 transform rotate-3 hover:rotate-0 transition-transform duration-500">
              <div class="bg-white rounded-2xl p-6 shadow-2xl">
                <!-- Simulation d'interface -->
                <div class="space-y-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900">Service réservé</p>
                      <p class="text-sm text-gray-500">Ménage à domicile</p>
                    </div>
                  </div>
                  
                  <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                      <span class="text-sm text-gray-600">Marie Dubois</span>
                      <div class="flex items-center">
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                          <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="text-sm text-gray-600 ml-1">4.9</span>
                      </div>
                    </div>
                    <p class="text-xs text-gray-500">Prestation terminée avec succès</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Statistiques -->
    <section class="py-16 bg-white border-t border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div class="text-center">
            <div class="text-3xl md:text-4xl font-bold text-primary mb-2">1000+</div>
            <div class="text-gray-600">Prestataires</div>
          </div>
          <div class="text-center">
            <div class="text-3xl md:text-4xl font-bold text-primary mb-2">50k+</div>
            <div class="text-gray-600">Services réalisés</div>
          </div>
          <div class="text-center">
            <div class="text-3xl md:text-4xl font-bold text-primary mb-2">4.8/5</div>
            <div class="text-gray-600">Note moyenne</div>
          </div>
          <div class="text-center">
            <div class="text-3xl md:text-4xl font-bold text-primary mb-2">24h</div>
            <div class="text-gray-600">Temps de réponse</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services populaires -->
    <section class="py-20 bg-gray-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Services les plus demandés
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Découvrez nos catégories de services les plus populaires
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="category in featuredCategories"
            :key="category.name"
            @click="searchCategory(category.name)"
            class="group bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer border-2 border-transparent hover:border-primary/20"
          >
            <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
              <svg class="w-8 h-8 text-primary" fill="currentColor" viewBox="0 0 24 24">
                <path :d="category.icon" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ category.name }}</h3>
            <p class="text-gray-600 mb-4">{{ category.description }}</p>
            <div class="flex flex-wrap gap-2">
              <span
                v-for="service in category.services"
                :key="service"
                class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm group-hover:bg-primary/10 group-hover:text-primary transition-colors"
              >
                {{ service }}
              </span>
            </div>
          </div>
        </div>

        <div class="text-center mt-12">
          <Link
            :href="route('services.index')"
            class="inline-flex items-center bg-primary text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-primary-dark transition-colors shadow-lg hover:shadow-xl"
          >
            Voir tous les services
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </Link>
        </div>
      </div>
    </section>

    <!-- Comment ça marche -->
    <section id="how-it-works" class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Comment ça marche ?
          </h2>
          <p class="text-xl text-gray-600 max-w-2xl mx-auto">
            Un processus simple en 3 étapes pour trouver votre prestataire idéal
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
          <div
            v-for="(step, index) in howItWorks"
            :key="index"
            class="relative text-center group"
          >
            <!-- Connecteur -->
            <div v-if="index < howItWorks.length - 1" class="hidden md:block absolute top-8 left-full w-full h-0.5 bg-gradient-to-r from-primary/30 to-transparent z-0"></div>
            
            <div class="relative z-10">
              <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-dark rounded-2xl flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                {{ index + 1 }}
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ step.title }}</h3>
              <p class="text-gray-600">{{ step.description }}</p>
            </div>
          </div>
        </div>

        <div class="text-center mt-16">
          <Link
            :href="route('services.index')"
            class="inline-flex items-center bg-secondary text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-secondary/90 transition-colors shadow-lg hover:shadow-xl"
          >
            Commencer maintenant
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </Link>
        </div>
      </div>
    </section>

    <!-- Avantages prestataires -->
    <section class="py-20 bg-gradient-to-br from-secondary/5 to-primary/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
          <div class="order-2 lg:order-1">
            <div class="bg-gradient-to-br from-secondary/20 to-primary/20 rounded-3xl p-8 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
              <div class="bg-white rounded-2xl p-8 shadow-2xl">
                <div class="space-y-6">
                  <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900">Revenus augmentés</p>
                      <p class="text-sm text-gray-500">+40% en moyenne</p>
                    </div>
                  </div>
                  
                  <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                      <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                    </div>
                    <div>
                      <p class="font-semibold text-gray-900">Nouveaux clients</p>
                      <p class="text-sm text-gray-500">Chaque semaine</p>
                    </div>
                  </div>

                  <div class="bg-gradient-to-r from-primary/10 to-secondary/10 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-700">🎯 Commission réduite : seulement 8%</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="order-1 lg:order-2">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
              Vous êtes un prestataire de services ?
            </h2>
            <p class="text-xl text-gray-600 mb-8">
              Rejoignez notre plateforme et développez votre activité grâce à une clientèle qualifiée.
            </p>
            
            <div class="space-y-6 mb-8">
              <div
                v-for="benefit in providerBenefits"
                :key="benefit.title"
                class="flex items-start group"
              >
                <div class="flex-shrink-0 w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center mr-4 group-hover:bg-primary/20 transition-colors">
                  <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-1">{{ benefit.title }}</h3>
                  <p class="text-gray-600">{{ benefit.description }}</p>
                </div>
              </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
              <Link
                :href="route('provider.registration.step1')"
                class="bg-secondary text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-secondary/90 transition-colors inline-flex items-center justify-center shadow-lg hover:shadow-xl"
              >
                Devenir prestataire
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
              </Link>
              
              <button class="border-2 border-gray-200 text-gray-700 px-8 py-4 rounded-xl font-semibold text-lg hover:border-secondary hover:text-secondary transition-colors">
                En savoir plus
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Témoignages -->
    <section class="py-20 bg-white">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Ce que disent nos utilisateurs
          </h2>
          <p class="text-xl text-gray-600">
            Des milliers de clients et prestataires nous font confiance
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div
            v-for="testimonial in testimonials"
            :key="testimonial.name"
            class="bg-gradient-to-br from-white to-gray-50 rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100"
          >
            <div class="flex items-center mb-6">
              <div class="w-12 h-12 bg-primary/20 text-primary rounded-full flex items-center justify-center font-bold mr-4">
                {{ testimonial.name.charAt(0) }}
              </div>
              <div>
                <p class="font-semibold text-gray-900">{{ testimonial.name }}</p>
                <p class="text-sm text-gray-500">{{ testimonial.role }}</p>
              </div>
            </div>
            
            <div class="flex items-center mb-4">
              <div class="flex">
                <svg v-for="i in 5" :key="i" class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </div>
            </div>
            
            <p class="text-gray-700 italic">"{{ testimonial.content }}"</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Final -->
    <section class="py-20 bg-gradient-to-r from-primary to-primary-dark text-white">
      <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold mb-6">
          Prêt à commencer ?
        </h2>
        <p class="text-xl mb-8 opacity-90">
          Rejoignez des milliers d'utilisateurs qui font confiance à ServicesPro
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <Link
            :href="route('services.index')"
            class="bg-white text-primary px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg hover:shadow-xl"
          >
            Trouver un service
          </Link>
          
          <Link
            :href="route('provider.registration.step1')"
            class="bg-secondary text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-secondary/90 transition-colors shadow-lg hover:shadow-xl border-2 border-white/20"
          >
            Devenir prestataire
          </Link>
        </div>
      </div>
    </section>

  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import { route } from '@/utils/routes'
import AppLayout from '@/layouts/AppLayout.vue'

// State
const searchForm = ref({
  service: '',
  location: ''
})

// Data
const featuredCategories = [
  {
    name: 'Ménage & Nettoyage',
    description: 'Professionels du nettoyage pour votre domicile',
    icon: 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2z',
    services: ['Ménage régulier', 'Grand nettoyage', 'Vitres']
  },
  {
    name: 'Jardinage',
    description: 'Entretien et aménagement de vos espaces verts',
    icon: 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
    services: ['Tonte pelouse', 'Taille haies', 'Plantation']
  },
  {
    name: 'Bricolage & Réparations',
    description: 'Petits travaux et réparations du quotidien',
    icon: 'M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z',
    services: ['Montage meuble', 'Réparations', 'Peinture']
  },
  {
    name: 'Cours Particuliers',
    description: 'Accompagnement scolaire et formations',
    icon: 'M12 14l9-5-9-5-9 5 9 5z',
    services: ['Mathématiques', 'Langues', 'Informatique']
  },
  {
    name: 'Beauté & Bien-être',
    description: 'Services de beauté à domicile',
    icon: 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
    services: ['Coiffure', 'Esthétique', 'Massage']
  },
  {
    name: 'Garde d\'enfants',
    description: 'Solutions de garde flexibles et sécurisées',
    icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    services: ['Babysitting', 'Garde régulière', 'Sortie école']
  }
]

const howItWorks = [
  {
    title: 'Décrivez votre besoin',
    description: 'Précisez le service souhaité, vos préférences et votre localisation en quelques clics.'
  },
  {
    title: 'Recevez des propositions',
    description: 'Des prestataires qualifiés vous contactent avec des devis personnalisés.'
  },
  {
    title: 'Choisissez et réservez',
    description: 'Comparez les profils, choisissez votre prestataire et planifiez votre service.'
  }
]

const providerBenefits = [
  {
    title: 'Clients pré-qualifiés',
    description: 'Recevez des demandes de clients sérieux dans votre zone d\'intervention'
  },
  {
    title: 'Gestion simplifiée',
    description: 'Outils intégrés pour gérer vos devis, plannings et paiements'
  },
  {
    title: 'Commission réduite',
    description: 'Seulement 8% de commission sur vos prestations réalisées'
  },
  {
    title: 'Support dédié',
    description: 'Équipe support disponible pour vous accompagner dans votre activité'
  }
]

const testimonials = [
  {
    name: 'Marie L.',
    role: 'Cliente',
    content: 'Service impeccable ! J\'ai trouvé une femme de ménage fantastique en moins de 24h. Interface très intuitive.'
  },
  {
    name: 'Thomas B.',
    role: 'Prestataire jardinage',
    content: 'Grâce à ServicesPro, j\'ai augmenté mon chiffre d\'affaires de 40%. Les clients sont de qualité et les paiements sécurisés.'
  },
  {
    name: 'Sophie D.',
    role: 'Cliente',
    content: 'Parfait pour mes cours de piano ! Le prestataire était exactement ce que je cherchais. Je recommande vivement.'
  }
]

// Methods
const performSearch = () => {
  if (searchForm.value.service.trim() || searchForm.value.location.trim()) {
    // Redirection vers la page des services avec les paramètres de recherche
    router.visit(route('services.index', {
      search: searchForm.value.service,
      location: searchForm.value.location
    }))
  } else {
    // Si aucun critère, aller simplement à la page des services
    router.visit(route('services.index'))
  }
}

const searchCategory = (categoryName) => {
  searchForm.value.service = categoryName
  performSearch()
}
</script>