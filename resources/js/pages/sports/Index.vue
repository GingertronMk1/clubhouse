<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link } from '@inertiajs/vue3';
import { create, show } from '@/routes/sports';

defineProps<{
    sports: { id: string; slug: string; name: string; description: string }[];
}>();
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head title="Sports" />
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-semibold">Sports</h1>
            <Link
                :href="create.url()"
                class="rounded bg-slate-900 px-4 py-2 text-white"
                >Add sport</Link
            >
        </div>
        <p
            v-if="sports.length === 0"
            class="text-slate-600"
        >
            No sports yet. Add one to get started.
        </p>
        <ul
            v-else
            class="space-y-4"
        >
            <li
                v-for="sport in sports"
                :key="sport.id"
                class="rounded-lg border border-slate-200 p-5"
            >
                <h2 class="text-xl font-semibold">
                    <Link
                        :href="show.url(sport.slug)"
                        class="underline"
                        >{{ sport.name }}</Link
                    >
                </h2>
                <p class="mt-2 whitespace-pre-wrap text-slate-600">
                    {{ sport.description }}
                </p>
            </li>
        </ul>
    </main>
</template>
