<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

// import DeleteUser from '@/components/DeleteUser.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type SharedData, type User, type NavItem } from '@/types';

interface Props {
    mustVerifyEmail: boolean;
    status?: string;
}

defineProps<Props>();

const page = usePage<SharedData>();
const user = page.props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
});

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

//TODO: Tipar, componentizar
const breadcrumbItems = [
    {
        title: 'Configurações',
        href: '/settings',
    },
    {
        title: 'Perfil',
        href: '/settings/profile',
    },
];

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';

const submit = () => {
    form.patch(route('profile.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Configurações de perfil" />

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

                <div class="flex flex-col space-y-6">
                    <HeadingSmall title="Informações do perfil" description="Atualize seu nome e endereço de email" />

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="grid gap-2">
                            <Label for="name">Nome</Label>
                            <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" placeholder="Nome completo" />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="email">E-mail</Label>
                            <Input
                                id="email"
                                type="email"
                                class="mt-1 block w-full"
                                v-model="form.email"
                                required
                                autocomplete="username"
                                placeholder="Endereço de email"
                            />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div v-if="mustVerifyEmail && !user.email_verified_at">
                            <p class="-mt-4 text-sm text-muted-foreground">
                                Seu endereço de email não está verificado.
                                <Link
                                    :href="route('verification.send')"
                                    method="post"
                                    as="button"
                                    class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                >
                                    Clique aqui para reenviar o email de verificação.
                                </Link>
                            </p>

                            <div v-if="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                                Um novo link de verificação foi enviado para seu endereço de email.
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <Button :disabled="form.processing">Salvar</Button>

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
                    <!-- TODO: Analisar se o usuário pode apagar sua própria conta ou pelo menos desativar -->
                    <!-- <DeleteUser /> -->
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
