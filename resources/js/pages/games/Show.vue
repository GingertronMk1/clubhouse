<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import * as routes from '@/routes/games';
import * as competitions from '@/routes/competitions';
defineOptions({ layout: AppLayout });
type Named = { id: string; name: string };
const props = defineProps<{
    record: Named & {
        start: string;
        description: string;
        summary: string;
        score: unknown;
        sport: Named | null;
        competition: Named | null;
        club1: (Named & { club: Named | null }) | null;
        club2: (Named & { club: Named | null }) | null;
        location: Named | null;
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
            >Back to games</Link
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
        <p v-if="record.club1 && record.club2" class="mt-5 text-xl font-medium">
            {{ record.club1.club?.name }} {{ record.club1.name }} vs
            {{ record.club2.club?.name }} {{ record.club2.name }}
        </p>
        <p class="mt-3 text-slate-600">
            {{ new Date(record.start).toLocaleString()
            }}<span v-if="record.location"> · {{ record.location.name }}</span
            ><span v-if="record.sport"> · {{ record.sport.name }}</span>
        </p>
        <p v-if="record.competition" class="mt-2 text-slate-600">
            Competition:
            <Link
                :href="competitions.show(record.competition.id)"
                class="underline"
                >{{ record.competition.name }}</Link
            >
        </p>
        <section class="mt-6">
            <h2 class="text-xl font-semibold">Description</h2>
            <p class="mt-2 whitespace-pre-wrap text-slate-600">
                {{ record.description }}
            </p>
        </section>
        <section class="mt-6">
            <h2 class="text-xl font-semibold">Summary</h2>
            <p class="mt-2 whitespace-pre-wrap text-slate-600">
                {{ record.summary }}
            </p>
        </section>
        <section class="mt-6">
            <h2 class="text-xl font-semibold">Score</h2>
            <pre
                class="mt-2 overflow-auto rounded border border-slate-200 p-4 text-sm text-slate-700"
                >{{ JSON.stringify(record.score, null, 2) }}</pre>
        </section>
    </main>
</template>
