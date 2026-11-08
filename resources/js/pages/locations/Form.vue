<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
import { Head, Link, useForm } from '@inertiajs/vue3';
import * as locations from '@/routes/locations';

const props = defineProps<{
    record?: {
        id: string;
        name: string;
        description: string;
        additional_information?: unknown;
    };
}>();
const routes = locations;
const label = 'location';
const form = useForm({
    name: props.record?.name ?? '',
    description: props.record?.description ?? '',
    additional_information: JSON.stringify(
        props.record?.additional_information ?? {},
        null,
        2,
    ),
});
function submit() {
    if (props.record) form.put(routes.update.url(props.record.id));
    else form.post(routes.store.url());
}
</script>

<template>
    <main class="mx-auto max-w-2xl px-6 py-12 text-slate-900">
        <Head :title="`${record ? 'Edit' : 'Add'} ${label}`" />
        <Link
            :href="record ? routes.show(record.id) : routes.index()"
            class="text-sm underline"
            >Back to {{ record ? label : 'locations' }}</Link
        >
        <h1 class="mt-5 text-3xl font-semibold">
            {{ record ? 'Edit' : 'Add' }} {{ label }}
        </h1>
        <form class="mt-8 space-y-6" @submit.prevent="submit">
            <div>
                <label for="name" class="block font-medium">Name</label>
                <input
                    id="name"
                    v-model="form.name"
                    required
                    maxlength="255"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.name"
                    class="mt-1 text-sm text-red-700"
                    role="alert"
                >
                    {{ form.errors.name }}
                </p>
            </div>
            <div>
                <label for="description" class="block font-medium"
                    >Description</label
                >
                <textarea
                    id="description"
                    v-model="form.description"
                    required
                    rows="4"
                    class="mt-1 w-full rounded border border-slate-300 p-2"
                />
                <p
                    v-if="form.errors.description"
                    class="mt-1 text-sm text-red-700"
                    role="alert"
                >
                    {{ form.errors.description }}
                </p>
            </div>
            <div>
                <label for="additional_information" class="block font-medium"
                    >Additional information (JSON)</label
                >
                <textarea
                    id="additional_information"
                    v-model="form.additional_information"
                    required
                    rows="6"
                    spellcheck="false"
                    class="mt-1 w-full rounded border border-slate-300 p-2 font-mono text-sm"
                />
                <p
                    v-if="form.errors.additional_information"
                    class="mt-1 text-sm text-red-700"
                    role="alert"
                >
                    {{ form.errors.additional_information }}
                </p>
            </div>
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
