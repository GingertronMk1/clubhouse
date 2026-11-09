<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

defineOptions({ layout: AppLayout });
defineProps<{ game: { id: string; name: string } }>();
</script>

<template>
    <main>
        <Head title="Live Game" />
        <h1>Live Game</h1>
        <p>{{ game.name }}</p>
    </main>
</template>
