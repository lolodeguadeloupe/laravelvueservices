<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-screen bg-gradient-to-br from-primary/5 via-white to-secondary/5">
        <!-- Côté gauche - Image/Branding -->
        <div class="hidden lg:flex lg:flex-1 lg:flex-col lg:justify-center lg:px-12 xl:px-24">
            <div class="mx-auto max-w-md">
                <Link :href="home()" class="flex items-center mb-8">
                    <div class="w-12 h-12 bg-primary/20 text-primary rounded-2xl flex items-center justify-center mr-3">
                        <AppLogoIcon class="size-8" />
                    </div>
                    <span class="text-2xl font-bold text-gray-900">ServicesPro</span>
                </Link>
                
                <div class="space-y-6">
                    <h2 class="text-3xl font-bold text-gray-900">
                        Rejoignez notre communauté
                    </h2>
                    <p class="text-lg text-gray-600">
                        Connectez-vous avec des milliers de prestataires et clients qui nous font confiance.
                    </p>
                    
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary/20 text-primary rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-gray-700">Prestataires vérifiés</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-secondary/20 text-secondary rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <span class="text-gray-700">Paiements sécurisés</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25a9.75 9.75 0 100 19.5 9.75 9.75 0 000-19.5z" />
                                </svg>
                            </div>
                            <span class="text-gray-700">Support 24/7</span>
                        </div>
                    </div>
                    
                    <div class="bg-white/50 rounded-2xl p-6 border border-white/20">
                        <div class="flex items-center space-x-4">
                            <div class="flex text-yellow-400">
                                <svg v-for="i in 5" :key="i" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">4.8/5</p>
                                <p class="text-sm text-gray-600">Note de satisfaction</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Côté droit - Formulaire -->
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <!-- Logo mobile -->
                <div class="lg:hidden mb-8">
                    <Link :href="home()" class="flex items-center justify-center">
                        <div class="w-12 h-12 bg-primary/20 text-primary rounded-2xl flex items-center justify-center mr-3">
                            <AppLogoIcon class="size-8" />
                        </div>
                        <span class="text-2xl font-bold text-gray-900">ServicesPro</span>
                    </Link>
                </div>

                <!-- Contenu du formulaire -->
                <div>
                    <h1 class="text-2xl font-bold leading-9 tracking-tight text-gray-900">{{ title }}</h1>
                    <p v-if="description" class="mt-2 text-sm leading-6 text-gray-500">
                        {{ description }}
                    </p>
                </div>

                <div class="mt-8">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
