<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link } from '@inertiajs/vue3';
import * as locations from '@/routes/locations';

defineProps<{
    records: { id: string; name: string; description: string }[];
}>();
const routes = locations;
const title = 'Locations';
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="title" />
        <div class="mb-8 flex items-center justify-between">
            <h1 class="text-3xl font-semibold">{{ title }}</h1>
            <Link
                :href="routes.create()"
                class="rounded bg-slate-900 px-4 py-2 text-white"
                >Add location</Link
            >
        </div>
        <p
            v-if="records.length === 0"
            class="text-slate-600"
        >
            No locations yet. Add one to get started.
        </p>
        <ul
            v-else
            class="space-y-4"
        >
            <li
                v-for="record in records"
                :key="record.id"
                class="rounded-lg border border-slate-200 p-5"
            >
                <h2 class="text-xl font-semibold">
                    <Link
                        :href="routes.show(record.id)"
                        class="underline"
                        >{{ record.name }}</Link
                    >
                </h2>
                <p class="mt-2 whitespace-pre-wrap text-slate-600">
                    {{ record.description }}
                </p>
            </li>
        </ul>
    </main>
</template>
