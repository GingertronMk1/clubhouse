<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import * as routes from '@/routes/competitions';
defineOptions({ layout: AppLayout });
defineProps<{
    records: {
        id: string;
        name: string;
        description: string;
        location: { name: string } | null;
    }[];
}>();
</script>
<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head title="Competitions" />
        <div class="mb-8 flex items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">Competitions</h1>
            <Link
                :href="routes.create()"
                class="rounded bg-slate-900 px-4 py-2 text-white"
                >Add competition</Link
            >
        </div>
        <p v-if="!records.length" class="text-slate-600">
            No competitions yet. Add one to get started.
        </p>
        <ul v-else class="space-y-4">
            <li
                v-for="record in records"
                :key="record.id"
                class="rounded-lg border border-slate-200 p-5"
            >
                <h2 class="text-xl font-semibold">
                    <Link :href="routes.show(record.id)" class="underline">{{
                        record.name
                    }}</Link>
                </h2>
                <p class="mt-2 whitespace-pre-wrap text-slate-600">
                    {{ record.description }}
                </p>
                <p v-if="record.location" class="mt-2 text-sm text-slate-500">
                    {{ record.location.name }}
                </p>
            </li>
        </ul>
    </main>
</template>
