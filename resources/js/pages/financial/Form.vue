<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Title from '@/components/Title.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { computed } from 'vue';
import FormActions from '@/components/FormActions.vue';
import FormBackground from '@/components/FormBackground.vue';
import FormWrapper from '@/components/FormWrapper.vue';

const props = defineProps<{
    cashAccount?: {
        id: number;
        name: string;
        user_id: number;
        user?: {
            id: number;
            name: string;
        };
    };
    users?: {
        id: number;
        name: string;
    }[];
    session?: {
        id: number;
        initial_balance: number;
    };
}>();

const isEdit = computed(() => !!props.cashAccount);

const breadcrumbs: BreadcrumbItem[] = props.cashAccount ? [
    { title: 'Caixas', href: '/financial' },
    { title: 'Caixa ' + props.cashAccount.id, href: `/financial/${props.cashAccount.id}/edit` }
] : [
    { title: 'Caixas', href: '/financial' },
    { title: 'Cadastrar', href: '/financial/create' }
];

const userOptions = props.users?.map(user => ({
    label: user.name,
    value: user.id,
}));

const initialData = {
    name: props.cashAccount?.name ?? '',
    user_id: props.cashAccount?.user_id ?? '',
    initial_balance: props.session?.initial_balance ?? '',
}

const form = useForm({
    ...initialData,
});

function formatCurrency() {
  let value = form.initial_balance.replace(/\D/g, '');
  value = (Number(value) / 100).toFixed(2);
  form.initial_balance = 'R$ ' + value.replace('.', ',');
}

const submit = () => {
    const numericBalance = Number(
        form.initial_balance
            .replace('R$', '')
            .replace(/\./g, '')
            .replace(',', '.')
    );
    form.initial_balance = numericBalance;

    const routeName = isEdit.value ? 'financial.update' : 'financial.store'
    const routeParams = isEdit.value ? [props.cashAccount?.id] : []

    form
    .transform(data => ({
      ...data,
      _method: isEdit.value ? 'PUT' : undefined,
    }))
    .post(route(routeName, ...routeParams))
};
</script>

<template>
    <Head title="Caixas" />
    <AppLayout>
        <div class="flex h-full  flex-col gap-4 rounded-xl p-4">
            <Heading
                :title="isEdit ? `Editar Caixa ${props.cashAccount?.name}` : 'Cadastrar Caixa'"
                :breadcrumbs="breadcrumbs"
            />
            <FormWrapper @submit="submit">
                <FormBackground>
                    <div>
                        <Title>Informações do caixa</Title>
                        <p class="text-sm text-gray-400">Insira os dados do caixa</p>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full space-y-2">
                            <Label for="telefone">Nome do caixa</Label>
                            <Input type="text" v-model="form.name" placeholder="Digite o nome do caixa"/>
                            <InputError :message="form.errors.name" />
                        </div>
                        
                        <div class="w-full space-y-2">
                            <Label required for="email">Usuário</Label>
                            <Select v-model="form.user_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Selecione o usuário do caixa" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="option in userOptions"
                                        :key="option.value"
                                        :value="option.value"
                                    >
                                        {{ option.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.user_id" />
                        </div>
                        <!-- <div v-if="isEdit" class="w-full space-y-2">
                            <Label for="initial_balance">Balanço inicial</Label>
                            <Input type="text" v-model="form.initial_balance" placeholder="Digite o balanço inicial" />
                            <InputError :message="form.errors.initial_balance" />
                        </div> -->
                    </div>

                    <template v-if="isEdit">
                        <div class="flex flex-col md:flex-row gap-4">
                             <div class="w-full space-y-2">
                                <Label for="initial_balance">Balanço inicial</Label>
                                <Input type="text" @input="formatCurrency" v-model="form.initial_balance" placeholder="Digite o balanço inicial" />
                                <InputError :message="form.errors.initial_balance" />
                            </div>
                            <div class="w-full space-y-2">
                                <div class="invisible h-12"></div>
                            </div>
                        </div>
                    </template>
                </FormBackground>

                <FormActions
                :isEdit="isEdit"
                :processing="form.processing"
                cancelRoute="financial.index"
                cancelText="Cancelar"
                submitText="Cadastrar"
                editText="Atualizar"
                processingText="Cadastrando..."
                processingEditText="Atualizando..."
                />
            </FormWrapper>
        </div>
    </AppLayout>
</template>
