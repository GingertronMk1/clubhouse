<script setup lang="ts">
import Layout from '@/layouts/Layout.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import login from '@/routes/login';
import { home } from '@/routes';
import { computed } from 'vue';

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
});

function submit() {
    loginForm.post(login.store().url, {
        onSuccess: () => router.visit(home().url),
    });
}

const page = usePage();
const errors = computed(() => page.props.errors ?? {});
</script>

<template>
    <Layout>
        <form @submit.prevent="submit" class="flex flex-col">
            <label for="email">
                Email
                {{ errors.email }}
                <input
                    type="email"
                    name="email"
                    id="email"
                    v-model="loginForm.email"
                />
            </label>
            <label for="password">
                Password
                {{ errors.password }}
                <input
                    type="password"
                    name="password"
                    id="password"
                    v-model="loginForm.password"
                />
            </label>
            <label for="remember">
                Remember me
                <input
                    type="checkbox"
                    name="remember"
                    id="remember"
                    v-model="loginForm.remember"
                />
            </label>
            <input type="submit" value="Login" />
        </form>
    </Layout>
</template>
