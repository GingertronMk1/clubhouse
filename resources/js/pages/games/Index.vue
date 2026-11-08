<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import * as routes from '@/routes/games';
defineOptions({ layout: AppLayout });
type Named = { name: string };
defineProps<{
    records: {
        id: string;
        name: string;
        start: string | null;
        team1: Named | null;
        team2: Named | null;
        competition: Named | null;
        location: Named | null;
    }[];
}>();
function formatDate(value: string | null) {
    return value ? new Date(value).toLocaleString() : 'Kickoff to be announced';
}
</script>
<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head title="Games" />
        <div class="mb-8 flex items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">Games</h1>
            <Link
                :href="routes.create()"
                class="rounded bg-slate-900 px-4 py-2 text-white"
                >Add game</Link
            >
        </div>
        <p v-if="!records.length" class="text-slate-600">
            No games yet. Add one to get started.
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
                <p
                    v-if="record.team1 && record.team2"
                    class="mt-2 text-slate-700"
                >
                    {{ record.team1.name }} vs {{ record.team2.name }}
                </p>
                <p class="mt-2 text-sm text-slate-600">
                    {{ formatDate(record.start)
                    }}<span v-if="record.location">
                        · {{ record.location.name }}</span
                    ><span v-if="record.competition">
                        · {{ record.competition.name }}</span
                    >
                </p>
            </li>
        </ul>
    </main>
</template>
