<script setup lang="ts">
import {Link, router, usePage} from "@inertiajs/vue3";
import {computed} from "vue";
import {home, login, logout, register} from "@/routes";

const env = import.meta.env;

const {props} = usePage();
const user = computed(() => props.auth.user)

const logoutFunction = function () {
    router.visit(logout().url, {
        method: logout().method,
        onSuccess: () => router.visit(home().url),
    })
}
</script>

<template>
        <header class="container p-2 mx-auto flex flex-row justify-between items-center">
            <Link :href="home()" class="text-4xl">
                <h1 v-text="env.VITE_APP_NAME"/>
            </Link>

            <span class="flex flex-row gap-2">
                <button v-if="user" @click="logoutFunction()">Log Out</button>
                <template v-else>
                    <Link :href="register()">Register</Link>
                    <Link :href="login()">Log In</Link>
                </template>
            </span>
        </header>
        <main class="flex flex-col bg-blue-200 grow">
            <div class="container p-2 mx-auto flex flex-col bg-gray-50 h-full">
                <slot/>
            </div>
        </main>
        <footer class="flex flex-row bg-gray-800 text-white">
            <section class="container mx-auto p-2 flex flex-row justify-between items-center">
                Footer!
            </section>
        </footer>
</template>

<style scoped>

</style>
