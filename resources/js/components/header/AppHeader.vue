<script setup lang="ts">
import {computed} from "vue";
import {Link, usePage} from "@inertiajs/vue3";
import auth from "@/routes/auth";

const user = computed(() => usePage().props.auth.user);
</script>

<template>
    <header class="py-2 flex flex-row">
        <div class="container mx-auto flex flex-row justify-between">
            <h1 class="text-3xl">Header</h1>
            <div class="flex flex-row gap-2">
                <template v-if="!user">
                    <Link :href="auth.login.show().url" as="button">Login</Link>
                </template>
                <template v-else>
                    <Link :href="auth.logout().url" :method="auth.logout().method" as="button">Logout</Link>
                </template>
            </div>
        </div>
    </header>
</template>

<style scoped>

</style>
