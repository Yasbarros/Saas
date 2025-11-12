<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ViewBackground from '@/components/ViewBackground.vue';
import InfoItem from '@/components/InfoItem.vue';
import { formatDateTime } from '@/utils/format';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuGroup,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Link } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import ToggleUserStatusDialog from '@/components/ToggleUserStatusDialog.vue';
import DeleteDialog from '@/components/DeleteDialog.vue';
import { router } from '@inertiajs/vue3';
import { useFlashToasts } from '@/composables/useFlashToasts';

const props = defineProps<{
    user: {
        id: number;
        name: string;
        email: string;
        phone: string | null;
        is_active: boolean;
        roles: {
            id: number;
            name: string;
        }[];
        email_verified_at: string | null;
        created_at: string;
        updated_at: string;
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Usuários',
        href: '/users',
    },
    {
        title: props.user.name,
        href: `/users/${props.user.id}`,
    },
];

useFlashToasts();
</script>

<template>
    <Head title="Usuário" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 lg:w-1/2">
            <Heading :title="'Usuário'" :breadcrumbs="breadcrumbs" />
            
            <!-- TODO: Dar um jeito de componentizar isso -->
            <div class="space-x-2">
                <Button as-child>
                    <Link :href="route('users.edit', props.user.id)">
                        Editar
                    </Link>
                </Button>
                
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline">
                            Ações
                            <ChevronDown  />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent class="w-40">
                        <DropdownMenuGroup>
                            <ToggleUserStatusDialog
                                as-child
                                :user="props.user"
                            />
                            <DeleteDialog
                                as-child
                                :title="'Excluir usuário'"
                                :description="`Tem certeza que deseja excluir o usuário ${props.user.name}?`"
                                :onConfirm="() => {
                                    router.delete(route('users.destroy', props.user.id), {
                                        preserveState: false,
                                        preserveScroll: true
                                    });
                                }"
                            />
                        </DropdownMenuGroup>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <ViewBackground>
                <InfoItem :title="'ID'" :info="props.user.id" />
                <InfoItem :title="'Nome'" :info="props.user.name" />
                <InfoItem :title="'Email'" :info="props.user.email" />
                <!-- TODO: Criar função para formatar telefone -->
                <InfoItem :title="'Telefone'" :info="props.user.phone" />
                <InfoItem :title="'Funções'" :info="props.user.roles.map(role => role.name).join(', ') || '-'"/>
                <InfoItem :title="'Ativo'" :info="props.user.is_active ? 'Sim' : 'Não'" />
                <InfoItem :title="'Email Verificado'" :info="props.user.email_verified_at ? 'Sim' : 'Não'" />
                <InfoItem :title="'Criado'" :info="formatDateTime(props.user.created_at)" />
                <InfoItem :title="'Atualizado'" :info="formatDateTime(props.user.updated_at)" />
            </ViewBackground>
        </div>
    </AppLayout>
</template>
