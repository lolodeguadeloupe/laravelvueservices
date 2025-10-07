<script setup lang="ts">
import RegisteredUserController from '@/actions/App/Http/Controllers/Auth/RegisteredUserController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
</script>

<template>
    <AuthBase
        title="Créer votre compte"
        description="Rejoignez ServicesPro et découvrez des services de qualité"
    >
        <Head title="Inscription" />

        <Form
            v-bind="RegisteredUserController.store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <div class="space-y-4">
                <div>
                    <Label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom complet
                    </Label>
                    <Input
                        id="name"
                        type="text"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="name"
                        name="name"
                        placeholder="Jean Dupont"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base"
                    />
                    <InputError :message="errors.name" class="mt-2" />
                </div>

                <div>
                    <Label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse email
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="votre@email.com"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base"
                    />
                    <InputError :message="errors.email" class="mt-2" />
                </div>

                <div>
                    <Label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Mot de passe
                    </Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="••••••••"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base"
                    />
                    <InputError :message="errors.password" class="mt-2" />
                    <p class="mt-2 text-xs text-gray-500">
                        Au moins 8 caractères avec des lettres et des chiffres
                    </p>
                </div>

                <div>
                    <Label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmer le mot de passe
                    </Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="••••••••"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base"
                    />
                    <InputError :message="errors.password_confirmation" class="mt-2" />
                </div>

                <Button
                    type="submit"
                    class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center mt-6"
                    tabindex="5"
                    :disabled="processing"
                    data-test="register-user-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-5 w-5 animate-spin mr-2"
                    />
                    {{ processing ? 'Création en cours...' : 'Créer mon compte' }}
                </Button>
            </div>

            <!-- Avantages -->
            <div class="bg-primary/5 rounded-xl p-4 border border-primary/10">
                <h4 class="font-semibold text-gray-900 mb-2">Pourquoi rejoindre ServicesPro ?</h4>
                <ul class="space-y-1 text-sm text-gray-600">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Accès à des milliers de services vérifiés
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Réservation simple et paiement sécurisé
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Support client disponible 24/7
                    </li>
                </ul>
            </div>

            <!-- Séparateur -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="bg-white px-4 text-gray-500">ou</span>
                </div>
            </div>

            <!-- Navigation -->
            <div class="text-center space-y-4">
                <p class="text-sm text-gray-600">
                    Vous avez déjà un compte ?
                </p>
                <TextLink
                    :href="login()"
                    :tabindex="6"
                    class="w-full inline-flex justify-center items-center px-4 py-3 border-2 border-primary text-primary rounded-xl font-semibold hover:bg-primary hover:text-white transition duration-200"
                >
                    Se connecter
                </TextLink>
            </div>

            <!-- Liens légaux -->
            <div class="text-center pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    En créant un compte, vous acceptez nos 
                    <TextLink href="/terms" class="text-primary hover:underline">Conditions d'utilisation</TextLink>
                    et notre 
                    <TextLink href="/privacy" class="text-primary hover:underline">Politique de confidentialité</TextLink>
                </p>
            </div>
        </Form>
    </AuthBase>
</template>
