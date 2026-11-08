<script setup lang="ts">
const name = defineModel<string>('name', { required: true });
const description = defineModel<string>('description', { required: true });

defineProps<{
    errors: { name?: string; description?: string };
}>();
</script>

<template>
    <div class="space-y-6">
        <div>
            <label for="name" class="block font-medium">Name</label>
            <input
                id="name"
                v-model="name"
                required
                maxlength="255"
                class="mt-1 w-full rounded border border-slate-300 p-2"
            />
            <p
                v-if="errors.name"
                class="mt-1 text-sm text-red-700"
                role="alert"
            >
                {{ errors.name }}
            </p>
        </div>
        <div>
            <label for="description" class="block font-medium"
                >Description</label
            >
            <textarea
                id="description"
                v-model="description"
                required
                rows="4"
                class="mt-1 w-full rounded border border-slate-300 p-2"
            />
            <p
                v-if="errors.description"
                class="mt-1 text-sm text-red-700"
                role="alert"
            >
                {{ errors.description }}
            </p>
        </div>
    </div>
</template>
