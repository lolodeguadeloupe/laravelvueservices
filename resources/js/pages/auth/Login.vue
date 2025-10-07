<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { register } from '@/routes';
import { request } from '@/routes/password';
import { Form, Head } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <AuthBase
        title="Connexion à votre compte"
        description="Connectez-vous pour accéder à votre espace personnel"
    >
        <Head title="Connexion" />

        <div
            v-if="status"
            class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-center text-sm font-medium text-green-700"
        >
            {{ status }}
        </div>

        <Form
            v-bind="AuthenticatedSessionController.store.form()"
            :reset-on-success="['password']"
            v-slot="{ errors, processing }"
            class="space-y-6"
        >
            <div class="space-y-4">
                <div>
                    <Label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse email
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        :tabindex="1"
                        autocomplete="email"
                        placeholder="votre@email.com"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base"
                    />
                    <InputError :message="errors.email" class="mt-2" />
                </div>

                <div>
                    <div class="flex items-center justify-between mb-2">
                        <Label for="password" class="block text-sm font-medium text-gray-700">
                            Mot de passe
                        </Label>
                        <TextLink
                            v-if="canResetPassword"
                            :href="request()"
                            class="text-sm text-primary hover:text-primary/80 font-medium"
                            :tabindex="5"
                        >
                            Mot de passe oublié ?
                        </TextLink>
                    </div>
                    <Input
                        id="password"
                        type="password"
                        name="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base"
                    />
                    <InputError :message="errors.password" class="mt-2" />
                </div>

                <div class="flex items-center">
                    <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                        <Checkbox 
                            id="remember" 
                            name="remember" 
                            :tabindex="3" 
                            class="h-4 w-4 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary"
                        />
                        <span class="text-sm text-gray-700">Se souvenir de moi</span>
                    </Label>
                </div>

                <Button
                    type="submit"
                    class="w-full bg-primary hover:bg-primary/90 text-white font-semibold py-3 px-4 rounded-xl transition duration-200 shadow-lg hover:shadow-xl flex items-center justify-center"
                    :tabindex="4"
                    :disabled="processing"
                    data-test="login-button"
                >
                    <LoaderCircle
                        v-if="processing"
                        class="h-5 w-5 animate-spin mr-2"
                    />
                    {{ processing ? 'Connexion...' : 'Se connecter' }}
                </Button>
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
                    Vous n'avez pas encore de compte ?
                </p>
                <TextLink 
                    :href="register()" 
                    :tabindex="6"
                    class="w-full inline-flex justify-center items-center px-4 py-3 border-2 border-primary text-primary rounded-xl font-semibold hover:bg-primary hover:text-white transition duration-200"
                >
                    Créer un compte
                </TextLink>
            </div>

            <!-- Liens utiles -->
            <div class="text-center pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500 mb-4">
                    En vous connectant, vous acceptez nos 
                    <TextLink href="/terms" class="text-primary hover:underline">Conditions d'utilisation</TextLink>
                    et notre 
                    <TextLink href="/privacy" class="text-primary hover:underline">Politique de confidentialité</TextLink>
                </p>
            </div>
        </Form>
    </AuthBase>
</template>
