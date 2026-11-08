<script setup lang="ts">
import NameDescriptionFields from '@/components/NameDescriptionFields.vue';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as clubs from '@/routes/clubs';

const props = defineProps<{
    record?: {
        id: string;
        slug: string;
        name: string;
        description: string;
    };
}>();
const routes = clubs;
const label = 'club';
const form = useForm({
    name: props.record?.name ?? '',
    description: props.record?.description ?? '',
});
function submit() {
    if (props.record) form.put(routes.update.url(props.record.slug));
    else form.post(routes.store.url());
}
</script>

<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="`${record ? 'Edit' : 'Add'} ${label}`" />
        <Link
            :href="record ? routes.show(record.slug) : routes.index()"
            class="text-sm underline"
            >Back to {{ record ? label : 'clubs' }}</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ record ? 'Edit' : 'Add' }} {{ label }}
        </h1>
        <form class="mt-8 space-y-6" @submit.prevent="submit">
            <NameDescriptionFields
                v-model:name="form.name"
                v-model:description="form.description"
                :errors="form.errors"
            />
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded bg-slate-900 px-4 py-2 text-white disabled:opacity-50"
            >
                {{ form.processing ? 'Saving…' : `Save ${label}` }}
            </button>
        </form>
    </main>
</template>
