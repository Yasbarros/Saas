<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm, Link } from '@inertiajs/vue3';
import Title from '@/components/Title.vue';
import ViewBackground from '@/components/ViewBackground.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { computed } from 'vue';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';

const props = defineProps<{
    cashAccount?: {
        id: number;
        name: string;
        user_id: number;
        status: number;
        initial_balance: string;
        user?: {
            id: number;
            name: string;
        };
    };
    users?: {
        id: number;
        name: string;
    }[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Caixas', href: '/financial' },
    { title: 'Abrir', href: `/financial/open` }
];

const userOptions = props.users?.map(user => ({
    label: user.name,
    value: user.id,
}));

const form = useForm({
    user_id_opened: props.cashAccount?.user_id ?? '',
    user_name: props.cashAccount?.user?.name ?? '',
    name: props.cashAccount?.name ?? '',
    initial_balance: props.cashAccount?.initial_balance
        ? `R$ ${Number(props.cashAccount.initial_balance).toFixed(2).replace('.', ',')}`
        : 'R$ 0,00',
    cash_account_id: props.cashAccount?.id ?? '',
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
    form.post(route('financial.storeOpen', props.cashAccount?.id));
};
</script>

<template>
    <Head title="Caixas" />
    <AppLayout>
        <div class="flex h-full  flex-col gap-4 rounded-xl p-4">
            <Heading
                :title="'Abrir Caixa'"
                :breadcrumbs="breadcrumbs"
            />
            <form @submit.prevent="submit" class="lg:max-w-2/4">
                <ViewBackground class="space-y-4">
                    <div>
                        <Title>Informações do caixa</Title>
                        <p class="text-sm text-gray-400">Insira os dados do caixa</p>
                    </div>
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="w-full space-y-2">
                            <Label for="telefone">Nome do caixa</Label>
                            <Input disabled v-model="form.name"/>
                        </div>
                        <div class="w-full space-y-2">
                            <Label for="telefone">Saldo Inicial</Label>
                            <Input type="text"  @input="formatCurrency" v-model="form.initial_balance" placeholder="Digite o saldo inicial"/>
                            <InputError :message="form.errors.initial_balance" />
                        </div>
                    </div>
                </ViewBackground>

                <div class="flex justify-end gap-2 pt-4">
                    <Button as-child type="button" variant="outline">
                        <Link :href="route('financial.index')">Cancelar</Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        Abrir Caixa
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
