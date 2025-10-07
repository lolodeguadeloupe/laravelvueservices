<script setup lang="ts">
import PasswordResetLinkController from '@/actions/App/Http/Controllers/Auth/PasswordResetLinkController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
}>();
</script>

<template>
    <AuthLayout
        title="Mot de passe oublié"
        description="Saisissez votre adresse email pour recevoir un lien de réinitialisation"
    >
        <Head title="Mot de passe oublié" />

        <div
            v-if="status"
            class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-center text-sm font-medium text-green-700"
        >
            {{ status }}
        </div>

        <div class="space-y-6">
            <Form
                v-bind="PasswordResetLinkController.store.form()"
                v-slot="{ errors, processing }"
                class="space-y-6"
            >
                <div>
                    <Label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse email
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        autocomplete="email"
                        autofocus
                        placeholder="votre@email.com"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base"
                    />
                    <InputError :message="errors.email" class="mt-2" />
                </div>

                <!-- Information rassurante -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Pas de panique !</h3>
                            <p class="mt-1 text-sm text-blue-700">
                                Saisissez l'adresse email associée à votre compte. Nous vous enverrons un lien 
                                sécurisé pour réinitialiser votre mot de passe.
                            </p>
                        </div>
                    </div>
                </div>

                <Button
                    type="submit"
                    class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-5 w-5 animate-spin mr-2"
                    />
                    {{ processing ? 'Envoi en cours...' : 'Envoyer le lien de réinitialisation' }}
                </Button>
            </Form>

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
                    Vous vous souvenez de votre mot de passe ?
                </p>
                <TextLink 
                    :href="login()"
                    class="w-full inline-flex justify-center items-center px-4 py-3 border-2 border-primary text-primary rounded-xl font-semibold hover:bg-primary hover:text-white transition duration-200"
                >
                    Retour à la connexion
                </TextLink>
            </div>

            <!-- Support -->
            <div class="text-center pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 mb-2">
                    Vous rencontrez des difficultés ?
                </p>
                <TextLink href="/contact" class="text-primary hover:underline text-sm">
                    Contactez notre support client
                </TextLink>
            </div>
        </div>
    </AuthLayout>
</template>
