<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { show as clubShow } from '@/routes/clubs';

defineOptions({ layout: AppLayout });

defineProps<{
    user: { name: string; email: string };
    clubs: { id: string; slug: string; name: string; can_view: boolean }[];
}>();
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head title="Your profile" />
        <h1 class="text-3xl font-semibold">Your profile</h1>
        <dl class="mt-8 space-y-4">
            <div>
                <dt class="text-sm font-medium text-slate-500">Name</dt>
                <dd class="mt-1">{{ user.name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-slate-500">
                    Email address
                </dt>
                <dd class="mt-1">{{ user.email }}</dd>
            </div>
        </dl>
        <section
            class="mt-10"
            aria-labelledby="clubs-heading"
        >
            <h2
                id="clubs-heading"
                class="text-xl font-semibold"
            >
                Your clubs
            </h2>
            <p
                v-if="clubs.length === 0"
                class="mt-3 text-slate-600"
            >
                You don't belong to any clubs yet.
            </p>
            <ul
                v-else
                class="mt-4 space-y-3"
            >
                <li
                    v-for="club in clubs"
                    :key="club.id"
                >
                    <Link
                        v-if="club.can_view"
                        :href="clubShow(club.slug)"
                        class="underline underline-offset-4"
                        >{{ club.name }}</Link
                    >
                    <span v-else>{{ club.name }}</span>
                </li>
            </ul>
        </section>
    </main>
</template>
