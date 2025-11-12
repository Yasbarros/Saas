import { computed, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

export function useFlashToasts() {
    const page = usePage();

    const flashSuccess = computed(() => page.props.flash?.success);
    const flashError = computed(() => page.props.flash?.error);
    const flashWarning = computed(() => page.props.flash?.warning);
    const flashInfo = computed(() => page.props.flash?.info);

    onMounted(() => {
        if (flashSuccess.value) {
            toast.success('Sucesso', {
                description: flashSuccess.value,
            });
        }

        if (flashError.value) {
            toast.error('Erro', {
                description: flashError.value,
            });
        }

        if (flashWarning.value) {
            toast.warning('Aviso', {
                description: flashWarning.value,
            });
        }

        if (flashInfo.value) {
            toast.info('Informação', {
                description: flashInfo.value,
            });
        }
    });
}
