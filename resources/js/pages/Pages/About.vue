<template>
  <AppLayout>
    <div class="min-h-screen bg-white">
      <!-- Hero Section -->
      <section class="bg-gradient-to-br from-primary/5 via-white to-secondary/5 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
              À propos de <span class="text-primary">ServicesPro</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
              Nous connectons les clients avec des prestataires de services qualifiés 
              pour simplifier votre quotidien et développer l'activité des professionnels.
            </p>
          </div>
        </div>
      </section>

      <!-- Notre Mission -->
      <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
              <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                Notre mission
              </h2>
              <p class="text-lg text-gray-600 mb-6">
                ServicesPro est née d'un constat simple : il est souvent difficile de trouver 
                un prestataire de confiance pour les services du quotidien. En même temps, 
                de nombreux professionnels peinent à faire connaître leur expertise.
              </p>
              <p class="text-lg text-gray-600 mb-6">
                Notre plateforme crée un pont entre ces deux mondes, en garantissant 
                <strong class="text-primary">qualité</strong>, <strong class="text-primary">sécurité</strong> 
                et <strong class="text-primary">simplicité</strong> pour tous.
              </p>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="text-center p-4">
                  <div class="w-16 h-16 bg-primary/20 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                  <h3 class="font-semibold text-gray-900 mb-2">Sécurité</h3>
                  <p class="text-sm text-gray-600">Prestataires vérifiés et assurés</p>
                </div>
                
                <div class="text-center p-4">
                  <div class="w-16 h-16 bg-secondary/20 text-secondary rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                  </div>
                  <h3 class="font-semibold text-gray-900 mb-2">Simplicité</h3>
                  <p class="text-sm text-gray-600">Réservation en quelques clics</p>
                </div>
                
                <div class="text-center p-4">
                  <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                  <h3 class="font-semibold text-gray-900 mb-2">Qualité</h3>
                  <p class="text-sm text-gray-600">Services de haute qualité garantis</p>
                </div>
              </div>
            </div>
            
            <div class="relative">
              <div class="bg-gradient-to-br from-primary/20 to-secondary/20 rounded-3xl p-8 transform rotate-3 hover:rotate-0 transition-transform duration-500">
                <div class="bg-white rounded-2xl p-8 shadow-2xl">
                  <div class="space-y-6">
                    <div class="text-center">
                      <div class="text-4xl font-bold text-primary mb-2">2019</div>
                      <p class="text-gray-600">Création de ServicesPro</p>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-6 space-y-4">
                      <div class="flex items-center justify-between">
                        <span class="text-gray-600">Prestataires actifs</span>
                        <span class="font-bold text-gray-900">1000+</span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span class="text-gray-600">Services réalisés</span>
                        <span class="font-bold text-gray-900">50 000+</span>
                      </div>
                      <div class="flex items-center justify-between">
                        <span class="text-gray-600">Note moyenne</span>
                        <div class="flex items-center">
                          <span class="font-bold text-gray-900 mr-2">4.8</span>
                          <div class="flex text-yellow-400">
                            <svg v-for="i in 5" :key="i" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Nos Valeurs -->
      <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Nos valeurs
            </h2>
            <p class="text-xl text-gray-600">
              Les principes qui guident nos actions au quotidien
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
              <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Communauté</h3>
              <p class="text-gray-600">
                Nous créons des liens durables entre clients et prestataires basés sur la confiance mutuelle.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
              <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Excellence</h3>
              <p class="text-gray-600">
                Nous nous engageons à offrir la meilleure expérience possible à nos utilisateurs.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
              <div class="w-16 h-16 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Innovation</h3>
              <p class="text-gray-600">
                Nous utilisons la technologie pour simplifier et améliorer les services du quotidien.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
              <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Transparence</h3>
              <p class="text-gray-600">
                Nous privilégions la clarté et l'honnêteté dans toutes nos interactions.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- L'équipe -->
      <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Notre équipe
            </h2>
            <p class="text-xl text-gray-600">
              Des passionnés dédiés à votre réussite
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center group">
              <div class="relative mb-6">
                <div class="w-32 h-32 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-full mx-auto flex items-center justify-center text-3xl font-bold text-primary group-hover:scale-110 transition-transform duration-300">
                  AS
                </div>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Alexandre Silva</h3>
              <p class="text-primary font-medium mb-3">CEO & Fondateur</p>
              <p class="text-gray-600 text-sm">
                Entrepreneur passionné avec 10 ans d'expérience dans les services à domicile.
              </p>
            </div>

            <div class="text-center group">
              <div class="relative mb-6">
                <div class="w-32 h-32 bg-gradient-to-br from-secondary/20 to-primary/20 rounded-full mx-auto flex items-center justify-center text-3xl font-bold text-secondary group-hover:scale-110 transition-transform duration-300">
                  MD
                </div>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Marie Dubois</h3>
              <p class="text-primary font-medium mb-3">CTO</p>
              <p class="text-gray-600 text-sm">
                Experte en technologie avec une vision centrée sur l'expérience utilisateur.
              </p>
            </div>

            <div class="text-center group">
              <div class="relative mb-6">
                <div class="w-32 h-32 bg-gradient-to-br from-green-100 to-blue-100 rounded-full mx-auto flex items-center justify-center text-3xl font-bold text-green-600 group-hover:scale-110 transition-transform duration-300">
                  PL
                </div>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-2">Pierre Laurent</h3>
              <p class="text-primary font-medium mb-3">Responsable Partenariats</p>
              <p class="text-gray-600 text-sm">
                Spécialiste du développement commercial et de la relation prestataires.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Rejoignez-nous -->
      <section class="py-20 bg-gradient-to-r from-primary to-primary-dark text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
          <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Rejoignez l'aventure ServicesPro
          </h2>
          <p class="text-xl mb-8 opacity-90">
            Que vous soyez client ou prestataire, nous sommes là pour vous accompagner
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

          <div class="mt-12 pt-8 border-t border-white/20">
            <p class="text-white/80">
              Des questions ? <Link :href="route('contact')" class="text-white font-semibold hover:underline">Contactez-nous</Link>
            </p>
          </div>
        </div>
      </section>
    </div>
  </AppLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { route } from '@/utils/routes'
</script>