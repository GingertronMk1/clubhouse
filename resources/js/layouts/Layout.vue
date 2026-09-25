<script setup lang="ts">
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { home, login, logout, register } from '@/routes';

const env = import.meta.env;

const { props } = usePage();
const user = computed(() => props.auth.user);

const logoutFunction = function () {
    router.visit(logout().url, {
        method: logout().method,
        onSuccess: () => router.visit(home().url),
    });
};
</script>

<template>
    <header
        class="container mx-auto flex flex-row items-center justify-between p-2"
    >
        <Link :href="home()" class="text-4xl">
            <h1 v-text="env.VITE_APP_NAME" />
        </Link>

        <span class="flex flex-row gap-2">
            <button v-if="user" @click="logoutFunction()">Log Out</button>
            <template v-else>
                <Link :href="register()">Register</Link>
                <Link :href="login()">Log In</Link>
            </template>
        </span>
    </header>
    <main class="flex grow flex-col bg-blue-200">
        <div class="container mx-auto flex h-full flex-col bg-gray-50 p-2">
            <slot />
        </div>
    </main>
    <footer class="flex flex-row bg-gray-800 text-white">
        <section
            class="container mx-auto flex flex-row items-center justify-between p-2"
        >
            Footer!
        </section>
    </footer>
</template>

<style scoped></style>
