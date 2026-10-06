<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import BrandLogo from '@/Components/BrandLogo.vue';
import Icon from '@/Components/Icon.vue';

defineProps({
    title: { type: String, default: null },
    back: { type: String, default: null },
});

const page = usePage();

const isProfessional = page.props.auth.user.role === 'professional';
const homeHref = isProfessional ? '/profissional' : '/paciente';

const tabs = [
    { label: 'Início', icon: 'home', href: homeHref },
    ...(isProfessional ? [] : [{ label: 'Exames', icon: 'exams', href: '/exames' }]),
    { label: 'Chat', icon: 'chat', href: '/mensagens' },
    { label: 'Perfil', icon: 'user', href: '/profile' },
];
const isActive = (href) => page.url === href || page.url.startsWith(href + '/');
</script>

<template>
    <div class="flex min-h-screen flex-col bg-rosa-100">
        <header class="sticky top-0 z-10 bg-rosa-100/90 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-2xl items-center gap-3 px-4">
                <Link
                    v-if="back"
                    :href="back"
                    class="-ml-2 rounded-full p-2 text-rosa-600 hover:bg-rosa-200"
                    aria-label="Voltar"
                >
                    <Icon name="arrow-left" class="h-5 w-5" />
                </Link>
                <Link v-else :href="homeHref" aria-label="Cuidar">
                    <BrandLogo size="sm" />
                </Link>

                <h1 v-if="title" class="flex-1 text-lg font-semibold text-rosa-800">
                    {{ title }}
                </h1>
                <div v-else class="flex-1" />

                <nav class="hidden items-center gap-1 md:flex" aria-label="Menu principal">
                    <Link
                        v-for="tab in tabs"
                        :key="tab.href"
                        :href="tab.href"
                        class="rounded-full px-4 py-2 text-sm font-medium"
                        :class="isActive(tab.href) ? 'bg-rosa-500 text-white' : 'text-rosa-700 hover:bg-rosa-200'"
                    >
                        {{ tab.label }}
                    </Link>
                </nav>

                <Link
                    href="/logout"
                    method="post"
                    as="button"
                    class="rounded-full p-2 text-rosa-600 hover:bg-rosa-200"
                    aria-label="Sair"
                >
                    <Icon name="logout" class="h-5 w-5" />
                </Link>
            </div>
        </header>

        <main class="mx-auto w-full max-w-2xl flex-1 px-4 pb-28 pt-2 md:pb-10">
            <slot />
        </main>

        <footer class="mx-auto w-full max-w-2xl px-4 pb-24 text-center text-xs text-rosa-700/80 md:pb-6">
            Conteúdo educativo, não substitui consulta médica.
            Fontes: INCA e Ministério da Saúde.
        </footer>

        <nav
            class="fixed inset-x-0 bottom-0 z-10 border-t border-rosa-200 bg-white/95 md:hidden"
            aria-label="Menu principal"
        >
            <ul :class="isProfessional ? 'grid-cols-3' : 'grid-cols-4'" class="mx-auto grid max-w-2xl">
                <li v-for="tab in tabs" :key="tab.href">
                    <Link
                        :href="tab.href"
                        class="flex flex-col items-center gap-1 py-2 text-xs"
                        :class="isActive(tab.href) ? 'font-semibold text-rosa-600' : 'text-rosa-400'"
                    >
                        <Icon :name="tab.icon" class="h-5 w-5" />
                        {{ tab.label }}
                    </Link>
                </li>
            </ul>
        </nav>
    </div>
</template>
