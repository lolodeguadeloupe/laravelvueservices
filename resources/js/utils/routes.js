/**
 * Helper function pour générer les URLs des routes
 * Compatible avec le système de routes Laravel/Inertia
 */
export const route = (name, params = {}) => {
  const routes = {
    // Pages principales
    'home': '/',
    'services.index': '/services',
    'about': '/about',
    'how-it-works': '/how-it-works',
    'contact': '/contact',
    'help': '/help',
    'terms': '/terms',
    'privacy': '/privacy',
    
    // Authentification
    'login': '/login',
    'register': '/register',
    'password.request': '/forgot-password',
    'password.reset': '/reset-password',
    
    // Services
    'services.show': '/services/{service}',
    'services.create': '/services/create',
    'services.edit': '/services/{service}/edit',
    'services.store': '/services',
    'services.update': '/services/{service}',
    'services.destroy': '/services/{service}',
    
    // Réservations
    'bookings.index': '/bookings',
    'bookings.show': '/bookings/{booking}',
    'bookings.create': '/services/{service}/book',
    'bookings.store': '/bookings',
    'bookings.accept': '/bookings/{booking}/accept',
    'bookings.reject': '/bookings/{booking}/reject',
    'bookings.complete': '/bookings/{booking}/complete',
    'bookings.cancel': '/bookings/{booking}/cancel',
    'bookings.quote': '/bookings/{booking}/quote',
    
    // Provider
    'provider.dashboard': '/provider/dashboard',
    'provider.services.index': '/provider/services',
    'provider.services.create': '/provider/services/create',
    'provider.services.show': '/provider/services/{service}',
    'provider.services.edit': '/provider/services/{service}/edit',
    'provider.services.store': '/provider/services',
    'provider.services.update': '/provider/services/{service}',
    'provider.services.destroy': '/provider/services/{service}',
    'provider.services.toggle-status': '/provider/services/{service}/toggle-status',
    'provider.bookings': '/provider/bookings',
    'provider.earnings': '/provider/earnings',
    'provider.profile': '/provider/profile',
    'provider.profile.update': '/provider/profile',
    
    // Inscription prestataire
    'provider.registration.step1': '/provider/registration/step1',
    'provider.registration.step2': '/provider/registration/step2',
    'provider.registration.step3': '/provider/registration/step3',
    'provider.registration.success': '/provider/registration/success',
    
    // Dashboard
    'dashboard': '/dashboard',
  }
  
  let url = routes[name]
  
  if (!url) {
    console.warn(`Route '${name}' not found, returning '#'`)
    return '#'
  }
  
  // Remplacer les paramètres dans l'URL
  if (typeof params === 'object' && params !== null) {
    Object.keys(params).forEach(key => {
      url = url.replace(`{${key}}`, params[key])
    })
  }
  
  return url
}

// Export par défaut pour faciliter l'import
export default route