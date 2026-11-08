<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link } from '@inertiajs/vue3';
import { show as sportShow } from '@/routes/sports';
import { create, show } from '@/routes/sports/positions';

defineProps<{
    sport: { id: string; slug: string; name: string };
    positions: { id: string; name: string; description: string }[];
}>();
</script>

<template>
    <main class="mx-auto max-w-4xl px-6 py-12 text-slate-900">
        <Head :title="`${sport.name} positions`" />
        <Link :href="sportShow.url(sport.slug)" class="text-sm underline"
            >Back to {{ sport.name }}</Link
        >
        <div class="mt-5 mb-8 flex items-center justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ sport.name }} positions</h1>
            <Link
                :href="create.url(sport.slug)"
                class="rounded bg-slate-900 px-4 py-2 text-white"
                >Add position</Link
            >
        </div>
        <p v-if="positions.length === 0" class="text-slate-600">
            No positions yet. Add one to get started.
        </p>
        <ul v-else class="space-y-4">
            <li
                v-for="position in positions"
                :key="position.id"
                class="rounded-lg border border-slate-200 p-5"
            >
                <h2 class="text-xl font-semibold">
                    <Link
                        :href="show.url([sport.slug, position.id])"
                        class="underline"
                        >{{ position.name }}</Link
                    >
                </h2>
                <p class="mt-2 whitespace-pre-wrap text-slate-600">
                    {{ position.description }}
                </p>
            </li>
        </ul>
    </main>
</template>
