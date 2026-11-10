<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{ href: string; name: string; description: string }>();
</script>

<template>
    <li class="rounded-lg border border-slate-200 p-5">
        <h2 class="text-xl font-semibold">
            <Link
                :href="href"
                class="underline"
                >{{ name }}</Link
            >
        </h2>
        <p class="mt-2 whitespace-pre-wrap text-slate-600">{{ description }}</p>
        <slot />
    </li>
</template>
