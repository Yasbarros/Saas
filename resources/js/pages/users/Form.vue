<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import Title from '@/components/Title.vue';
import ViewBackground from '@/components/ViewBackground.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import { Link } from '@inertiajs/vue3';
import MultiSelect from '@/components/ui/multi-select/MultiSelect.vue';
import { ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { vMaska } from 'maska/vue';

const props = defineProps<{
    roles: { id: number; name: string }[];
    user?: {
        id: number;
        name: string;
        email: string;
        phone: string | null;
        roles: { id: number; name: string }[];
    }
}>();

const breadcrumbs: BreadcrumbItem[] = props.user ? [
    { title: 'Usuários', href: '/users' },
    { title: props.user.name, href: `/users/${props.user.id}` },
    { title: 'Editar', href: `/users/${props.user.id}/edit` }
] : [
    { title: 'Usuários', href: '/users' },
    { title: 'Cadastrar', href: '/users/create' }
];

const selectedRoles = ref<number[]>(props.user ? props.user.roles.map(role => role.id) : []);

const roleOptions = props.roles.map(role => ({
    label: role.name,
    value: role.id,
}));

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    phone: props.user?.phone || '',
    roles: selectedRoles.value,
});

watch(selectedRoles, (newVal) => {
    form.roles = newVal;
});

const submit = () => {
    //TODO: Adicionar validação no front e onBlur, além das máscaras
    //TODO: Analisar como remover máscara antes de enviar a requisição
    if (form.phone) {
        form.phone = form.phone.replace(/\D/g, '');
    }

    if (props.user) {
        form.put(route('users.update', props.user.id));
        return;
    }

    form.post(route('users.store'));
};
</script>

<template>
    <Head title="Usuarios" />

    <AppLayout>
        <div class="flex h-full  flex-col gap-4 rounded-xl p-4">
            <Heading
                :title='props.user ? `Editar Usuário ${props.user.name}` : "Cadastrar Usuário"'
                :breadcrumbs="breadcrumbs"
            />
            <form @submit.prevent="submit" class="lg:max-w-2/4">
                <ViewBackground class="space-y-4">
                    <div>
                        <Title>Informações do usuário</Title>
                        <p class="text-sm text-gray-400">Insira os dados do usuário</p>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full space-y-2">
                            <!-- TODO: Criar componente FormInput, FormItem ou algo do tipo para abranger esses 3 itens -->
                            <Label required for="name">Nome</Label>
                            <Input id="name" type="text" placeholder="Digite o nome" required v-model="form.name" />
                            <InputError :message="form.errors.name" />
                        </div>
                        
                        <div class="w-full space-y-2">
                            <Label required for="email">Email</Label>
                            <Input :disabled="props.user" id="email" type="email" placeholder="email@exemplo.com" required v-model="form.email"/>
                            <InputError :message="form.errors.email" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- TODO: Adicionar tooltip -->
                        <div class="w-full space-y-2">
                            <Label for="role">Funções</Label>
                            <MultiSelect
                                :options="roleOptions"
                                v-model="selectedRoles"
                            />
                            <InputError :message="form.errors.roles" />
                        </div>
                        <div class="w-full space-y-2">
                            <Label for="telefone">Telefone</Label>
                            <Input
                                id="telefone"
                                type="tel"
                                placeholder="Digite o telefone"
                                v-model="form.phone"
                                v-maska="'(##) #####-####'"
                            />
                            <InputError :message="form.errors.phone" />
                        </div>
                    </div>
                </ViewBackground>

                <!-- TODO: Componentizar isso -->
                <div class="flex justify-end gap-2 pt-4">
                    <Button as-child type="button" variant="outline">
                        <Link :href="route('users.index')">Cancelar</Link>
                    </Button>
                    <Button :disabled="form.processing" type="submit" variant="default">{{ props.user ? "Atualizar" : "Cadastrar" }}</Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
