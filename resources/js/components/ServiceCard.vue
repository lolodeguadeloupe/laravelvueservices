<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
    <!-- Image -->
    <div class="aspect-w-16 aspect-h-9">
      <img
        :src="service.images?.[0]?.url || '/default-service.jpg'"
        :alt="service.title"
        class="w-full h-48 object-cover"
      />
    </div>

    <div class="p-6">
      <!-- Catégorie -->
      <div class="mb-3">
        <span class="inline-block bg-primary/10 text-primary text-xs font-medium px-2 py-1 rounded-full">
          {{ service.category.name }}
        </span>
      </div>

      <!-- Titre et description -->
      <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
        {{ service.title }}
      </h3>
      <p class="text-gray-600 text-sm mb-4 line-clamp-3">
        {{ service.description }}
      </p>

      <!-- Prestataire -->
      <div class="flex items-center mb-4">
        <div class="w-8 h-8 bg-primary/20 text-primary rounded-full flex items-center justify-center font-medium text-sm mr-3">
          {{ service.provider.profile?.first_name?.charAt(0) }}{{ service.provider.profile?.last_name?.charAt(0) }}
        </div>
        <div class="flex-1">
          <p class="text-sm font-medium text-gray-900">
            {{ service.provider.profile?.first_name }} {{ service.provider.profile?.last_name }}
          </p>
          <p class="text-xs text-gray-500">
            {{ service.provider.profile?.city }}
          </p>
        </div>
      </div>

      <!-- Prix et actions -->
      <div class="flex items-center justify-between">
        <div>
          <span class="text-sm text-gray-500">À partir de</span>
          <p class="text-xl font-bold text-primary">{{ service.price }}€</p>
        </div>
        
        <div class="flex space-x-2">
          <Link
            :href="route('services.show', service.id)"
            class="px-3 py-2 text-primary border border-primary rounded-lg hover:bg-primary/5 transition-colors text-sm font-medium"
          >
            Détails
          </Link>
          
          <Link
            v-if="$page.props.auth.user"
            :href="route('bookings.create', service.id)"
            class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium"
          >
            Réserver
          </Link>
          
          <!-- Si l'utilisateur n'est pas connecté -->
          <Link
            v-else
            :href="route('login')"
            class="px-3 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors text-sm font-medium"
          >
            Réserver
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
  service: Object
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>