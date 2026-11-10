<script setup lang="ts">
import FormInput from '@/components/FormInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/routes/login';

defineOptions({ layout: AppLayout });

const form = useForm({ email: '', password: '' });

function submit() {
    form.post(store.url(), { onFinish: () => form.reset('password') });
}
</script>

<template>
    <main class="mx-auto max-w-md px-6 py-12 text-slate-900">
        <Head title="Log in" />
        <h1 class="text-3xl font-semibold">Log in</h1>
        <p class="mt-3 text-slate-600">Welcome back to Clubhouse.</p>
        <form
            class="mt-8 space-y-6"
            @submit.prevent="submit"
        >
            <div>
                <label
                    for="email"
                    class="block font-medium"
                    >Email address</label
                >
                <FormInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    autocomplete="username"
                    required
                    autofocus
                    :aria-invalid="!!form.errors.email"
                    :aria-describedby="
                        form.errors.email ? 'email-error' : undefined
                    "
                />
                <p
                    v-if="form.errors.email"
                    id="email-error"
                    class="mt-1 text-sm text-red-700"
                    role="alert"
                >
                    {{ form.errors.email }}
                </p>
            </div>
            <div>
                <label
                    for="password"
                    class="block font-medium"
                    >Password</label
                >
                <FormInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    autocomplete="current-password"
                    required
                    :aria-invalid="!!form.errors.password"
                    :aria-describedby="
                        form.errors.password ? 'password-error' : undefined
                    "
                />
                <p
                    v-if="form.errors.password"
                    id="password-error"
                    class="mt-1 text-sm text-red-700"
                    role="alert"
                >
                    {{ form.errors.password }}
                </p>
            </div>
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded bg-slate-900 px-4 py-2 text-white disabled:opacity-50"
            >
                {{ form.processing ? 'Logging in…' : 'Log in' }}
            </button>
        </form>
    </main>
</template>
