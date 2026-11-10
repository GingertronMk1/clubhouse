<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { confirmDelete } from '@/lib/confirmDelete';

defineOptions({ layout: AppLayout });
import { Head, Link } from '@inertiajs/vue3';
import * as locations from '@/routes/locations';

const props = defineProps<{
    record: {
        id: string;
        name: string;
        description: string;
        additional_information?: unknown;
    };
}>();
const routes = locations;
function remove() {
    confirmDelete(
        routes.destroy.url(props.record.id),
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
            >Back to locations</Link
        >
        <div class="mt-5 flex items-start justify-between gap-4">
            <h1 class="text-3xl font-semibold">{{ record.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link
                    :href="routes.edit(record.id)"
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
        <p class="mt-5 whitespace-pre-wrap text-slate-600">
            {{ record.description }}
        </p>
        <section class="mt-6">
            <h2 class="text-xl font-semibold">Additional information</h2>
            <pre
                class="mt-2 overflow-auto rounded border border-slate-200 p-4 text-sm text-slate-700"
                >{{
                    JSON.stringify(record.additional_information, null, 2)
                }}</pre>
        </section>
    </main>
</template>
