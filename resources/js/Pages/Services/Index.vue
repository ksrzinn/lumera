<script setup>
import { computed, ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alert from '@/Components/Alert.vue';
import Icon from '@/Components/Icon.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head } from '@inertiajs/vue3';

const SEARCH_TERM = 'UBS perto de mim';
const GEOLOCATION_TIMEOUT_MS = 10000;

const locating = ref(false);
const error = ref(null);
const mapsUrl = ref(null);
const city = ref('');

const hasCity = computed(() => city.value.trim() !== '');

const coordinatesUrl = ({ latitude, longitude }) =>
    `https://www.google.com/maps/search/${encodeURIComponent(SEARCH_TERM)}/@${latitude},${longitude},14z`;

const cityUrl = (name) =>
    `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(`UBS em ${name}`)}`;

const open = (url) => {
    mapsUrl.value = url;
    window.open(url, '_blank', 'noopener');
};

const errorMessage = (code) => {
    switch (code) {
        case GeolocationPositionError.PERMISSION_DENIED:
            return 'Você não permitiu o acesso à localização. Digite sua cidade abaixo para buscar.';
        case GeolocationPositionError.TIMEOUT:
            return 'Não conseguimos obter sua localização a tempo. Tente de novo ou digite sua cidade abaixo.';
        default:
            return 'Não foi possível descobrir sua localização. Digite sua cidade abaixo para buscar.';
    }
};

const useMyLocation = () => {
    error.value = null;
    mapsUrl.value = null;

    if (!window.isSecureContext) {
        error.value = 'A localização só funciona em conexão segura (HTTPS). Digite sua cidade abaixo para buscar.';
        return;
    }

    if (!('geolocation' in navigator)) {
        error.value = 'Seu navegador não permite obter a localização. Digite sua cidade abaixo para buscar.';
        return;
    }

    locating.value = true;
    navigator.geolocation.getCurrentPosition(
        ({ coords }) => {
            locating.value = false;
            open(coordinatesUrl(coords));
        },
        (failure) => {
            locating.value = false;
            error.value = errorMessage(failure.code);
        },
        { timeout: GEOLOCATION_TIMEOUT_MS, maximumAge: 60000 },
    );
};

const searchCity = () => {
    if (hasCity.value) {
        error.value = null;
        open(cityUrl(city.value.trim()));
    }
};
</script>

<template>
    <Head title="Serviços de saúde" />

    <AppLayout title="Serviços de saúde">
        <section class="rounded-3xl bg-white/80 p-5 shadow-card">
            <h2 class="font-semibold text-rosa-900">Encontre uma unidade de saúde perto de você</h2>
            <p class="mt-1 text-sm text-rosa-800">
                Usamos a localização do seu aparelho só para abrir a busca "{{ SEARCH_TERM }}" no Google Maps.
                Nada é guardado no Cuidar.
            </p>

            <div class="mt-4">
                <PrimaryButton type="button" :disabled="locating" @click="useMyLocation">
                    <Icon name="pin" class="me-2 h-5 w-5" />
                    {{ locating ? 'Obtendo localização...' : 'Usar minha localização' }}
                </PrimaryButton>
            </div>
        </section>

        <Alert v-if="error" variant="warning" class="mt-4">{{ error }}</Alert>

        <section class="mt-5 rounded-3xl bg-white/80 p-5 shadow-card">
            <h2 class="font-semibold text-rosa-900">Ou busque pela cidade</h2>
            <form class="mt-3 space-y-3" @submit.prevent="searchCity">
                <label for="city" class="sr-only">Cidade</label>
                <TextInput
                    id="city"
                    v-model="city"
                    type="text"
                    maxlength="100"
                    placeholder="Digite sua cidade"
                    autocomplete="address-level2"
                />
                <SecondaryButton type="submit" :disabled="!hasCity">Buscar no Google Maps</SecondaryButton>
            </form>
        </section>

        <p v-if="mapsUrl" class="mt-5 text-center text-sm text-rosa-800">
            A busca abriu em outra aba. Se não abriu,
            <a :href="mapsUrl" target="_blank" rel="noopener" class="font-semibold text-rosa-600 underline">
                clique aqui para abrir o Google Maps
            </a>.
        </p>

        <p class="mt-6 text-center text-xs text-rosa-700">
            O Google Maps é um serviço de terceiros. Confirme horários e serviços diretamente com a unidade.
        </p>
    </AppLayout>
</template>
