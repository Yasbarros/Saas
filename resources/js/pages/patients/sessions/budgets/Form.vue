<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Heading from '@/components/Heading.vue';
import Label from '@/components/ui/label/Label.vue';
import Input from '@/components/ui/input/Input.vue';
import InputError from '@/components/InputError.vue';
import { Select, SelectTrigger, SelectValue, SelectContent, SelectItem } from '@/components/ui/select';
import { ref, computed } from 'vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import Button from '@/components/ui/button/Button.vue';
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import { z } from 'zod';
import FormActions from '@/components/FormActions.vue';
import FormBackground from '@/components/FormBackground.vue';
import FormWrapper from '@/components/FormWrapper.vue';

//TODO: Deixar pagina responsiva para mobile e desktop.

type Patient = {
    id: number;
    name: string;
}

type PriceTable = {
    id: number;
    name: string;
    patient_id: number;
}
type Procedure = {
    id: number;
    name: string;
}
type CashAccount = {
    id: number;
    name: string;
    user_id: number;
    user?: {
        id: number;
        name: string;
    };
}

type Budget = {
    id: number;
    cash_id: number;
    date: string;
    registration_dentist: string;
    procedure_dentist: string;
    execute_all_procedures: boolean;
    price_table_id: number;
    procedure_id: number;
    value: number;
    notes: string;
    unit_value: number;
    guide: string;
    teeth_region: string;
    dentition: string;
    created_at: string;
    updated_at: string;
    procedure?:{
        id: number;
        name: string;
    }
    price_table?:{
        id: number;
        name: string;
    }
}

const props = defineProps<{
    priceTables: PriceTable[],
    procedures: Procedure[],
    cashAccounts: CashAccount[],
    patient: Patient,
    budgets: Budget[],
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Orçamentos',
        href: `/patients/${props.patient.id}/budgets`,
    },
    {
        title: 'Cadastrar Orçamento',
        href: `/patients/${props.patient.id}/budgets/form`,
    }
];

const totalParticles = computed(() => {
  return props.budgets
    .filter(proc => proc.price_table?.id === 3)
    .reduce((sum, proc) => sum + Number(proc.value), 0);
});

const totalPlans = computed(() => {
  return props.budgets
    .filter(proc => proc.price_table?.id !== 3)
    .reduce((sum, proc) => sum + Number(proc.value), 0);
});

const form = useForm({
    cash_id: 1,
    date: '',
    registration_dentist: '',
    procedure_dentist: '',
    execute_all_procedures: false,
    price_table_id: '',
    procedure_id: '',
    value:  '',
    notes: '',
    unit_value: '',
    guide: '',
    teeth_region: '',
    dentition: '',
})

const selectedProcedures = ref([]);

function approve() {
    if (selectedProcedures.value.length === 0) {
        toast.error('Selecione pelo menos um procedimento para aprovar.');
        return;
    }

    router.post(route('patients.budgets.approve', { id: props.patient.id }), {
        budgets: selectedProcedures.value,
    });
}

const budgetsSchema = z.object({
    cash_id: z.number().min(1, 'O campo caixa é obrigatório.'),
    date: z.string().nonempty('O campo data é obrigatório.'),
    registration_dentist: z.string().nonempty('O campo orçamento por é obrigatório.'),
    procedure_dentist: z.string().nonempty('O campo dentista é obrigatório.'),
    price_table_id: z.number().min(1, 'O campo tabela é obrigatório.'),
    procedure_id: z.number().min(1, 'O campo procedimento é obrigatório.'),
    value: z.number().min(0, 'O campo valor deve ser um número positivo.'),
    unit_value: z.number().min(0, 'O campo valor unitário deve ser um número positivo.'),
    guide: z.string().optional(),
    teeth_region: z.string().optional(),
    dentition: z.string().optional(),
    notes: z.string().optional(),
});

