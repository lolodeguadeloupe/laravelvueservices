<template>
  <AppLayout>
    <div class="min-h-screen bg-white">
      <!-- Hero Section -->
      <section class="bg-gradient-to-br from-primary/5 via-white to-secondary/5 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
              Comment fonctionne <span class="text-primary">ServicesPro</span> ?
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
              Découvrez en quelques étapes simples comment trouver le bon prestataire 
              ou développer votre activité sur notre plateforme.
            </p>
          </div>
        </div>
      </section>

      <!-- Pour les clients -->
      <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Pour les <span class="text-primary">clients</span>
            </h2>
            <p class="text-xl text-gray-600">
              Trouvez le prestataire idéal en 4 étapes simples
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center group">
              <div class="relative mb-6">
                <div class="w-20 h-20 bg-primary/20 text-primary rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-primary text-white rounded-full flex items-center justify-center text-sm font-bold">
                  1
                </div>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Recherchez</h3>
              <p class="text-gray-600">
                Utilisez notre moteur de recherche pour trouver le service dont vous avez besoin près de chez vous.
              </p>
            </div>

            <div class="text-center group">
              <div class="relative mb-6">
                <div class="w-20 h-20 bg-secondary/20 text-secondary rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-secondary text-white rounded-full flex items-center justify-center text-sm font-bold">
                  2
                </div>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Comparez</h3>
              <p class="text-gray-600">
                Consultez les profils, avis et tarifs des prestataires pour faire le bon choix.
              </p>
            </div>

            <div class="text-center group">
              <div class="relative mb-6">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                  3
                </div>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Réservez</h3>
              <p class="text-gray-600">
                Prenez rendez-vous directement en ligne en choisissant votre créneau préféré.
              </p>
            </div>

            <div class="text-center group">
              <div class="relative mb-6">
                <div class="w-20 h-20 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                  <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>
                <div class="absolute -top-2 -right-2 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                  4
                </div>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-3">Profitez</h3>
              <p class="text-gray-600">
                Votre prestataire intervient au moment convenu. Évaluez ensuite la prestation.
              </p>
            </div>
          </div>

          <div class="text-center mt-12">
            <Link
              :href="route('services.index')"
              class="inline-flex items-center px-8 py-4 bg-primary text-white rounded-xl font-semibold text-lg hover:bg-primary/90 transition-colors shadow-lg hover:shadow-xl"
            >
              Commencer ma recherche
              <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </Link>
          </div>
        </div>
      </section>

      <!-- Pour les prestataires -->
      <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Pour les <span class="text-secondary">prestataires</span>
            </h2>
            <p class="text-xl text-gray-600">
              Développez votre activité grâce à notre plateforme
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
              <div class="w-16 h-16 bg-primary/20 text-primary rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Inscription</h3>
              <p class="text-gray-600">
                Créez votre profil professionnel et téléchargez vos documents de validation.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
              <div class="w-16 h-16 bg-secondary/20 text-secondary rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Validation</h3>
              <p class="text-gray-600">
                Notre équipe vérifie vos qualifications et valide votre profil professionnel.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
              <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Configuration</h3>
              <p class="text-gray-600">
                Ajoutez vos services, définissez vos tarifs et votre zone d'intervention.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 text-center">
              <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
              </div>
              <h3 class="text-xl font-semibold text-gray-900 mb-4">Développement</h3>
              <p class="text-gray-600">
                Recevez des demandes de clients et développez votre chiffre d'affaires.
              </p>
            </div>
          </div>

          <div class="text-center mt-12">
            <Link
              :href="route('provider.registration.step1')"
              class="inline-flex items-center px-8 py-4 bg-secondary text-white rounded-xl font-semibold text-lg hover:bg-secondary/90 transition-colors shadow-lg hover:shadow-xl"
            >
              Devenir prestataire
              <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </Link>
          </div>
        </div>
      </section>

      <!-- Avantages -->
      <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Pourquoi choisir ServicesPro ?
            </h2>
            <p class="text-xl text-gray-600">
              Les avantages qui font la différence
            </p>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8">
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-primary/20 text-primary rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900 mb-2">Prestataires vérifiés</h3>
                  <p class="text-gray-600">
                    Tous nos prestataires sont soigneusement sélectionnés et leurs qualifications vérifiées.
                  </p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-secondary/20 text-secondary rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900 mb-2">Tarifs transparents</h3>
                  <p class="text-gray-600">
                    Consultez les tarifs avant de réserver. Pas de surprise, pas de frais cachés.
                  </p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z" />
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900 mb-2">Support client 24/7</h3>
                  <p class="text-gray-600">
                    Notre équipe est disponible pour vous accompagner à chaque étape.
                  </p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                  <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                  </div>
                </div>
                <div>
                  <h3 class="text-xl font-semibold text-gray-900 mb-2">Garantie satisfaction</h3>
                  <p class="text-gray-600">
                    Si vous n'êtes pas satisfait, nous trouvons une solution ou remboursons.
                  </p>
                </div>
              </div>
            </div>

            <div class="relative">
              <div class="bg-gradient-to-br from-primary/20 to-secondary/20 rounded-3xl p-8 transform -rotate-3 hover:rotate-0 transition-transform duration-500">
                <div class="bg-white rounded-2xl p-8 shadow-2xl">
                  <div class="text-center space-y-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-primary/20 text-primary rounded-full">
                      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                      </svg>
                    </div>
                    
                    <div>
                      <h3 class="text-2xl font-bold text-gray-900 mb-2">Plus de 50 000</h3>
                      <p class="text-gray-600">services réalisés avec succès</p>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-6">
                      <div class="flex justify-center items-center mb-2">
                        <div class="flex text-yellow-400">
                          <svg v-for="i in 5" :key="i" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                          </svg>
                        </div>
                        <span class="ml-2 text-lg font-semibold text-gray-900">4.8/5</span>
                      </div>
                      <p class="text-sm text-gray-600">Note moyenne de satisfaction</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- FAQ -->
      <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Questions fréquentes
            </h2>
            <p class="text-xl text-gray-600">
              Toutes les réponses à vos questions
            </p>
          </div>

          <div class="space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-md">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Comment sont sélectionnés les prestataires ?
              </h3>
              <p class="text-gray-600">
                Chaque prestataire doit passer par un processus de validation rigoureux : vérification des qualifications, 
                contrôle des assurances, validation des références et test de qualité de service.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-md">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Que se passe-t-il si je ne suis pas satisfait ?
              </h3>
              <p class="text-gray-600">
                Notre garantie satisfaction vous protège. En cas de problème, contactez notre support sous 48h. 
                Nous médions avec le prestataire ou vous remboursons si nécessaire.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-md">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Les prestataires sont-ils assurés ?
              </h3>
              <p class="text-gray-600">
                Absolument ! Tous nos prestataires disposent d'une assurance responsabilité civile professionnelle 
                valide que nous vérifions régulièrement.
              </p>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-md">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Puis-je annuler ou modifier ma réservation ?
              </h3>
              <p class="text-gray-600">
                Oui, vous pouvez annuler ou modifier gratuitement jusqu'à 24h avant l'intervention. 
                Pour les annulations tardives, des frais peuvent s'appliquer selon les conditions du prestataire.
              </p>
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
            Rejoignez des milliers d'utilisateurs qui nous font déjà confiance
          </p>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              :href="route('services.index')"
              class="bg-white text-primary px-8 py-4 rounded-xl font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg hover:shadow-xl"
            >
              Je cherche un service
            </Link>
            
            <Link
              :href="route('provider.registration.step1')"
              class="bg-secondary text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-secondary/90 transition-colors shadow-lg hover:shadow-xl border-2 border-white/20"
            >
              Je propose mes services
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