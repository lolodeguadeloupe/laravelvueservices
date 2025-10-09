<template>
  <AppLayout>
    <div class="min-h-screen bg-white">
      <!-- Hero Section -->
      <section class="bg-gradient-to-br from-primary/5 via-white to-secondary/5 py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
              Conditions Générales d'Utilisation
            </h1>
            <p class="text-xl text-gray-600">
              Dernière mise à jour : {{ lastUpdate }}
            </p>
          </div>
        </div>
      </section>

      <!-- Sommaire -->
      <section class="py-12 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="bg-white rounded-2xl p-8 shadow-lg">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Sommaire</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div class="space-y-2">
                <a href="#article1" class="block text-primary hover:underline">1. Objet</a>
                <a href="#article2" class="block text-primary hover:underline">2. Définitions</a>
                <a href="#article3" class="block text-primary hover:underline">3. Inscription</a>
                <a href="#article4" class="block text-primary hover:underline">4. Services</a>
                <a href="#article5" class="block text-primary hover:underline">5. Réservations</a>
              </div>
              <div class="space-y-2">
                <a href="#article6" class="block text-primary hover:underline">6. Paiements</a>
                <a href="#article7" class="block text-primary hover:underline">7. Responsabilités</a>
                <a href="#article8" class="block text-primary hover:underline">8. Données personnelles</a>
                <a href="#article9" class="block text-primary hover:underline">9. Résiliation</a>
                <a href="#article10" class="block text-primary hover:underline">10. Droit applicable</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Contenu des CGU -->
      <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 prose prose-lg max-w-none">
          
          <article id="article1" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 1 - Objet</h2>
            <p class="text-gray-700 mb-4">
              Les présentes Conditions Générales d'Utilisation (« CGU ») régissent l'utilisation de la plateforme ServicesPro, 
              accessible à l'adresse <strong>servicespro.fr</strong>, éditée par la société ServicesPro SAS.
            </p>
            <p class="text-gray-700 mb-4">
              ServicesPro est une plateforme de mise en relation entre des clients particuliers et des prestataires de services 
              professionnels dans les domaines de l'entretien, du jardinage, du bricolage, du bien-être et autres services à domicile.
            </p>
            <p class="text-gray-700">
              L'utilisation de la plateforme implique l'acceptation pleine et entière des présentes CGU par l'utilisateur.
            </p>
          </article>

          <article id="article2" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 2 - Définitions</h2>
            <div class="space-y-4">
              <div>
                <strong class="text-gray-900">Plateforme :</strong>
                <span class="text-gray-700"> Le site internet servicespro.fr et son application mobile.</span>
              </div>
              <div>
                <strong class="text-gray-900">Client :</strong>
                <span class="text-gray-700"> Personne physique utilisant la plateforme pour rechercher et réserver des services.</span>
              </div>
              <div>
                <strong class="text-gray-900">Prestataire :</strong>
                <span class="text-gray-700"> Professionnel inscrit sur la plateforme proposant ses services.</span>
              </div>
              <div>
                <strong class="text-gray-900">Service :</strong>
                <span class="text-gray-700"> Prestation proposée par un prestataire via la plateforme.</span>
              </div>
              <div>
                <strong class="text-gray-900">Réservation :</strong>
                <span class="text-gray-700"> Demande confirmée d'un client pour un service spécifique.</span>
              </div>
            </div>
          </article>

          <article id="article3" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 3 - Inscription et comptes utilisateurs</h2>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">3.1 Conditions d'inscription</h3>
            <p class="text-gray-700 mb-4">
              L'inscription est gratuite et ouverte à toute personne physique majeure et capable juridiquement. 
              Les prestataires doivent en outre justifier de leur statut professionnel et de leurs assurances.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">3.2 Vérification des prestataires</h3>
            <p class="text-gray-700 mb-4">
              ServicesPro vérifie l'identité, les qualifications et les assurances des prestataires avant validation de leur profil. 
              Cette vérification peut prendre 48 à 72 heures.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">3.3 Responsabilité du compte</h3>
            <p class="text-gray-700">
              Chaque utilisateur est responsable de la confidentialité de ses identifiants et de toutes les activités 
              effectuées sous son compte. Toute utilisation frauduleuse doit être signalée immédiatement.
            </p>
          </article>

          <article id="article4" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 4 - Services proposés</h2>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">4.1 Nature des services</h3>
            <p class="text-gray-700 mb-4">
              ServicesPro propose exclusivement un service de mise en relation. La plateforme n'est pas prestataire 
              des services réservés et n'intervient pas dans leur exécution.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">4.2 Disponibilité</h3>
            <p class="text-gray-700 mb-4">
              ServicesPro s'efforce d'assurer une disponibilité de la plateforme 24h/24 et 7j/7, mais ne peut garantir 
              une disponibilité absolue en cas de maintenance ou de problème technique.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">4.3 Évolution des services</h3>
            <p class="text-gray-700">
              ServicesPro se réserve le droit de faire évoluer, modifier ou suspendre ses services à tout moment, 
              avec un préavis raisonnable sauf en cas d'urgence technique ou légale.
            </p>
          </article>

          <article id="article5" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 5 - Réservations et prestations</h2>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">5.1 Processus de réservation</h3>
            <p class="text-gray-700 mb-4">
              Le client sélectionne un service, choisit un prestataire et confirme sa réservation. 
              Le prestataire dispose de 24h pour accepter ou refuser la demande.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">5.2 Annulation</h3>
            <p class="text-gray-700 mb-4">
              Les réservations peuvent être annulées gratuitement jusqu'à 24h avant l'intervention. 
              Des frais d'annulation peuvent s'appliquer pour les annulations tardives.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">5.3 Exécution des prestations</h3>
            <p class="text-gray-700">
              Les prestations sont exécutées directement entre le client et le prestataire. 
              ServicesPro n'intervient pas dans leur réalisation mais peut médier en cas de litige.
            </p>
          </article>

          <article id="article6" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 6 - Conditions financières</h2>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">6.1 Tarification</h3>
            <p class="text-gray-700 mb-4">
              Les tarifs des services sont fixés librement par chaque prestataire et affichés sur leur profil. 
              ServicesPro perçoit une commission de 15% sur chaque prestation réalisée.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">6.2 Paiement</h3>
            <p class="text-gray-700 mb-4">
              Le paiement s'effectue en ligne au moment de la réservation. Les moyens de paiement acceptés sont : 
              carte bancaire, PayPal et virement bancaire.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">6.3 Remboursement</h3>
            <p class="text-gray-700">
              En cas d'annulation dans les conditions prévues ou de problème de qualité avéré, 
              ServicesPro procède au remboursement sous 5 à 10 jours ouvrés.
            </p>
          </article>

          <article id="article7" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 7 - Responsabilités</h2>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">7.1 Responsabilité de ServicesPro</h3>
            <p class="text-gray-700 mb-4">
              ServicesPro assure un service de mise en relation et met en œuvre tous les moyens pour vérifier 
              les prestataires. Sa responsabilité ne peut être engagée pour les dommages résultant de l'exécution des prestations.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">7.2 Responsabilité des prestataires</h3>
            <p class="text-gray-700 mb-4">
              Les prestataires sont seuls responsables de l'exécution de leurs prestations et doivent disposer 
              des assurances professionnelles requises.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">7.3 Responsabilité des clients</h3>
            <p class="text-gray-700">
              Les clients s'engagent à faciliter l'intervention du prestataire et à signaler tout problème 
              dans les délais raisonnables.
            </p>
          </article>

          <article id="article8" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 8 - Protection des données personnelles</h2>
            <p class="text-gray-700 mb-4">
              ServicesPro s'engage à protéger les données personnelles de ses utilisateurs conformément au RGPD 
              et à la loi Informatique et Libertés.
            </p>
            <p class="text-gray-700 mb-4">
              Les données collectées sont nécessaires au fonctionnement du service et ne sont pas cédées à des tiers 
              sans consentement, sauf obligation légale.
            </p>
            <p class="text-gray-700">
              Pour plus d'informations, consultez notre <Link :href="route('privacy')" class="text-primary hover:underline">Politique de Confidentialité</Link>.
            </p>
          </article>

          <article id="article9" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 9 - Résiliation</h2>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">9.1 Résiliation par l'utilisateur</h3>
            <p class="text-gray-700 mb-4">
              Tout utilisateur peut supprimer son compte à tout moment depuis son espace personnel 
              ou en contactant le support client.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">9.2 Résiliation par ServicesPro</h3>
            <p class="text-gray-700">
              ServicesPro se réserve le droit de suspendre ou supprimer un compte en cas de non-respect 
              des présentes CGU, avec un préavis de 15 jours sauf en cas de faute grave.
            </p>
          </article>

          <article id="article10" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Article 10 - Dispositions générales</h2>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">10.1 Droit applicable</h3>
            <p class="text-gray-700 mb-4">
              Les présentes CGU sont régies par le droit français. Tout litige relève de la compétence 
              des tribunaux français.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">10.2 Médiation</h3>
            <p class="text-gray-700 mb-4">
              En cas de litige, une solution amiable sera recherchée avant toute action judiciaire. 
              Le consommateur peut recourir à la médiation de la consommation.
            </p>
            
            <h3 class="text-xl font-semibold text-gray-900 mb-3">10.3 Modification des CGU</h3>
            <p class="text-gray-700">
              ServicesPro se réserve le droit de modifier les présentes CGU. Les utilisateurs seront informés 
              par email et devront accepter les nouvelles conditions pour continuer à utiliser le service.
            </p>
          </article>

        </div>
      </section>

      <!-- Contact -->
      <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Une question sur nos CGU ?</h2>
          <p class="text-gray-600 mb-8">Notre équipe juridique est à votre disposition</p>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              :href="route('contact')"
              class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-xl font-semibold hover:bg-primary/90 transition-colors"
            >
              Nous contacter
            </Link>
            
            <Link
              :href="route('help')"
              class="inline-flex items-center px-6 py-3 bg-white text-primary border-2 border-primary rounded-xl font-semibold hover:bg-primary hover:text-white transition-colors"
            >
              Centre d'aide
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

const lastUpdate = '7 octobre 2025'
</script>

