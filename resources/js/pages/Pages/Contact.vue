<template>
  <AppLayout>
    <div class="min-h-screen bg-white">
      <!-- Hero Section -->
      <section class="bg-gradient-to-br from-primary/5 via-white to-secondary/5 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
              Contactez-nous
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
              Notre équipe est à votre écoute pour répondre à toutes vos questions 
              et vous accompagner dans votre expérience ServicesPro.
            </p>
          </div>
        </div>
      </section>

      <!-- Contact Content -->
      <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Informations de contact -->
            <div>
              <h2 class="text-3xl font-bold text-gray-900 mb-8">
                Restons en contact
              </h2>
              
              <div class="space-y-8">
                <!-- Support client -->
                <div class="flex items-start">
                  <div class="flex-shrink-0 w-12 h-12 bg-primary/20 text-primary rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Support Client</h3>
                    <p class="text-gray-600 mb-2">
                      Pour toute question concernant vos réservations ou notre service
                    </p>
                    <div class="space-y-1">
                      <p class="text-primary font-medium">support@servicespro.com</p>
                      <p class="text-gray-500 text-sm">Réponse sous 2h en moyenne</p>
                    </div>
                  </div>
                </div>

                <!-- Prestataires -->
                <div class="flex items-start">
                  <div class="flex-shrink-0 w-12 h-12 bg-secondary/20 text-secondary rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2h8z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Espace Prestataires</h3>
                    <p class="text-gray-600 mb-2">
                      Questions sur votre inscription, vos services ou vos paiements
                    </p>
                    <div class="space-y-1">
                      <p class="text-primary font-medium">prestataires@servicespro.com</p>
                      <p class="text-gray-500 text-sm">Équipe dédiée disponible 9h-18h</p>
                    </div>
                  </div>
                </div>

                <!-- Partenariats -->
                <div class="flex items-start">
                  <div class="flex-shrink-0 w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Partenariats</h3>
                    <p class="text-gray-600 mb-2">
                      Intégrations, API, partenariats commerciaux
                    </p>
                    <div class="space-y-1">
                      <p class="text-primary font-medium">partenaires@servicespro.com</p>
                      <p class="text-gray-500 text-sm">Équipe business development</p>
                    </div>
                  </div>
                </div>

                <!-- Presse -->
                <div class="flex items-start">
                  <div class="flex-shrink-0 w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Relations Presse</h3>
                    <p class="text-gray-600 mb-2">
                      Demandes média, interviews, communiqués
                    </p>
                    <div class="space-y-1">
                      <p class="text-primary font-medium">presse@servicespro.com</p>
                      <p class="text-gray-500 text-sm">Kit presse disponible sur demande</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Infos pratiques -->
              <div class="mt-12 p-6 bg-gray-50 rounded-2xl">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informations pratiques</h3>
                <div class="space-y-3 text-sm">
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-gray-700">Support disponible 7j/7 de 8h à 20h</span>
                  </div>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-gray-700">Réponse par email sous 2h en moyenne</span>
                  </div>
                  <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    <span class="text-gray-700">Siège social : Paris, France</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Formulaire de contact -->
            <div class="bg-white border border-gray-200 rounded-2xl p-8 shadow-lg">
              <h2 class="text-2xl font-bold text-gray-900 mb-6">
                Envoyez-nous un message
              </h2>
              
              <form class="space-y-6">
                <!-- Type de demande -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Type de demande
                  </label>
                  <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <option value="support">Support client</option>
                    <option value="provider">Question prestataire</option>
                    <option value="partnership">Partenariat</option>
                    <option value="press">Presse</option>
                    <option value="other">Autre</option>
                  </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Prénom -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Prénom *
                    </label>
                    <input
                      type="text"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                      placeholder="Votre prénom"
                    />
                  </div>

                  <!-- Nom -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Nom *
                    </label>
                    <input
                      type="text"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                      placeholder="Votre nom"
                    />
                  </div>
                </div>

                <!-- Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Adresse email *
                  </label>
                  <input
                    type="email"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="votre@email.com"
                  />
                </div>

                <!-- Sujet -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Sujet *
                  </label>
                  <input
                    type="text"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Objet de votre message"
                  />
                </div>

                <!-- Message -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Message *
                  </label>
                  <textarea
                    rows="5"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-none"
                    placeholder="Décrivez votre demande en détail..."
                  ></textarea>
                </div>

                <!-- Consentement -->
                <div class="flex items-start">
                  <input
                    type="checkbox"
                    required
                    class="mt-1 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
                  />
                  <label class="ml-3 text-sm text-gray-600">
                    J'accepte que mes données personnelles soient utilisées pour traiter ma demande. 
                    <Link :href="route('privacy')" class="text-primary hover:underline">
                      En savoir plus
                    </Link>
                  </label>
                </div>

                <!-- Bouton d'envoi -->
                <button
                  type="submit"
                  class="w-full py-4 px-6 rounded-lg font-semibold transition-colors shadow-lg bg-primary text-white hover:bg-primary-dark hover:shadow-xl"
                >
                  Envoyer le message
                </button>
              </form>
            </div>
          </div>
        </div>
      </section>

      <!-- FAQ Section -->
      <section class="py-20 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Questions fréquentes
            </h2>
            <p class="text-xl text-gray-600">
              Les réponses aux questions les plus courantes
            </p>
          </div>

          <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Comment puis-je annuler une réservation ?
              </h3>
              <p class="text-gray-600">
                Vous pouvez annuler gratuitement votre réservation jusqu'à ce que le prestataire 
                l'accepte. Une fois acceptée, les conditions d'annulation dépendent du service et 
                du délai. Connectez-vous à votre compte pour gérer vos réservations.
              </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Comment devenir prestataire sur ServicesPro ?
              </h3>
              <p class="text-gray-600">
                L'inscription se fait en 3 étapes simples : création de compte, validation de vos 
                informations et documents, puis activation de votre profil. 
                <Link :href="route('provider.registration.step1')" class="text-primary hover:underline">
                  Commencez votre inscription ici
                </Link>.
              </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Les prestataires sont-ils vérifiés ?
              </h3>
              <p class="text-gray-600">
                Oui, tous nos prestataires passent par un processus de vérification comprenant 
                la validation de leur identité, de leurs qualifications et de leurs assurances 
                professionnelles.
              </p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">
                Que faire en cas de problème avec un service ?
              </h3>
              <p class="text-gray-600">
                Contactez immédiatement notre support client à support@servicespro.com. 
                Nous intervenons rapidement pour résoudre tout litige et garantir votre satisfaction.
              </p>
            </div>
          </div>

          <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">
              Vous ne trouvez pas la réponse à votre question ?
            </p>
            <Link
              :href="route('help')"
              class="inline-flex items-center bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary-dark transition-colors"
            >
              Consulter le centre d'aide
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </Link>
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