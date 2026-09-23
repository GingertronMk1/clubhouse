<script setup lang="ts">
import {useForm, usePage} from "@inertiajs/vue3";
import auth from "@/routes/auth";
import {computed, ref} from "vue";
import AppLayout from "@/layouts/AppLayout.vue";

const data = useForm({
    email: "",
    password: "",
});

function login() {
    data.post(auth.login.create().url)
}

const errors = computed(() => usePage().props.errors);

const showPassword = ref(false);
</script>

<template>
    <AppLayout>
        <div class="gap-2 flex flex-col">
            <label for="email" class="flex flex-col">
                <span>Email</span>
                <span v-if="errors.email" v-text="errors.email"/>
                <input type="email" name="email" id="email" placeholder="Email Address" v-model="data.email">
            </label>
            <label for="password" class="flex flex-col">
                <span>Password</span>
                <input :type="showPassword ? 'text' : 'password'" name="password" id="password" v-model="data.password">
            </label>
            <label for="password_as_plain_text" class="flex flex-row gap-2">
                <input type="checkbox" name="password_as_plain_text" id="password_as_plain_text" v-model="showPassword"
                       :checked="showPassword"/>
                <span>{{ showPassword ? 'Hide' : 'Show' }} password</span>
            </label>
            <button @click="login()">Log In</button>
        </div>
    </AppLayout>
</template>
