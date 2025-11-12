<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import ViewBackground from '@/components/ViewBackground.vue';
import InfoItem from '@/components/InfoItem.vue';
import Heading from '@/components/Heading.vue';
import { formatDateTime } from '@/utils/format';
import { getPermissionLabel } from '@/utils/permissions';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Link } from '@inertiajs/vue3';
import { ChevronDown } from 'lucide-vue-next';
import DeleteDialog from '@/components/DeleteDialog.vue';
import { router } from '@inertiajs/vue3';
import { useFlashToasts } from '@/composables/useFlashToasts';

const props = defineProps<{
    role: {
        id: number;
        name: string;
        created_at: string;
        updated_at: string;
        permissions: {
            id: number;
            name: string;
        }[];
    };
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Usuários',
        href: '/users',
    },
    {
        title: 'Funções',
        href: '/roles',
    },
    {
        title: props.role.name,
        href: `/roles/${props.role.id}`,
    }
];

const permissionsLabel = props.role.permissions
    .map(permission => getPermissionLabel(permission.name))
    .join(', ');

useFlashToasts();
</script>

<template>
    <Head title="Função" />

    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 lg:w-1/2">
            <Heading :title="`Função ${role.name}`" :breadcrumbs="breadcrumbs" />

            <!-- TODO: Dar um jeito de componentizar isso -->
            <div class="space-x-2">
                <Button as-child>
                    <Link :href="route('roles.edit', props.role.id)">
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
                            <DeleteDialog
                                as-child
                                :title="'Excluir função'"
                                :description="`Tem certeza que deseja excluir a função ${props.role.name}?`"
                                :onConfirm="() => {
                                    router.delete(route('roles.destroy', props.role.id), {
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
                <InfoItem :title="'ID'" :info="role.id" />
                <InfoItem :title="'Nome'" :info="role.name" />
                <InfoItem :title="'Permissões'" :info="permissionsLabel" />
                <InfoItem :title="'Criado'" :info="formatDateTime(role.created_at)" />
                <InfoItem :title="'Atualizado'" :info="formatDateTime(role.updated_at)" />
            </ViewBackground>
        </div>
    </AppLayout>
</template>
