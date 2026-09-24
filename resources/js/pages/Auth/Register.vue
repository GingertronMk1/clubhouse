<script setup lang="ts">
import Layout from "@/layouts/Layout.vue";
import {router, useForm, usePage} from "@inertiajs/vue3";
import register from "@/routes/register";
import {home} from "@/routes";
import {computed} from "vue";

const loginForm = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
})

function submit() {
    loginForm.post(register.store().url, {
        onSuccess: () => router.visit(home().url)
    })
}

const page = usePage();
const errors = computed(() => page.props.errors ?? {});
</script>

<template>
    <Layout>
        <form @submit.prevent="submit" class="flex flex-col">
            <label for="name">
                Name
                {{ errors.name }}
                <input type="text" name="name" id="name" v-model="loginForm.name"/>
            </label>
            <label for="email">
                Email
                {{ errors.email }}
                <input type="email" name="email" id="email" v-model="loginForm.email"/>
            </label>
            <label for="password">
                Password
                {{ errors.password }}
                <input type="password" name="password" id="password" v-model="loginForm.password"/>
            </label>
            <label for="password_confirmation">
                Password
                {{ errors.password_confirmation }}
                <input type="password" name="password_confirmation" id="password_confirmation" v-model="loginForm.password_confirmation"/>
            </label>
            <input type="submit" value="Register"/>
        </form>
    </Layout>

</template>
