<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import * as clubs from '@/routes/clubs';
import * as locations from '@/routes/locations';

const props = defineProps<{
    kind: 'clubs' | 'locations';
    record: {
        id: string;
        name: string;
        description: string;
        additional_information?: unknown;
    };
}>();
const routes = props.kind === 'clubs' ? clubs : locations;
function remove() {
    if (window.confirm(`Delete ${props.record.name}?`))
        router.delete(routes.destroy.url(props.record.id));
}
</script>

<template>
    <div
        class="min-h-screen bg-stone-50 text-stone-900 dark:bg-stone-950 dark:text-stone-100"
    >
        <Head :title="record.name" />
        <main class="mx-auto max-w-2xl px-6 py-12">
            <Link :href="routes.index()" class="text-sm underline"
                >Back to {{ kind }}</Link
            >
            <h1 class="mt-6 text-3xl font-semibold">{{ record.name }}</h1>
            <p
                class="mt-4 whitespace-pre-wrap text-stone-700 dark:text-stone-300"
            >
                {{ record.description }}
            </p>
            <section v-if="kind === 'locations'" class="mt-8">
                <h2 class="font-semibold">Additional information</h2>
                <pre
                    class="mt-2 overflow-auto rounded bg-stone-100 p-4 text-sm dark:bg-stone-900"
                    >{{
                        JSON.stringify(record.additional_information, null, 2)
                    }}</pre>
            </section>
            <div class="mt-8 flex gap-4">
                <Link
                    :href="routes.edit(record.id)"
                    class="rounded bg-stone-900 px-4 py-2 text-white dark:bg-stone-100 dark:text-stone-900"
                    >Edit</Link
                ><button
                    type="button"
                    class="rounded border border-red-600 px-4 py-2 text-red-700 dark:text-red-400"
                    @click="remove"
                >
                    Delete
                </button>
            </div>
        </main>
    </div>
</template>
