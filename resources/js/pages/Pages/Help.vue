<template>
  <AppLayout>
    <div class="min-h-screen bg-white">
      <!-- Hero Section -->
      <section class="bg-gradient-to-br from-primary/5 via-white to-secondary/5 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
              Centre d'aide <span class="text-primary">ServicesPro</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
              Trouvez rapidement les réponses à vos questions ou contactez notre équipe support
            </p>
            
            <!-- Barre de recherche -->
            <div class="max-w-2xl mx-auto">
              <div class="relative">
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Rechercher dans l'aide..."
                  class="w-full px-6 py-4 text-lg border border-gray-300 rounded-2xl focus:ring-2 focus:ring-primary focus:border-primary pl-14"
                />
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Liens rapides -->
      <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
              Liens rapides
            </h2>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <button
              @click="scrollToSection('clients')"
              class="p-6 bg-primary/5 rounded-2xl hover:bg-primary/10 transition-colors group"
            >
              <div class="w-12 h-12 bg-primary/20 text-primary rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Aide clients</h3>
              <p class="text-sm text-gray-600">Réservations, paiements, réclamations</p>
            </button>

            <button
              @click="scrollToSection('providers')"
              class="p-6 bg-secondary/5 rounded-2xl hover:bg-secondary/10 transition-colors group"
            >
              <div class="w-12 h-12 bg-secondary/20 text-secondary rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2V6" />
                </svg>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Aide prestataires</h3>
              <p class="text-sm text-gray-600">Inscription, services, commissions</p>
            </button>

            <button
              @click="scrollToSection('payments')"
              class="p-6 bg-green-50 rounded-2xl hover:bg-green-100 transition-colors group"
            >
              <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Paiements</h3>
              <p class="text-sm text-gray-600">Modes de paiement, remboursements</p>
            </button>

            <button
              @click="scrollToSection('technical')"
              class="p-6 bg-blue-50 rounded-2xl hover:bg-blue-100 transition-colors group"
            >
              <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h3 class="font-semibold text-gray-900 mb-2">Support technique</h3>
              <p class="text-sm text-gray-600">Problèmes de connexion, bugs</p>
            </button>
          </div>
        </div>
      </section>

      <!-- FAQ Clients -->
      <section id="clients" class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
              Questions fréquentes - <span class="text-primary">Clients</span>
            </h2>
          </div>

          <div class="space-y-4">
            <FAQItem
              question="Comment réserver un service ?"
              answer="Recherchez le service souhaité, sélectionnez un prestataire, choisissez votre créneau et confirmez votre réservation. Vous recevrez une confirmation par email."
            />
            
            <FAQItem
              question="Puis-je annuler ma réservation ?"
              answer="Oui, vous pouvez annuler gratuitement jusqu'à 24h avant l'intervention. Pour les annulations tardives, des frais peuvent s'appliquer selon les conditions du prestataire."
            />
            
            <FAQItem
              question="Comment payer un service ?"
              answer="Nous acceptons les cartes bancaires, PayPal et les virements. Le paiement est sécurisé et effectué directement sur la plateforme."
            />
            
            <FAQItem
              question="Que faire si je ne suis pas satisfait ?"
              answer="Contactez notre support sous 48h après la prestation. Nous médions avec le prestataire ou vous remboursons si nécessaire, conformément à notre garantie satisfaction."
            />
            
            <FAQItem
              question="Les prestataires sont-ils assurés ?"
              answer="Absolument ! Tous nos prestataires disposent d'une assurance responsabilité civile professionnelle que nous vérifions régulièrement."
            />
          </div>
        </div>
      </section>

      <!-- FAQ Prestataires -->
      <section id="providers" class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
              Questions fréquentes - <span class="text-secondary">Prestataires</span>
            </h2>
          </div>

          <div class="space-y-4">
            <FAQItem
              question="Comment devenir prestataire ?"
              answer="Inscrivez-vous en 3 étapes : créez votre profil, téléchargez vos documents (identité, assurance, certifications) et attendez la validation de notre équipe sous 48-72h."
            />
            
            <FAQItem
              question="Quels sont les frais de commission ?"
              answer="Notre commission est de 15% du montant HT de chaque prestation. Elle couvre l'utilisation de la plateforme, le support client et la promotion de vos services."
            />
            
            <FAQItem
              question="Comment recevoir mes paiements ?"
              answer="Les paiements sont versés chaque semaine sur votre compte bancaire, 7 jours après la réalisation de la prestation pour couvrir la période de réclamation."
            />
            
            <FAQItem
              question="Puis-je refuser une demande ?"
              answer="Oui, vous êtes libre d'accepter ou refuser toute demande. Cependant, un taux d'acceptation élevé améliore votre classement dans les résultats de recherche."
            />
            
            <FAQItem
              question="Comment gérer mon planning ?"
              answer="Votre dashboard prestataire vous permet de gérer vos créneaux, accepter/refuser les demandes et suivre vos interventions en cours."
            />
          </div>
        </div>
      </section>

      <!-- FAQ Paiements -->
      <section id="payments" class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
              Questions fréquentes - <span class="text-green-600">Paiements</span>
            </h2>
          </div>

          <div class="space-y-4">
            <FAQItem
              question="Quand suis-je débité ?"
              answer="Vous êtes débité au moment de la confirmation de réservation. En cas d'annulation dans les délais, vous êtes automatiquement remboursé."
            />
            
            <FAQItem
              question="Mes données bancaires sont-elles sécurisées ?"
              answer="Oui, nous utilisons le cryptage SSL et nos partenaires de paiement (Stripe, PayPal) sont certifiés PCI-DSS pour une sécurité maximale."
            />
            
            <FAQItem
              question="Comment obtenir un remboursement ?"
              answer="En cas de problème, contactez notre support. Selon la situation, le remboursement est traité sous 5-10 jours ouvrés sur votre moyen de paiement initial."
            />
            
            <FAQItem
              question="Puis-je modifier ma facture ?"
              answer="Les factures sont automatiquement générées. Pour toute modification, contactez notre support avec les détails nécessaires."
            />
          </div>
        </div>
      </section>

      <!-- Support technique -->
      <section id="technical" class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
              Support technique - <span class="text-blue-600">Aide technique</span>
            </h2>
          </div>

          <div class="space-y-4">
            <FAQItem
              question="J'ai des problèmes de connexion"
              answer="Vérifiez votre connexion internet, videz le cache de votre navigateur (Ctrl+F5) ou essayez un autre navigateur. Si le problème persiste, contactez notre support."
            />
            
            <FAQItem
              question="L'application mobile ne fonctionne pas"
              answer="Assurez-vous d'avoir la dernière version depuis votre store. Redémarrez l'app ou votre téléphone. Contactez-nous si les problèmes continuent."
            />
            
            <FAQItem
              question="Je ne reçois pas les emails"
              answer="Vérifiez vos spams/courriers indésirables et ajoutez notre adresse à vos contacts. Vérifiez aussi que votre adresse email est correcte dans votre profil."
            />
            
            <FAQItem
              question="Comment signaler un bug ?"
              answer="Contactez notre support en décrivant le problème, votre navigateur/appareil et les étapes pour reproduire le bug. Des captures d'écran sont très utiles."
            />
          </div>
        </div>
      </section>

      <!-- Contact support -->
      <section class="py-20 bg-gradient-to-r from-primary to-primary-dark text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
          <h2 class="text-3xl md:text-4xl font-bold mb-6">
            Vous ne trouvez pas votre réponse ?
          </h2>
          <p class="text-xl mb-8 opacity-90">
            Notre équipe support est là pour vous aider
          </p>
          
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <!-- Email -->
            <div class="text-center">
              <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Email</h3>
              <p class="opacity-90 mb-2">support@servicespro.fr</p>
              <p class="text-sm opacity-75">Réponse sous 24h</p>
            </div>

            <!-- Téléphone -->
            <div class="text-center">
              <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Téléphone</h3>
              <p class="opacity-90 mb-2">01 23 45 67 89</p>
              <p class="text-sm opacity-75">Lun-Ven 9h-18h</p>
            </div>

            <!-- Chat -->
            <div class="text-center">
              <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
              </div>
              <h3 class="text-lg font-semibold mb-2">Chat en direct</h3>
              <p class="opacity-90 mb-2">Disponible 24/7</p>
              <p class="text-sm opacity-75">Réponse immédiate</p>
            </div>
          </div>

          <Link
            :href="route('contact')"
            class="inline-flex items-center px-8 py-4 bg-white text-primary rounded-xl font-semibold text-lg hover:bg-gray-100 transition-colors shadow-lg hover:shadow-xl"
          >
            Contactez notre support
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </Link>
        </div>
      </section>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import { route } from '@/utils/routes'

const searchQuery = ref('')

// Composant FAQ Item
import { defineComponent } from 'vue'

const FAQItem = defineComponent({
  props: {
    question: String,
    answer: String
  },
  template: `
    <div class="bg-white rounded-2xl p-6 shadow-md hover:shadow-lg transition-all duration-300">
      <button
        @click="isOpen = !isOpen"
        class="w-full text-left flex justify-between items-center"
      >
        <h3 class="text-lg font-semibold text-gray-900 pr-4">{{ question }}</h3>
        <svg
          class="w-5 h-5 text-gray-500 transform transition-transform"
          :class="{ 'rotate-180': isOpen }"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
      <div
        v-show="isOpen"
        class="mt-4 pt-4 border-t border-gray-200 text-gray-600"
      >
        {{ answer }}
      </div>
    </div>
  `,
  data() {
    return {
      isOpen: false
    }
  }
})

const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' })
  }
}
</script>