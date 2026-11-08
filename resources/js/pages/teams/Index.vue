<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link } from '@inertiajs/vue3';
import { show as clubShow } from '@/routes/clubs';
import { create, show } from '@/routes/clubs/teams';

defineProps<{
    club: { id: string; name: string };
    teams: { id: string; name: string; description: string }[];
}>();
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="`${club.name} teams`" />
        <Link :href="clubShow.url(club.id)" class="text-sm underline"
            >Back to {{ club.name }}</Link
        >
        <div class="mt-5 mb-8 flex items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ club.name }} teams</h1>
            <Link
                :href="create.url(club.id)"
                class="rounded bg-slate-900 px-4 py-2 text-white"
                >Add team</Link
            >
        </div>
        <p v-if="teams.length === 0" class="text-slate-600">
            No teams yet. Add one to get started.
        </p>
        <ul v-else class="space-y-4">
            <li
                v-for="team in teams"
                :key="team.id"
                class="rounded-lg border border-slate-200 p-5"
            >
                <h2 class="text-xl font-semibold">
                    <Link
                        :href="show.url([club.id, team.id])"
                        class="underline"
                        >{{ team.name }}</Link
                    >
                </h2>
                <p class="mt-2 whitespace-pre-wrap text-slate-600">
                    {{ team.description }}
                </p>
            </li>
        </ul>
    </main>
</template>
