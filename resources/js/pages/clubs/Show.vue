<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDelete } from '@/lib/confirmDelete';
import { index as gamesIndex } from '@/routes/games';

defineOptions({ layout: AppLayout });
import { Head, Link } from '@inertiajs/vue3';
import * as clubs from '@/routes/clubs';
import { index as teamsIndex, show as teamShow } from '@/routes/clubs/teams';

const props = defineProps<{
    teams: { id: string; slug: string; name: string }[];
    record: {
        id: string;
        slug: string;
        name: string;
        description: string;
    };
}>();
const routes = clubs;
function remove() {
    confirmDelete(
        routes.destroy.url(props.record.slug),
        `Delete ${props.record.name}?`,
    );
}
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="record.name" />
        <Link
            :href="routes.index()"
            class="text-sm underline"
            >Back to clubs</Link
        >
        <div class="mt-5 flex items-start justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ record.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link
                    :href="routes.edit(record.slug)"
                    class="underline"
                    >Edit</Link
                >
                <button
                    type="button"
                    class="text-red-700 underline"
                    @click="remove"
                >
                    Delete
                </button>
            </div>
        </div>
        <Link
            :href="gamesIndex({ query: { club: record.id } })"
            class="mt-5 inline-block text-sm underline"
            >View games</Link
        >
        <p class="mt-5 whitespace-pre-wrap text-slate-600">
            {{ record.description }}
        </p>
        <section
            class="mt-8"
            aria-labelledby="teams-heading"
        >
            <div class="flex items-center justify-between gap-4">
                <h2
                    id="teams-heading"
                    class="text-xl font-semibold"
                >
                    Teams
                </h2>
                <Link
                    :href="teamsIndex.url(record.slug)"
                    class="text-sm underline"
                    >Manage teams</Link
                >
            </div>
            <p
                v-if="teams.length === 0"
                class="mt-3 text-slate-600"
            >
                No teams yet.
            </p>
            <ul
                v-else
                class="mt-4 space-y-2"
            >
                <li
                    v-for="team in teams"
                    :key="team.id"
                >
                    <Link
                        :href="teamShow.url([record.slug, team.slug])"
                        class="underline"
                        >{{ team.name }}</Link
                    >
                </li>
            </ul>
        </section>
    </main>
</template>
