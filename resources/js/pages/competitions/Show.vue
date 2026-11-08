<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import * as routes from '@/routes/competitions';
import * as games from '@/routes/games';
defineOptions({ layout: AppLayout });
type Named = { id: string; name: string };
const props = defineProps<{
    record: Named & {
        description: string;
        location: Named | null;
        parent: Named | null;
        children: Named[];
        games: (Named & { club1: Named | null; club2: Named | null })[];
    };
}>();
function remove() {
    if (window.confirm(`Delete ${props.record.name}?`))
        router.delete(routes.destroy.url(props.record.id));
}
</script>
<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="record.name" /><Link
            :href="routes.index()"
            class="text-sm underline"
            >Back to competitions</Link
        >
        <div class="mt-5 flex items-start justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ record.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link :href="routes.edit(record.id)" class="underline"
                    >Edit</Link
                ><button
                    type="button"
                    class="text-red-700 underline"
                    @click="remove"
                >
                    Delete
                </button>
            </div>
        </div>
        <p class="mt-5 whitespace-pre-wrap text-slate-600">
            {{ record.description }}
        </p>
        <p v-if="record.location" class="mt-4 text-slate-600">
            Location: {{ record.location.name }}
        </p>
        <p v-if="record.parent" class="mt-2 text-slate-600">
            Parent:
            <Link :href="routes.show(record.parent.id)" class="underline">{{
                record.parent.name
            }}</Link>
        </p>
        <section class="mt-8">
            <h2 class="text-xl font-semibold">Sub-competitions</h2>
            <p v-if="!record.children.length" class="mt-2 text-slate-600">
                No sub-competitions yet.
            </p>
            <ul v-else class="mt-2 space-y-2">
                <li v-for="child in record.children" :key="child.id">
                    <Link :href="routes.show(child.id)" class="underline">{{
                        child.name
                    }}</Link>
                </li>
            </ul>
        </section>
        <section class="mt-8">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold">Games</h2>
                <Link :href="games.create()" class="text-sm underline"
                    >Add game</Link
                >
            </div>
            <p v-if="!record.games.length" class="mt-2 text-slate-600">
                No games yet.
            </p>
            <ul v-else class="mt-2 space-y-2">
                <li v-for="game in record.games" :key="game.id">
                    <Link :href="games.show(game.id)" class="underline">{{
                        game.name
                    }}</Link
                    ><span
                        v-if="game.club1 && game.club2"
                        class="text-slate-600"
                    >
                        — {{ game.club1.name }} vs {{ game.club2.name }}</span
                    >
                </li>
            </ul>
        </section>
    </main>
</template>
