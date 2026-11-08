<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { destroy, edit, index } from '@/routes/sports';
import { index as positionsIndex } from '@/routes/sports/positions';

defineProps<{
    sport: {
        id: string;
        name: string;
        description: string;
        scoring: { name: string; value: number }[];
    };
}>();
function deleteSport(id: string) {
    if (window.confirm('Delete this sport?')) router.delete(destroy.url(id));
}
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="sport.name" />
        <Link :href="index.url()" class="text-sm underline"
            >Back to sports</Link
        >
        <div class="mt-5 flex items-start justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ sport.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link :href="edit.url(sport.id)" class="underline">Edit</Link>
                <button
                    type="button"
                    class="text-red-700 underline"
                    @click="deleteSport(sport.id)"
                >
                    Delete
                </button>
            </div>
        </div>
        <p class="mt-5 whitespace-pre-wrap text-slate-600">
            {{ sport.description }}
        </p>
        <section v-if="sport.scoring.length" class="mt-6">
            <h2 class="text-xl font-semibold">Scoring</h2>
            <ul class="mt-2 text-slate-700">
                <li v-for="(score, i) in sport.scoring" :key="i">
                    {{ score.name }}: {{ score.value }}
                </li>
            </ul>
        </section>
        <Link
            :href="positionsIndex.url(sport.id)"
            class="mt-6 inline-block underline"
            >View positions</Link
        >
    </main>
</template>
