<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm, usePage, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { SharedData, type NavItem } from '@/types';

//TODO: Tipar, componentizar
const breadcrumbItems = [
    {
        title: 'Configurações',
        href: '/settings',
    },
    //TODO: Revisar isso aqui, ta meio gambi
    {
        title: 'Perfil',
        href: '/settings/profile',
    },
    {
        title: 'Senha',
        href: '/settings/profile/password',
    },
];

//TODO: Componentizar isso
const sidebarNavItems: NavItem[] = [
    {
        title: 'Dados do perfil',
        href: '/settings/profile',
    },
    {
        title: 'Senha',
        href: '/settings/profile/password',
    },
];

const page = usePage<SharedData>();
const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors: any) => {
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Configurações de senha" />

        <SettingsLayout :breadcrumb-items="breadcrumbItems">
            <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-x-12 lg:space-y-0">
                <!-- TODO: Componentizar isso -->
                <aside class="w-full max-w-xl lg:w-48">
                    <nav class="flex flex-col space-x-0 space-y-1">
                        <Button
                            v-for="item in sidebarNavItems"
                            :key="item.href"
                            variant="ghost"
                            :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                            as-child
                        >
                            <Link :href="item.href">
                                {{ item.title }}
                            </Link>
                        </Button>
                    </nav>
                </aside>

                <Separator class="my-6 md:hidden" />

                <div class="space-y-6">
                    <HeadingSmall title="Atualizar senha" description="Certifique-se de que sua conta esteja usando uma senha longa e aleatória para permanecer segura" />

                    <form @submit.prevent="updatePassword" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="current_password">Senha atual</Label>
                            <Input
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="form.current_password"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="current-password"
                                placeholder="Senha atual"
                            />
                            <InputError :message="form.errors.current_password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password">Nova senha</Label>
                            <Input
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                                placeholder="Nova senha"
                            />
                            <InputError :message="form.errors.password" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="password_confirmation">Confirmar senha</Label>
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                autocomplete="new-password"
                                placeholder="Confirmar senha"
                            />
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing">Salvar senha</Button>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">Salvo.</p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
