<script setup lang="ts">
withDefaults(
    defineProps<{
        textarea?: boolean;
        type?: string;
    }>(),
    {
        textarea: false,
        type: 'text',
    },
);

const model = defineModel<string | number>({ required: true });
</script>

<template>
    <textarea
        v-if="textarea"
        v-model="model"
        class="mt-1 w-full rounded border border-slate-300 p-2"
    />
    <input
        v-else
        v-model="model"
        :type="type"
        :class="
            type === 'range'
                ? 'mt-2 w-full accent-emerald-700'
                : 'mt-1 w-full rounded border border-slate-300 p-2'
        "
    />
</template>
