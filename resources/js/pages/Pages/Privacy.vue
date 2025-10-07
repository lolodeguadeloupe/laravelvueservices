<template>
  <AppLayout>
    <div class="min-h-screen bg-white">
      <!-- Hero Section -->
      <section class="bg-gradient-to-br from-primary/5 via-white to-secondary/5 py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
              Politique de Confidentialité
            </h1>
            <p class="text-xl text-gray-600">
              Dernière mise à jour : {{ lastUpdate }}
            </p>
          </div>
        </div>
      </section>

      <!-- Résumé -->
      <section class="py-12 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="bg-primary/5 rounded-2xl p-8 mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">En résumé</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="text-center">
                <div class="w-16 h-16 bg-primary/20 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                  </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Données protégées</h3>
                <p class="text-sm text-gray-600">Vos données sont sécurisées et ne sont jamais vendues à des tiers</p>
              </div>
              
              <div class="text-center">
                <div class="w-16 h-16 bg-secondary/20 text-secondary rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2v2a2 2 0 01-2 2m-2-2a2 2 0 00-2 2v2a2 2 0 002 2m2-2a2 2 0 002 2M9 5a2 2 0 012-2v0a2 2 0 012 2v0M9 5a2 2 0 012-2v0M9 5v4a2 2 0 002 2h2" />
                  </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Contrôle total</h3>
                <p class="text-sm text-gray-600">Vous pouvez consulter, modifier ou supprimer vos données à tout moment</p>
              </div>
              
              <div class="text-center">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">RGPD conforme</h3>
                <p class="text-sm text-gray-600">Nous respectons le Règlement Général sur la Protection des Données</p>
              </div>
            </div>
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
                <a href="#section1" class="block text-primary hover:underline">1. Données collectées</a>
                <a href="#section2" class="block text-primary hover:underline">2. Finalités du traitement</a>
                <a href="#section3" class="block text-primary hover:underline">3. Base légale</a>
                <a href="#section4" class="block text-primary hover:underline">4. Conservation des données</a>
                <a href="#section5" class="block text-primary hover:underline">5. Sécurité</a>
              </div>
              <div class="space-y-2">
                <a href="#section6" class="block text-primary hover:underline">6. Partage des données</a>
                <a href="#section7" class="block text-primary hover:underline">7. Vos droits</a>
                <a href="#section8" class="block text-primary hover:underline">8. Cookies</a>
                <a href="#section9" class="block text-primary hover:underline">9. Contact DPO</a>
                <a href="#section10" class="block text-primary hover:underline">10. Modifications</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Contenu -->
      <section class="py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          
          <article id="section1" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">1. Quelles données nous collectons</h2>
            
            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">📋 Données d'inscription</h3>
              <ul class="space-y-2 text-gray-700">
                <li>• Nom et prénom</li>
                <li>• Adresse email</li>
                <li>• Numéro de téléphone</li>
                <li>• Adresse postale</li>
                <li>• Date de naissance</li>
              </ul>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">💼 Données professionnelles (prestataires)</h3>
              <ul class="space-y-2 text-gray-700">
                <li>• Statut juridique (auto-entrepreneur, société...)</li>
                <li>• Numéro SIRET</li>
                <li>• Qualifications et certifications</li>
                <li>• Assurances professionnelles</li>
                <li>• Documents d'identité (pour vérification)</li>
              </ul>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">🔍 Données d'utilisation</h3>
              <ul class="space-y-2 text-gray-700">
                <li>• Historique des réservations</li>
                <li>• Préférences de services</li>
                <li>• Avis et commentaires</li>
                <li>• Messages échangés via la plateforme</li>
                <li>• Données de géolocalisation (avec consentement)</li>
              </ul>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">💳 Données de paiement</h3>
              <p class="text-gray-700 mb-3">
                Les données bancaires sont traitées par nos partenaires de paiement certifiés PCI-DSS :
              </p>
              <ul class="space-y-2 text-gray-700">
                <li>• Stripe (pour les cartes bancaires)</li>
                <li>• PayPal (pour les comptes PayPal)</li>
              </ul>
              <p class="text-sm text-gray-600 mt-3">
                ServicesPro ne stocke jamais vos données bancaires complètes.
              </p>
            </div>
          </article>

          <article id="section2" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">2. Pourquoi nous utilisons vos données</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-primary/5 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">🎯 Services principaux</h3>
                <ul class="space-y-2 text-gray-700">
                  <li>• Création et gestion de votre compte</li>
                  <li>• Mise en relation avec des prestataires</li>
                  <li>• Traitement des réservations</li>
                  <li>• Support client</li>
                  <li>• Facturation et paiements</li>
                </ul>
              </div>

              <div class="bg-secondary/5 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">📈 Amélioration du service</h3>
                <ul class="space-y-2 text-gray-700">
                  <li>• Personnalisation des recommandations</li>
                  <li>• Analyse de l'utilisation</li>
                  <li>• Prévention de la fraude</li>
                  <li>• Développement de nouvelles fonctionnalités</li>
                  <li>• Études statistiques anonymisées</li>
                </ul>
              </div>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">📧 Communications (avec votre consentement)</h3>
              <ul class="space-y-2 text-gray-700">
                <li>• Newsletters et actualités ServicesPro</li>
                <li>• Offres promotionnelles personnalisées</li>
                <li>• Enquêtes de satisfaction</li>
              </ul>
              <p class="text-sm text-gray-600 mt-3">
                Vous pouvez vous désabonner à tout moment via le lien dans nos emails.
              </p>
            </div>
          </article>

          <article id="section3" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">3. Base légale du traitement</h2>
            
            <div class="space-y-6">
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-8 h-8 bg-primary/20 text-primary rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-2">Exécution du contrat</h3>
                  <p class="text-gray-700">
                    Le traitement de vos données personnelles, d'utilisation et de paiement est nécessaire 
                    pour vous fournir nos services de mise en relation.
                  </p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-8 h-8 bg-secondary/20 text-secondary rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-2">Intérêt légitime</h3>
                  <p class="text-gray-700">
                    L'amélioration de nos services, la prévention de la fraude et l'analyse de l'utilisation 
                    constituent un intérêt légitime pour ServicesPro et ses utilisateurs.
                  </p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-2">Consentement</h3>
                  <p class="text-gray-700">
                    Les communications marketing, la géolocalisation et certains cookies nécessitent 
                    votre consentement explicite, que vous pouvez retirer à tout moment.
                  </p>
                </div>
              </div>

              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div>
                  <h3 class="font-semibold text-gray-900 mb-2">Obligation légale</h3>
                  <p class="text-gray-700">
                    La vérification de l'identité des prestataires et la conservation de certaines données 
                    sont requises par la réglementation française.
                  </p>
                </div>
              </div>
            </div>
          </article>

          <article id="section4" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">4. Combien de temps nous conservons vos données</h2>
            
            <div class="overflow-x-auto">
              <table class="min-w-full bg-white border border-gray-300 rounded-xl">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type de données</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée de conservation</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Données de compte actif</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">Tant que le compte est actif</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Données de compte supprimé</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">30 jours puis suppression</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Historique des transactions</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">10 ans (obligation comptable)</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Messages support</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">3 ans</td>
                  </tr>
                  <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Données de navigation</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">13 mois maximum</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </article>

          <article id="section5" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">5. Comment nous protégeons vos données</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">🔐 Sécurité technique</h3>
                <ul class="space-y-2 text-gray-700">
                  <li>• Chiffrement SSL/TLS des communications</li>
                  <li>• Chiffrement des données sensibles en base</li>
                  <li>• Serveurs sécurisés en Europe</li>
                  <li>• Sauvegardes régulières et chiffrées</li>
                  <li>• Surveillance 24/7 des systèmes</li>
                </ul>
              </div>

              <div class="bg-gray-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">👥 Sécurité organisationnelle</h3>
                <ul class="space-y-2 text-gray-700">
                  <li>• Accès limité aux données par rôle</li>
                  <li>• Formation RGPD de tous les employés</li>
                  <li>• Audits de sécurité réguliers</li>
                  <li>• Contrats de confidentialité</li>
                  <li>• Procédures de gestion des incidents</li>
                </ul>
              </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">🚨 En cas d'incident</h3>
              <p class="text-gray-700">
                Si une violation de données personnelles présente un risque pour vos droits et libertés, 
                nous vous en informerons dans les 72 heures et vous conseillerons sur les mesures à prendre.
              </p>
            </div>
          </article>

          <article id="section6" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">6. Avec qui nous partageons vos données</h2>
            
            <div class="space-y-6">
              <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-red-900 mb-3">❌ Nous ne vendons jamais vos données</h3>
                <p class="text-red-800">
                  ServicesPro ne vend, ne loue et ne cède jamais vos données personnelles à des tiers 
                  à des fins commerciales ou publicitaires.
                </p>
              </div>

              <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-green-900 mb-3">✅ Partage nécessaire au service</h3>
                <ul class="space-y-2 text-green-800">
                  <li>• <strong>Entre utilisateurs :</strong> nom, photo, avis pour la mise en relation</li>
                  <li>• <strong>Prestataires de paiement :</strong> données nécessaires aux transactions</li>
                  <li>• <strong>Services techniques :</strong> hébergement sécurisé (OVHcloud - France)</li>
                  <li>• <strong>Support client :</strong> outils de communication (Intercom)</li>
                </ul>
              </div>

              <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3">⚖️ Obligations légales</h3>
                <p class="text-yellow-800">
                  Nous pouvons être amenés à communiquer vos données aux autorités compétentes 
                  en cas de réquisition judiciaire ou d'obligation légale.
                </p>
              </div>
            </div>
          </article>

          <article id="section7" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">7. Vos droits sur vos données</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">👁️ Droit d'accès</h3>
                <p class="text-gray-700">
                  Vous pouvez consulter toutes les données que nous avons sur vous depuis votre espace personnel 
                  ou en nous contactant.
                </p>
              </div>

              <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">✏️ Droit de rectification</h3>
                <p class="text-gray-700">
                  Vous pouvez corriger ou mettre à jour vos données à tout moment depuis votre profil 
                  ou en contactant notre support.
                </p>
              </div>

              <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">🗑️ Droit d'effacement</h3>
                <p class="text-gray-700">
                  Vous pouvez demander la suppression de vos données, sauf si nous devons les conserver 
                  pour des raisons légales.
                </p>
              </div>

              <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">📦 Droit à la portabilité</h3>
                <p class="text-gray-700">
                  Vous pouvez récupérer vos données dans un format structuré pour les transférer 
                  vers un autre service.
                </p>
              </div>

              <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">🛑 Droit d'opposition</h3>
                <p class="text-gray-700">
                  Vous pouvez vous opposer au traitement de vos données à des fins de prospection 
                  ou pour motif légitime.
                </p>
              </div>

              <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">⏸️ Droit à la limitation</h3>
                <p class="text-gray-700">
                  Vous pouvez demander la suspension temporaire du traitement de vos données 
                  dans certaines situations.
                </p>
              </div>
            </div>

            <div class="bg-primary/5 rounded-xl p-6 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">📞 Comment exercer vos droits</h3>
              <p class="text-gray-700 mb-4">
                Pour exercer vos droits, contactez-nous par email à <strong>dpo@servicespro.fr</strong> 
                ou via notre formulaire de contact en précisant :
              </p>
              <ul class="space-y-1 text-gray-700">
                <li>• Votre demande précise</li>
                <li>• Vos nom, prénom et email de compte</li>
                <li>• Une copie de votre pièce d'identité</li>
              </ul>
              <p class="text-sm text-gray-600 mt-4">
                Nous vous répondrons dans un délai maximum de 30 jours.
              </p>
            </div>
          </article>

          <article id="section8" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">8. Utilisation des cookies</h2>
            
            <div class="space-y-6">
              <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">🍪 Cookies essentiels</h3>
                <p class="text-gray-700 mb-3">
                  Nécessaires au fonctionnement du site (authentification, panier, préférences). 
                  Ils ne peuvent pas être désactivés.
                </p>
                <ul class="text-sm text-gray-600">
                  <li>• Cookies de session utilisateur</li>
                  <li>• Cookies de sécurité</li>
                  <li>• Cookies de préférences</li>
                </ul>
              </div>

              <div class="bg-white border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">📊 Cookies analytiques</h3>
                <p class="text-gray-700 mb-3">
                  Nous aident à comprendre comment vous utilisez notre site pour l'améliorer. 
                  Vous pouvez les refuser.
                </p>
                <ul class="text-sm text-gray-600">
                  <li>• Google Analytics (anonymisé)</li>
                  <li>• Statistiques d'utilisation</li>
                  <li>• Tests A/B pour l'amélioration UX</li>
                </ul>
              </div>

              <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">⚙️ Gérer vos cookies</h3>
                <p class="text-gray-700 mb-3">
                  Vous pouvez modifier vos préférences de cookies à tout moment :
                </p>
                <ul class="space-y-2 text-gray-700">
                  <li>• Via le bandeau cookies sur notre site</li>
                  <li>• Dans les paramètres de votre navigateur</li>
                  <li>• En nous contactant pour plus d'aide</li>
                </ul>
              </div>
            </div>
          </article>

          <article id="section9" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">9. Contact du Délégué à la Protection des Données</h2>
            
            <div class="bg-primary/5 rounded-xl p-6">
              <div class="flex items-start space-x-4">
                <div class="flex-shrink-0 w-12 h-12 bg-primary/20 text-primary rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                </div>
                <div>
                  <h3 class="text-lg font-semibold text-gray-900 mb-3">Marie Dupont, DPO</h3>
                  <div class="space-y-2">
                    <p class="text-gray-700">
                      <strong>Email :</strong> dpo@servicespro.fr
                    </p>
                    <p class="text-gray-700">
                      <strong>Courrier :</strong> ServicesPro SAS - DPO<br>
                      123 Rue de la Tech<br>
                      75001 Paris, France
                    </p>
                  </div>
                  <p class="text-sm text-gray-600 mt-4">
                    Le DPO est votre interlocuteur privilégié pour toutes vos questions sur la protection 
                    de vos données personnelles.
                  </p>
                </div>
              </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mt-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-3">🇫🇷 Droit de réclamation</h3>
              <p class="text-gray-700">
                Vous avez le droit d'introduire une réclamation auprès de la Commission Nationale de 
                l'Informatique et des Libertés (CNIL) si vous estimez que vos droits ne sont pas respectés :
              </p>
              <p class="text-primary mt-2">
                <a href="https://www.cnil.fr" target="_blank" class="hover:underline">www.cnil.fr</a>
              </p>
            </div>
          </article>

          <article id="section10" class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">10. Modifications de cette politique</h2>
            
            <div class="bg-gray-50 rounded-xl p-6">
              <p class="text-gray-700 mb-4">
                Nous pouvons être amenés à modifier cette politique de confidentialité pour refléter 
                les évolutions de nos services ou de la réglementation.
              </p>
              <p class="text-gray-700 mb-4">
                <strong>En cas de modification importante :</strong>
              </p>
              <ul class="space-y-2 text-gray-700 mb-4">
                <li>• Nous vous préviendrons par email 30 jours avant</li>
                <li>• Un bandeau d'information apparaîtra sur le site</li>
                <li>• Vous pourrez consulter les changements en détail</li>
              </ul>
              <p class="text-gray-700">
                La version actuelle est datée du <strong>{{ lastUpdate }}</strong> et remplace 
                toutes les versions précédentes.
              </p>
            </div>
          </article>

        </div>
      </section>

      <!-- Contact -->
      <section class="py-16 bg-gradient-to-r from-primary to-primary-dark text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-2xl font-bold mb-4">Des questions sur vos données ?</h2>
          <p class="text-xl mb-8 opacity-90">Notre équipe est là pour vous aider</p>
          
          <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a
              href="mailto:dpo@servicespro.fr"
              class="inline-flex items-center px-6 py-3 bg-white text-primary rounded-xl font-semibold hover:bg-gray-100 transition-colors"
            >
              Contacter notre DPO
            </a>
            
            <Link
              :href="route('help')"
              class="inline-flex items-center px-6 py-3 bg-secondary text-white rounded-xl font-semibold hover:bg-secondary/90 transition-colors border-2 border-white/20"
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

<style scoped>
.prose h2 {
  @apply text-2xl font-bold text-gray-900 mb-6 mt-8;
}

.prose h3 {
  @apply text-lg font-semibold text-gray-900 mb-3 mt-4;
}

.prose p {
  @apply text-gray-700 mb-4 leading-relaxed;
}

html {
  scroll-behavior: smooth;
}
</style>