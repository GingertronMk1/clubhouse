<script setup lang="ts">
defineProps<{
    name: string;
    value: string;
    required?: boolean;
}>();

const model = defineModel<string>({ required: true });
</script>

<template>
    <label class="cursor-pointer">
        <input
            v-model="model"
            type="radio"
            :name="name"
            :value="value"
            :required="required"
            class="peer sr-only"
        />
        <span
            class="inline-flex min-h-10 items-center rounded border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-800 transition peer-checked:border-club-orange peer-checked:bg-club-orange peer-checked:text-white peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-club-orange hover:border-club-orange"
        >
            <slot />
        </span>
    </label>
</template>
