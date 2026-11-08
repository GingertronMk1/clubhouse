<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import * as clubs from '@/routes/clubs';
import * as locations from '@/routes/locations';

defineProps<{
    records: { id: string; name: string; description: string }[];
}>();
const routes = clubs;
const title = 'Clubs';
</script>

<template>
    <div
        class="min-h-screen bg-stone-50 text-stone-900 dark:bg-stone-950 dark:text-stone-100"
    >
        <Head :title="title" />
        <main class="mx-auto max-w-4xl px-6 py-12">
            <nav class="mb-10 flex gap-6 text-sm font-medium">
                <Link :href="clubs.index()">Clubs</Link
                ><Link :href="locations.index()">Locations</Link>
            </nav>
            <div class="mb-8 flex items-center justify-between gap-4">
                <h1 class="text-3xl font-semibold">{{ title }}</h1>
                <Link
                    :href="routes.create()"
                    class="rounded bg-stone-900 px-4 py-2 text-sm font-medium text-white dark:bg-stone-100 dark:text-stone-900"
                    >Add club</Link
                >
            </div>
            <p
                v-if="records.length === 0"
                class="rounded-lg border border-stone-200 bg-white p-6 text-stone-600 dark:border-stone-800 dark:bg-stone-900 dark:text-stone-300"
            >
                No clubs yet.
            </p>
            <ul
                v-else
                class="divide-y divide-stone-200 overflow-hidden rounded-lg border border-stone-200 bg-white dark:divide-stone-800 dark:border-stone-800 dark:bg-stone-900"
            >
                <li v-for="record in records" :key="record.id">
                    <Link
                        :href="routes.show(record.id)"
                        class="block p-5 hover:bg-stone-50 dark:hover:bg-stone-800"
                        ><h2 class="font-semibold">{{ record.name }}</h2>
                        <p
                            class="mt-1 text-sm text-stone-600 dark:text-stone-400"
                        >
                            {{ record.description }}
                        </p></Link
                    >
                </li>
            </ul>
        </main>
    </div>
</template>