function handleSubmit() {

    form.clearErrors();
    const validation = budgetsSchema.safeParse(form.data());

    if (!validation.success) {
        const errors = validation.error.flatten().fieldErrors;
        for (const key in errors) {
            //TODO: Corrigir aviso do TypeScript
            form.setError(key, errors[key].join(', '));
        }
        return;
    }

    form.post(route('patients.budgets.store', { id: props.patient.id }));
    form.reset();
    totalParticles
}

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-2 rounded-xl p-4">
            <Heading
                :title='"Cadastrar Orçamento"'
                :breadcrumbs="breadcrumbs"
            />
            <div class="flex flex-col md:flex-row items-start h-full gap-10 md:gap-28">
                <FormWrapper @submit="handleSubmit">
                    <FormBackground>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-1">
                                <Label for="cash">Caixa</Label>
                                <Select v-model="form.cash_id" >
                                    <SelectTrigger>
                                        <SelectValue v-model="form.cash_id" placeholder="Selecione o caixa"/>
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in props.cashAccounts"
                                            :key="option.id"
                                            :value="option.id"
                                            class="flex items-center gap-2"
                                        >
                                            <span class="text-xs rounded-sm px-1 bg-gray-100 text-gray-500">{{ option.id }}</span>
                                            <span class="font-medium">{{ option.name }}</span>
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.cash_id" />
                            </div>
                            <div class="grid gap-1">
                                <Label for="date">Data</Label>
                                <Input id="date"  type="date" placeholder="Digite a data" v-model="form.date" />
                                <InputError :message="form.errors.date" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid gap-1">
                                <Label for="budgetFor">Orçamento por:</Label>
                                <!-- Input temporario para armazenar o nome do dentista que fez o registro -->
                                <Input id="budgetFor" type="text" placeholder="Digite o nome do dentista que está registrando o orçamento" v-model="form.registration_dentist" />
                                <!-- <Select v-model="form.registration_dentist"> 
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o dentista" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in props.priceTables"
                                            :key="option.id"
                                            :value="option.id"
                                        >
                                            {{ option.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select> -->
                                <InputError :message="form.errors.registration_dentist" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-1">
                                <Label for="price_table">Tabela de Preços</Label>
                                <Select v-model="form.price_table_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione a tabela" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in props.priceTables"
                                            :key="option.id"
                                            :value="option.id"
                                        >
                                            {{ option.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.price_table_id" />
                            </div>
                            <div class="grid gap-1">
                                <Label for="value">Valor</Label>
                                <Input id="value" type="number" placeholder="Digite o valor" v-model="form.value" />
                                <InputError :message="form.errors.value" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-4">
                            <div class="grid gap-1">
                                <Label for="notes">Observações</Label>
                                <Textarea id="notes" placeholder="Digite a observação" v-model="form.notes" />
                                <InputError :message="form.errors.notes" />
                            </div>
                        </div>
                        <div class="border-b border-gray-500 my-4"></div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" >
                            <div class="grid gap-1">
                                <Label class="mb-1" for="procedure">Procedimento</Label>
                                <Select v-model="form.procedure_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o procedimento" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in props.procedures"
                                            :key="option.id"
                                            :value="option.id"
                                        >
                                            {{ option.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.procedure_id" />
                            </div>
                            <div class="grid gap-1">
                                <Label class="mb-1">Valor Unitário</Label>
                                <Input v-model="form.unit_value" type="number" placeholder="Digite o Valor Unitário"
                                    class="w-full border rounded-lg p-2" />
                                <InputError :message="form.errors.unit_value" />
                            </div>
                        </div>
                        <div class=" grid grid-cols-1 md:grid-cols-2 gap-4 " >
                            <div class="grid gap-1">
                                <Label class="mb-1" for="dentist">Dentista</Label>
                                <!-- Input temporário para armazenar o nome do dentista que vai efetuar o procedimento -->
                                <Input v-model="form.procedure_dentist" type="text" placeholder="Digite o nome do dentista"
                                    class="w-full border rounded-lg p-2" />
                                <!-- <Select v-model="form.procedure_dentist">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Selecione o dentista" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="option in props.dentists"
                                            :key="option.id"
                                            :value="option.id"
                                        >
                                            {{ option.name }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select> -->
                                <InputError :message="form.errors.procedure_dentist" />
                            </div>
                            <div class="grid gap-1">
                                <Label for="guide">Guia</Label>
                                <Input v-model="form.guide" type="text" placeholder="Digite o nome do guia"
                                    class="w-full border rounded-lg p-2" />
                                <InputError :message="form.errors.guide" />
                            </div>
                        </div>
                        <div class=" grid grid-cols-1 md:grid-cols-2 gap-4 " >
                            <div class="grid gap-1">
                                <Label class="mb-1" for="teethRegion">Dentes/Região</Label>
                                <Input v-model="form.teeth_region" type="text" placeholder="Digite os dentes ou região"
                                    class="w-full border rounded-lg p-2" />
                                <InputError :message="form.errors.teeth_region" />
                            </div>
                            <div class="grid gap-1">
                                <Label class="mb-1" for="dentition">Dentição</Label>
                                <Input v-model="form.dentition" type="text" placeholder="Digite a dentição"
                                    class="w-full border rounded-lg p-2" />
                                <InputError :message="form.errors.dentition" />
                            </div>
                        </div>
                    </FormBackground>
                    <FormActions
                    :isEdit="false"
                    :processing="form.processing"
                    cancel-route="patients.index"
                    submitText="+ Adicionar Procedimento"
                    processingText="Adicionando..."
                    />
                </FormWrapper>
                <div class="w-max lg:w-1/3 border rounded-lg shadow p-4 flex flex-col">
                    <h2 class="font-semibold mb-4">Procedimentos</h2>
                    <div class="space-y-4 flex-1 overflow-y-auto max-h-56 pr-2">
                        <div v-for="(budget, index) in props.budgets" :key="index" class="border-t pt-2">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" :value="budget.id" v-model="selectedProcedures" />
                                    <span class="font-medium">{{ budget.procedure?.name }}</span>
                                </div>
                                <span class="font-semibold">R${{ budget.value }}</span>
                            </div>
                            <p class="text-xs text-gray-500">{{ budget.price_table?.name }}</p>
                            <p class="text-sm">{{ budget.procedure_dentist }}</p>
                        </div>
                        <div>
                            <p v-if="props.budgets.length === 0" class="text-sm text-gray-500">Nenhum procedimento adicionado.</p>
                        </div>
                    </div>

                    <div class="mt-4 space-y-1 pt-2 border-black border-t-2 text-sm">
                        <p><strong>Total Particular:</strong> R${{ totalParticles }}</p>
                        <p><strong>Total Plano:</strong> R${{ totalPlans }}</p>
                    </div>

                    <Button @click="approve" class="w-full bg-black text-white rounded-md py-2 mt-4 hover:bg-gray-800">
                        Aprovar
                    </Button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
