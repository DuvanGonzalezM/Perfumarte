<script setup>
import Alert from '@/Components/Alert.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

const url = computed(() => page.props.flash?.activation_url ?? null);
const copied = ref(false);

const copy = async () => {
    try {
        await navigator.clipboard.writeText(url.value);
        copied.value = true;
        setTimeout(() => (copied.value = false), 3000);
    } catch (e) {
        copied.value = false;
    }
};
</script>

<template>
    <Alert v-if="url" type="warning" icon="fa-key" class="activation-link">
        <div>
            <strong>Enlace de activación</strong> — válido 72 horas y de un solo uso efectivo.
            Entrégueselo al titular de la cuenta por un canal seguro. No volverá a mostrarse.
        </div>
        <div class="d-flex align-items-center gap-2 mt-2 flex-wrap">
            <code class="activation-link__url">{{ url }}</code>
            <button type="button" class="btn btn-sm btn-outline-dark" @click="copy">
                {{ copied ? 'Copiado' : 'Copiar' }}
            </button>
        </div>
    </Alert>
</template>

<style scoped>
.activation-link__url {
    word-break: break-all;
    background: rgba(0, 0, 0, 0.06);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.85rem;
}
</style>
